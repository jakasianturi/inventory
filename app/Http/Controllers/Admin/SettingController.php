<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
     /**
     * SettingController constructor.
     *
     * 
     */
    public function __construct()
    {
        
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = Setting::first();

        return view('admin.setting.form', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_situs'      => 'required',
            'logo'            => 'image|nullable',
            'favicon'         => 'image|nullable',
            'auth_background' => 'image|nullable',
            'email'           => 'nullable|email',
            'telepon'         => 'nullable',
            'alamat'          => 'nullable',
            'footer_teks'     => 'nullable',
        ], [
            'nama_situs.required'    => "Nama Situs tidak boleh kosong.",
            'logo.image'             => "Logo harus berupa gambar.",
            'favicon.image'          => "Favicon harus berupa gambar.",
            'auth_background.image'  => "Background Auth harus berupa gambar.",
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        // Get the first setting
        $setting = Setting::first();
        if (!$setting) {
            return redirect()
                    ->back()
                    ->withErrors(['error' => 'Tidak ada pengaturan.'])
                    ->withInput();
        }

        // Memulai Database Transaction
        DB::beginTransaction();

        // Variabel untuk melacak file yang baru diupload (untuk keperluan rollback)
        $uploadedFiles = [];

        try {
            // 1. Tangani Logo
            $logoPath = $setting->logo; // Set default ke logo lama

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $fileNameLogo = time() . '-logo.' . $image->getClientOriginalExtension();

                // Simpan file baru, update $logoPath
                Storage::disk('public')->putFileAs('uploads', $image, $fileNameLogo);
                $logoPath = 'uploads/' . $fileNameLogo;
            }

            // 2. Tangani Favicon
            $faviconPath = $setting->favicon;
            if ($request->hasFile('favicon')) {
                $image = $request->file('favicon');
                $fileNameFavicon = time() . '-favicon.' . $image->getClientOriginalExtension();

                // Simpan file baru, update $faviconPath
                Storage::disk('public')->putFileAs('uploads', $image, $fileNameFavicon);
                $faviconPath = 'uploads/' . $fileNameFavicon;
            }

            // 3. Tangani Auth Background
            $authBackgroundPath = $setting->auth_background;
            if ($request->hasFile('auth_background')) {
                $image = $request->file('auth_background');
                $fileNameAuthBackground = time() . '-auth_background.' . $image->getClientOriginalExtension();

                // Simpan file baru, update $authBackgroundPath
                Storage::disk('public')->putFileAs('uploads', $image, $fileNameAuthBackground);
                $authBackgroundPath = 'uploads/' . $fileNameAuthBackground;
            }

            // 4. Update Setting ke Database
            $setting->update([
                'nama_situs'      => $request->input('nama_situs'),
                'logo'            => $logoPath,
                'favicon'         => $faviconPath,
                'auth_background' => $authBackgroundPath,
                'email'           => $request->input('email'),
                'telepon'         => $request->input('telepon'),
                'alamat'          => $request->input('alamat'),
                'footer_teks'     => $request->input('footer_teks'),
            ]);

            // Jika semua proses di atas berhasil, simpan permanen ke DB
            DB::commit();

            return redirect()
                    ->route('admin.settings.index')
                    ->with('message', __('messages.update_setting'));

        } catch (Exception $e) {
            // Jika ada error, batalkan semua perubahan ke database
            DB::rollBack();

            // Hapus SEMUA file yang baru saja diupload pada request ini
            Storage::delete($logoPath);
            Storage::delete($faviconPath);
            Storage::delete($authBackgroundPath);

            return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat menyimpan pengaturan: ' . $e->getMessage());
        }
    }
}