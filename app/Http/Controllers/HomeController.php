<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Cek apakah user sudah login
        if (Auth::check()) {
            // Ambil role user yang sedang login
            $role = Auth::user()->user_role;

            // Redirect berdasarkan role
            if ($role === 'admin') {
                return redirect('/admin');
            } elseif ($role === 'user') {
                return redirect('/dashboard');
            }
        }

        // Jika tidak ada login (guest), tampilkan halaman utama atau arahkan ke login
        // Pilih salah satu sesuai kebutuhan aplikasi Anda:

        // return view('welcome'); // Opsi 1: Tampilkan landing page
        return redirect()->route('login'); // Opsi 2: Langsung lempar ke halaman login
    }
}