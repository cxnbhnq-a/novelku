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
        if ($token !== env('ADMIN_SECRET_TOKEN')) abort(404);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            
            // 🚨 LOG ADMIN LOGIN 🚨
            \App\Models\ActivityLog::create([
                'log_type' => 'admin_action',
                'message' => 'Admin berhasil login ke sistem',
                'status' => 'success',
                'severity' => 'info',
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Identitas tidak dikenali.']);
    }
    /**
     * Memproses Logout Khusus Admin
     */
    public function logout(Request $request)
    {
        // 1. Logout khusus guard 'admin'
        Auth::guard('admin')->logout();

        // 2. Cek! Kalau User biasa (guard 'web') NGGAK lagi login, baru hancurin session rumah
        if (! Auth::guard('web')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // 3. Admin aman, balik ke home tanpa mengganggu user
        return redirect('/');
    }
}