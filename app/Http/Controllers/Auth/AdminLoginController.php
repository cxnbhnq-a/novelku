<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /**
     * Menampilkan form login
     */
    public function showLoginForm($token)
    {
        // 1. CEK TOKEN! Kalau bukan token rahasia dari .env, LEMPAR 404!
        if ($token !== env('ADMIN_SECRET_TOKEN')) {
            abort(404);
        }

        // Kalau lolos, baru tampilkan halaman view login-nya
        // (Sambil bawa variabel $token buat dikirim lagi pas submit)
        return view('auth.admin-login', compact('token'));
    }

    /**
     * Memproses data login
     */
    public function login(Request $request, $token)
    {
        if ($token !== env('ADMIN_SECRET_TOKEN')) {
            abort(404);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // PERHATIKAN: Kita spesifik pakai guard 'admin'
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Identitas tidak dikenali sistem.',
        ])->onlyInput('email');
    }
    /**
     * Memproses Logout Khusus Admin
     */
    public function logout(Request $request)
    {
        // 1. Matiin sesi khusus guard 'admin'
        Auth::guard('admin')->logout();

        // 2. Hancurkan semua data sesi biar aman dari hacker
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3. Lempar ke halaman depan (Home) biar kayak pengunjung biasa (Stealth Mode)
        return redirect('/');
    }
}