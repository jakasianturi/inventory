<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PendaftaranController extends Controller
{
    /**
     * PendaftaranController constructor.
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
    public function index(Request $request)
    {
        $user_id = auth()->user()->id;

        $user_registration = Pendaftaran::with([
                'konfirmasiPendaftaran',
                'pembayaran',
                'user'
            ])
            ->where('user_id', $user_id)
            ->latest()
            ->first();

        // Cek status konfirmasi
        $status_konfirmasi_formulir_pendaftaran = null;
        $catatan_konfirmasi_formulir_pendaftaran = null;
        if ($user_registration && $user_registration->konfirmasiPendaftaran) {
            $status_konfirmasi_formulir_pendaftaran = $user_registration->konfirmasiPendaftaran->status;
            $catatan_konfirmasi_formulir_pendaftaran = $user_registration->konfirmasiPendaftaran->catatan;
        }
        
        return view('dashboard.pendaftaran.index', compact('user_registration', 'status_konfirmasi_formulir_pendaftaran', 'catatan_konfirmasi_formulir_pendaftaran'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_id = auth()->user()->id;
        $user_registeration = Pendaftaran::whereIn('user_id', [$user_id])->get()->toArray();
        // dd($user_registeration);
        if(!empty($user_registeration)) {
            return redirect()
                    ->route('dashboard.pendaftarans.index')
                    ->with('message_warning', __('Peringatan, anda sudah melakukan pendaftaran.'));
        } else {
            return view('dashboard.pendaftaran.form');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'nama_lengkap'     => 'required',
                'tempat_lahir'     => 'required',
                'tanggal_lahir'     => 'required|date',
                'jenis_kelamin'     => 'required',
                'agama'     => 'required',
                'asal_sekolah'     => 'required',
                'telepon'     => 'required',
                'email'     => 'required',
                'alamat'     => 'required',
                'nama_ayah'     => 'required',
                'pekerjaan_ayah'     => 'required',
                'nama_ibu'     => 'required',
                'pekerjaan_ibu'     => 'required',
                'wali'     => 'required',
                'surat_keterangan_lulus'     => 'required',
                'kartu_keluarga'     => 'required',
                'pas_photo'     => 'required',
                'akta_kelahiran'     => 'required',
            ],
            [
                'nama.required'         => 'Nama Lengkap tidak boleh kosong.',
                'tempat_lahir.required'    => 'Tempat Lahir tidak boleh kosong.',
                'tanggal_lahir.required'    => 'Tanggal Lahir tidak boleh kosong.',
                'jenis_kelamin.required'    => 'Jenis Kelamin tidak boleh kosong.',
                'agama.required'    => 'Agama tidak boleh kosong.',
                'asal_sekolah.required'    => 'Asal Sekolah tidak boleh kosong.',
                'telepon.required'    => 'Telepon tidak boleh kosong.',
                'email.required'        => 'Alamat Email tidak boleh kosong.',
                'alamat.required'    => 'Alamat tidak boleh kosong.',
                'nama_ayah.required'    => 'Nama tidak boleh kosong.',
                'pekerjaan_ayah.required'    => 'Pekerjaan tidak boleh kosong.',
                'nama_ibu.required'    => 'Nama tidak boleh kosong.',
                'pekerjaan_ibu.required'    => 'Pekerjaan tidak boleh kosong.',
                'wali.required'    => 'Wali tidak boleh kosong.',
                'surat_keterangan_lulus.required' => 'Ijazah / Surat Keterangan Lulus tidak boleh kosong.',
                'kartu_keluarga.required' => 'Kartu Keluarga tidak boleh kosong.',
                'pas_photo.required' => 'Pas Photo tidak boleh kosong.',
                'akta_kelahiran.required' => 'Akta Kelahiran tidak boleh kosong.',
            ],
        );

        if($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Silakan lengkapi data sesuai dengan ketentuan.');
        } else {

            $nomor_pendaftaran = 'SPMB-'.Carbon::now()->translatedFormat('Y') . str_pad(Pendaftaran::count() + 1, 4, '0', STR_PAD_LEFT);

            // SKL
            if(!empty($request->file('surat_keterangan_lulus'))) {
            $file = $request->file('surat_keterangan_lulus');
            $fileNameSKL = time() . '-skl-' .$nomor_pendaftaran. '.' . $file->getClientOriginalExtension();
            Storage::putFileAs('public/uploads', $file, $fileNameSKL);
            } else {
                $fileNameSKL = "";
            }

            // KK
            if(!empty($request->file('kartu_keluarga'))) {
                $file = $request->file('kartu_keluarga');
                $fileNameKK = time() . '-kk-' .$nomor_pendaftaran. '.' . $file->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $file, $fileNameKK);
            } else {
                $fileNameKK = "";
            }

            // Pas Photo
            if(!empty($request->file('pas_photo'))) {
                $file = $request->file('pas_photo');
                $fileNamePasPhoto = time() . '-pas_photo-' .$nomor_pendaftaran. '.' . $file->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $file, $fileNamePasPhoto);
            } else {
                $fileNamePasPhoto = "";
            }

            // Akta Kelahiran
            if(!empty($request->file('akta_kelahiran'))) {
                $file = $request->file('akta_kelahiran');
                $fileNameAktaKelahiran = time() . '-akta_kelahiran-' .$nomor_pendaftaran. '.' . $file->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $file, $fileNameAktaKelahiran);
            } else {
                $fileNameAktaKelahiran = "";
            }
            
            $user = auth()->user();
            $pendaftaran = Pendaftaran::create([
                'user_id'       => $user->id,
                'nomor_pendaftaran'       => $nomor_pendaftaran,
                'nama_lengkap'     => $request->input('nama_lengkap'),
                'jenis_kelamin'     => $request->input('jenis_kelamin'),
                'tempat_lahir'     => $request->input('tempat_lahir'),
                'tanggal_lahir'     => $request->input('tanggal_lahir'),
                'agama'     => $request->input('agama'),
                'asal_sekolah'     => $request->input('asal_sekolah'),
                'telepon'     => $request->input('telepon'),
                'email'     => $request->input('email'),
                'alamat'     => $request->input('alamat'),
                'nama_ayah'     => $request->input('nama_ayah'),
                'pekerjaan_ayah'     => $request->input('pekerjaan_ayah'),
                'nama_ibu'     => $request->input('nama_ibu'),
                'pekerjaan_ibu'     => $request->input('pekerjaan_ibu'),
                'wali'     => $request->input('wali'),
                'surat_keterangan_lulus'       => $fileNameSKL,
                'kartu_keluarga'       => $fileNameKK,
                'pas_photo'       => $fileNamePasPhoto,
                'akta_kelahiran'       => $fileNameAktaKelahiran,
            ]);
            
            // Create Konfirmasi
            $pendaftaran->konfirmasiPendaftaran()->create([
                'tipe' => 'pendaftaran',
                'status' => 'Menunggu Konfirmasi',
                'catatan' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return redirect()
                ->route('dashboard.pendaftarans.index')
                ->with('message', __('messages.create_pendaftaran'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function show(Pendaftaran $pendaftaran)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function edit(Pendaftaran $pendaftaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama_lengkap'     => 'required',
                'tempat_lahir'     => 'required',
                'tanggal_lahir'     => 'required|date',
                'jenis_kelamin'     => 'required',
                'agama'     => 'required',
                'asal_sekolah'     => 'required',
                'telepon'     => 'required',
                'email'     => 'required',
                'alamat'     => 'required',
                'nama_ayah'     => 'required',
                'pekerjaan_ayah'     => 'required',
                'nama_ibu'     => 'required',
                'pekerjaan_ibu'     => 'required',
                'wali'     => 'required',
                'surat_keterangan_lulus'     => 'nullable',
                'kartu_keluarga'     => 'nullable',
                'pas_photo'     => 'nullable',
                'akta_kelahiran'     => 'nullable',
            ],
            [
                'nama.required'         => 'Nama Lengkap tidak boleh kosong.',
                'tempat_lahir.required'    => 'Tempat Lahir tidak boleh kosong.',
                'tanggal_lahir.required'    => 'Tanggal Lahir tidak boleh kosong.',
                'jenis_kelamin.required'    => 'Jenis Kelamin tidak boleh kosong.',
                'agama.required'    => 'Agama tidak boleh kosong.',
                'asal_sekolah.required'    => 'Asal Sekolah tidak boleh kosong.',
                'telepon.required'    => 'Telepon tidak boleh kosong.',
                'email.required'        => 'Alamat Email tidak boleh kosong.',
                'alamat.required'    => 'Alamat tidak boleh kosong.',
                'nama_ayah.required'    => 'Nama tidak boleh kosong.',
                'pekerjaan_ayah.required'    => 'Pekerjaan tidak boleh kosong.',
                'nama_ibu.required'    => 'Nama tidak boleh kosong.',
                'pekerjaan_ibu.required'    => 'Pekerjaan tidak boleh kosong.',
                'wali.required'    => 'Wali tidak boleh kosong.',
            ],
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Silakan lengkapi data sesuai dengan ketentuan.');
        } else {
            $nomor_pendaftaran = $pendaftaran->nomor_pendaftaran;

            // SKL
            if (!empty($request->file('surat_keterangan_lulus'))) {
                $file = $request->file('surat_keterangan_lulus');
                $fileNameSKL = time() . '-skl-' . $nomor_pendaftaran . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $file, $fileNameSKL);
            } else {
                $fileNameSKL = $pendaftaran->surat_keterangan_lulus;
            }

            // KK
            if (!empty($request->file('kartu_keluarga'))) {
                $file = $request->file('kartu_keluarga');
                $fileNameKK = time() . '-kk-' . $nomor_pendaftaran . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $file, $fileNameKK);
            } else {
                $fileNameKK = $pendaftaran->kartu_keluarga;
            }

            // Pas Photo
            if (!empty($request->file('pas_photo'))) {
                $file = $request->file('pas_photo');
                $fileNamePasPhoto = time() . '-pas_photo-' . $nomor_pendaftaran . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $file, $fileNamePasPhoto);
            } else {
                $fileNamePasPhoto = $pendaftaran->pas_photo;
            }

            // Akta Kelahiran
            if (!empty($request->file('akta_kelahiran'))) {
                $file = $request->file('akta_kelahiran');
                $fileNameAktaKelahiran = time() . '-akta_kelahiran-' . $nomor_pendaftaran . '.' . $file->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $file, $fileNameAktaKelahiran);
            } else {
                $fileNameAktaKelahiran = $pendaftaran->akta_kelahiran;
            }

            Pendaftaran::where('id', $pendaftaran->id)
                ->update([
                    'nama_lengkap'     => $request->input('nama_lengkap'),
                    'nisn'     => $request->input('nisn'),
                    'tempat_lahir'     => $request->input('tempat_lahir'),
                    'jenis_kelamin'     => $request->input('jenis_kelamin'),
                    'tanggal_lahir'     => $request->input('tanggal_lahir'),
                    'agama'     => $request->input('agama'),
                    'asal_sekolah'     => $request->input('asal_sekolah'),
                    'telepon'     => $request->input('telepon'),
                    'email'     => $request->input('email'),
                    'alamat'     => $request->input('alamat'),
                    'nama_ayah'     => $request->input('nama_ayah'),
                    'pekerjaan_ayah'     => $request->input('pekerjaan_ayah'),
                    'nama_ibu'     => $request->input('nama_ibu'),
                    'pekerjaan_ibu'     => $request->input('pekerjaan_ibu'),
                    'wali'     => $request->input('wali'),
                    'surat_keterangan_lulus'       => $fileNameSKL,
                    'kartu_keluarga'       => $fileNameKK,
                    'pas_photo'       => $fileNamePasPhoto,
                    'akta_kelahiran'       => $fileNameAktaKelahiran,
                ]);
        }

        // Update Konfirmasi
        $pendaftaran->konfirmasiPendaftaran()->update([
            'tipe' => 'pendaftaran',
            'status' => 'Menunggu Konfirmasi',
            'catatan' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('dashboard.pendaftarans.index')
            ->with('message', __('messages.update_pendaftaran'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pendaftaran $pendaftaran)
    {
        //
    }
}