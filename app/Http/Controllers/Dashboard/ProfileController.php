<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * ProfileController constructor.
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
        $user = auth()->user();
        return view('dashboard.profile.form', [
                    'user'    => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'avatar'   => 'nullable|image',
            'email'    => 'nullable|email', // Disarankan menambahkan validasi email
            'password' => 'nullable|min:8|confirmed',
            'phone'  => 'nullable',
            'address'  => 'nullable'
        ], [
            'name.required'      => "Name tidak boleh kosong.",
            'avatar.image'       => "Avatar harus berupa gambar.",
            'password.min'       => 'Panjang password harus lebih dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Memulai Database Transaction
        DB::beginTransaction();

        try {
            // 1. Tangani Avatar
            $avatarPath = $user->avatar; // Set default ke avatar lama

            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $fileNameAvatar = time() . '-avatar.' . $image->getClientOriginalExtension();

                // Simpan file baru, update $avatarPath
                Storage::disk('public')->putFileAs('uploads', $image, $fileNameAvatar);
                $avatarPath = 'uploads/' . $fileNameAvatar;
            }

            // 2. Tangani Password
            if (!empty($request->input('password'))) {
                $password = Hash::make($request->input('password'));
            } else {
                $password = $user->password;
            }

            // 3. Update User (Lebih ringkas menggunakan object model langsung)
            $user->update([
                'name'     => $request->input('name'),
                'avatar'   => $avatarPath, // Menggunakan variabel yang sudah diperbaiki
                'email'    => $request->input('email'),
                'password' => $password,
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
            ]);

            // Jika semua proses di atas berhasil, simpan permanen ke DB
            DB::commit();

            return redirect()
                ->route('dashboard.profiles.index')
                ->with('message', __('messages.update_profile'));
        } catch (Exception $e) {
            // Jika ada error, batalkan semua perubahan ke database
            DB::rollBack();

            // Opsional: Hapus file yang terlanjur terupload jika DB gagal update
            if (isset($fileNameAvatar) && Storage::exists('uploads/' . $fileNameAvatar)) {
                Storage::delete('uploads/' . $fileNameAvatar);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}