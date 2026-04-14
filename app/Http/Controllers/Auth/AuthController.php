<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // login
    /**
     * Display a listing of the resource.
     */
    public function loginShow()
    {
        return view ('auth.login');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function loginProses(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index')->with('success', 'Login berhasil.');
        }

        return back()->withErrors([
            'username' => 'Login gagal, pastikan username dan password benar.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('loginShow')->with('success', 'Logout berhasil.');
    }

    // Register
    /**
     * Show the form for creating a new resource.
     */
    public function registerShow()
    {
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function registerProses(Request $request)
    {
        $validate = $request->validate([
            'username' => 'required|unique:users',
            'nama'     => 'required',
            'level'    => 'required|in:admin,pengguna',
            'password' => 'required'
        ]);

        $validate['password'] = Hash::make($validate['password']);

        User::create($validate);

        return redirect()->route('loginShow')->with('success', 'Registrasi berhasil. Silakan login.');
    }

    /**
     * Display the specified resource.
     */
    public function me(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
