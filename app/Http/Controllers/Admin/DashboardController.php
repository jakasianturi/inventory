<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * DashboardController constructor.
     *
     * 
     */
    public function __construct()
    {
        // $this->middleware(['auth', 'role:admin', 'status:active']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_total = User::where('user_role', 'user')->count();
        return view('admin.dashboard.welcome', compact('user_total'));
    }
}