<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    /**
     * PembayaranController constructor.
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
                'konfirmasiPembayaran',
                'user',
                'pembayaran',
            ])
            ->where('user_id', $user_id)
            ->latest()
            ->first();

        // Cek status konfirmasi pembayaran
        $status_konfirmasi_pembayaran = null;
        $catatan_konfirmasi_pembayaran = null;
        if ($user_registration && $user_registration->konfirmasiPembayaran) {
            $status_konfirmasi_pembayaran = $user_registration->konfirmasiPembayaran->status;
            $catatan_konfirmasi_pembayaran = $user_registration->konfirmasiPembayaran->catatan;
        }

        // Biaya Pendaftaran
        $detail_pembayaran = Pengaturan::first();
        
        return view('dashboard.pembayaran.index', compact(
            'user_registration',
            'status_konfirmasi_pembayaran',
            'catatan_konfirmasi_pembayaran',
            'detail_pembayaran'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama_pengirim' => 'required',
                'bank' => 'required',
                'tanggal_pembayaran' => 'required|date',
                'bukti_pembayaran' => 'required',
            ],
            [
                'nama_pengirim.required' => 'Nama pengirim wajib diisi.',
                'bank.required' => 'Bank wajib diisi.',
                'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi.',
                'tanggal_pembayaran.date' => 'Tanggal pembayaran harus berupa tanggal yang valid.',
                'bukti_pembayaran.required' => 'Bukti pembayaran wajib diisi.',
            ],
        );
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Silakan lengkapi data sesuai dengan ketentuan.');
        } else {
            // Bukti Pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $image = $request->file('bukti_pembayaran');
                $fileName = time() . '-' . 'bukti_pembayaran' . '.' . $image->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $image, $fileName);
            } else {
                $fileName = null;
            }
            // Simpan data pembayaran
            $pendaftaran = Pendaftaran::where('user_id', auth()->user()->id)
                ->latest()
                ->first();
            Pembayaran::create([
                'user_id' => auth()->user()->id,
                'pendaftaran_id' => $pendaftaran->id,
                'nama_pengirim' => $request->nama_pengirim,
                'bank' => $request->bank,
                'bukti_pembayaran' => $fileName,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
            ]);

            // Simpan data konfirmasi pembayaran
            $pendaftaran->konfirmasiPembayaran()->create([
                'tipe' => 'pembayaran',
                'status' => 'Menunggu Konfirmasi',
                'catatan' => null,
            ]);
        }

        return redirect()
            ->route('dashboard.pembayarans.index')
            ->with('message', __('messages.create_pembayaran'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pembayaran  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function show(Pembayaran $pembayaran)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Seleksi  $seleksi
     * @return \Illuminate\Http\Response
     */
    public function edit(Seleksi $seleksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pembayaran  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pembayaran $pembayaran)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama_pengirim' => 'required',
                'bank' => 'required',
                'tanggal_pembayaran' => 'required|date',
                'bukti_pembayaran' => 'nullable',
            ],
            [
                'nama_pengirim.required' => 'Sumber pengirim wajib diisi.',
                'bank.required' => 'Bank wajib diisi.',
                'tanggal_pembayaran.required' => 'Tanggal pembayaran wajib diisi.',
                'tanggal_pembayaran.date' => 'Tanggal pembayaran harus berupa tanggal yang valid.',
            ],
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Silakan lengkapi data sesuai dengan ketentuan.');
        } else {
            // Bukti Pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $image = $request->file('bukti_pembayaran');
                $fileName = time() . '-' . 'bukti_pembayaran' . '.' . $image->getClientOriginalExtension();
                Storage::putFileAs('public/uploads', $image, $fileName);
            } else {
                $fileName = $pembayaran->bukti_pembayaran;
            }
            // Simpan data pembayaran
            $pendaftaran = Pendaftaran::where('user_id', auth()->user()->id)
                ->latest()
                ->first();
            Pembayaran::where('pendaftaran_id', $pendaftaran->id)
                ->update([
                    'user_id' => auth()->user()->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'bukti_pembayaran' => $fileName,
                    'nama_pengirim' => $request->nama_pengirim,
                    'bank' => $request->bank,
                    'tanggal_pembayaran' => $request->tanggal_pembayaran,
                ]);

            // Simpan data konfirmasi pembayaran
            $pendaftaran->konfirmasiPembayaran()->update([
                'tipe' => 'pembayaran',
                'status' => 'Menunggu Konfirmasi',
                'catatan' => '',
            ]);
        }

        return redirect()
            ->route('dashboard.pembayarans.index')
            ->with('message', __('messages.update_pembayaran'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Seleksi  $seleksi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seleksi $seleksi)
    {
        //
    }

    /**
     * Export Hasil 
     */
    public function exportpdf($pendaftaran_id)
    {
        $date = Carbon::now()->translatedFormat('l, j F Y');
        $result = Pendaftaran::with([
                'user',
                'pembayaran',
                'konfirmasiPembayaran',
            ])
            ->where('id', $pendaftaran_id)
            ->where('user_id', auth()->user()->id)
            ->first();
        if (!$result) {
            return redirect()
                ->route('dashboard.pendaftarans.index')
                ->with('message_warning', __('Pendaftaran tidak ditemukan.'));
        }
        $result->status = $result->statusPendaftaran();
        if (!$result->status) {
            return redirect()
                ->route('dashboard.pendaftarans.index')
                ->with('message_warning', __('Pendaftaran belum diterima.'));
        }
        if (!$result->status) {
            return redirect()
                ->route('dashboard.pendaftarans.index')
                ->with('message_warning', __('Pendaftaran belum diterima.'));
        }
        
        $data = ['result' => $result];
        $htmlExport = view('dashboard.pembayaran.export', compact('result'))->render();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($htmlExport);
        return $pdf->stream('SPMB-' . $date . '-' . $result->user->nama_lengkap . '.pdf');
    }
}