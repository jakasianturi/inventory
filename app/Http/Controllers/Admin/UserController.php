<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * UserController constructor.
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
        $users = User::whereNotIn('user_role', ['admin'])->get();
       if($request->ajax()){
        return DataTables::of($users)
                        ->addColumn('action', function($data){
                            $button = '<a href="users/'.$data->id.'/edit" class="d-sm-inline-block btn btn-sm btn-success shadow-sm mx-1"><i class="fas fa-edit fa-sm"></i> Edit</a>';
                            $button .= '&nbsp;&nbsp;';
                            $button .= '<button type="button" name="delete" id="'.$data->id.'"class="delete d-sm-inline-block btn btn-sm btn-danger shadow-sm mx-1"><i class="fas fa-trash-alt fa-sm"></i> Hapus</button>'; 
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->addIndexColumn()
                        ->make(true);
        }
        return view('admin.user.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.form', [
            'url'           => 'admin.users.store',
            'button'        => __('Simpan'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'email'      => 'required|email|unique:App\Models\User,email,',
            'gender'     => 'required',
            'user_role'  => 'required',
            'status'     => 'required',
            'password'   => 'required|min:8|confirmed',
        ],
        [
            'name.required'         => 'Nama Lengkap tidak boleh kosong.',
            'email.required'        => 'Alamat Email tidak boleh kosong.',
            'email.email'           => 'Alamat Email tidak valid.',
            'email.unique'          => 'Alamat Email sudah digunakan.',
            'gender.required'       => 'Jenis Kelamin tidak boleh kosong.',
            'user_role.required'    => 'Peran tidak boleh kosong.',
            'status.required'       => 'Status tidak boleh kosong.',
            'password.required'     => 'Password tidak boleh kosong.',
            'password.min'          => 'Panjang password harus lebih dari 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak sesuai.',
        ],
        );

        if($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        } else {
            User::create([
                'name'       => $request->input('name'),
                'email'       => $request->input('email'),
                'gender'       => $request->input('gender'),
                'user_role'       => $request->input('user_role'),
                'status'       => $request->input('status'),
                'password'       => Hash::make($request->input('password')),
            ]);
        }

        return redirect()
                ->route('admin.users.index')
                ->with('message', __('messages.create_siswa', ['attribute' => $request->input('name')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($user)
    {
        $user = User::where('id', $user)->where('user_role', 'user')->firstOrFail();
        return view('admin.user.form', [
            'user'   => $user,
            'url'         => 'admin.users.update',
            'button'      => 'Perbaharui',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'email'      => 'required|email|unique:App\Models\User,email,'.$user->id,
            'gender'     => 'required',
            'user_role'  => 'required',
            'password'   => 'nullable|min:8|confirmed',
        ],
        [
            'name.required'         => 'Nama Lengkap tidak boleh kosong.',
            'email.required'        => 'Alamat Email tidak boleh kosong.',
            'email.email'           => 'Alamat Email tidak valid.',
            'email.unique'          => 'Alamat Email sudah digunakan.',
            'gender.required'       => 'Jenis Kelamin tidak boleh kosong.',
            'user_role.required'    => 'Peran tidak boleh kosong.',
            'password.min'          => 'Panjang password harus lebih dari 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak sesuai.',
        ],
        );

        if($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Silakan lengkapi data sesuai dengan ketentuan.');
        } else {
            if(!empty($request->input('password'))) {
                $password = Hash::make($request->input('password'));
            } else {
                $password = $user->password;
            }

            User::where('id', $user->id)
            ->update([
                'name'       => $request->input('name'),
                'email'       => $request->input('email'),
                'gender'       => $request->input('gender'),
                'user_role'       => $request->input('user_role'),
                'status'       => $request->input('status'),
                'password'       => $password,
            ]);
        }

        return redirect()
                ->route('admin.users.index')
                ->with('message', __('messages.update_siswa', ['attribute' => $request->input('name')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($user)
    {
        $result = ['status' => 200];
        try {
            $result['data'] = User::where('id', $user)->where('user_role', 'user')->delete();
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }
}