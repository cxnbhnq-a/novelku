<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Models\User;
use App\Mail\SendOtpMail;
use App\Services\ActivityLogService;
use App\Services\CaptchaService;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login', [
            'captchaQuestion' => CaptchaService::getQuestion(),
        ]);
    }

    /**
     * Handle an incoming authentication request. (SEKARANG JADI GERBANG OTP)
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
            'captcha' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (! CaptchaService::validate($value)) {
                        $fail('Verifikasi CAPTCHA tidak valid.');
                    }
                },
            ],
        ], [
            'email.required' => 'Kredensial tidak valid.',
            'email.email' => 'Kredensial tidak valid.',
            'password.required' => 'Kredensial tidak valid.',
            'captcha.required' => 'CAPTCHA wajib diisi.',
        ]);

        if ($validator->fails()) {
            CaptchaService::clear();

            return back()->withErrors($validator)->withInput();
        }

        CaptchaService::clear();

        $user = User::where('email', $request->email)->first();

        // 2. Kalau password cocok, JANGAN LOGIN DULU! Kirim OTP.
        if ($user && Hash::check($request->password, $user->password)) {
            
            // Generate 6 Digit Angka Random
            $otp = rand(100000, 999999);
            
            // Simpan OTP ke DB dengan masa aktif 5 menit
            $user->update([
                'otp_code' => Hash::make($otp),
                'otp_expires_at' => Carbon::now()->addMinutes(5)
            ]);

            // Kirim Email
            Mail::to($user->email)->send(new SendOtpMail($otp));

            // LOG: OTP Dikirim (Sukses)
            ActivityLogService::log(
                'otp_success',
                "OTP berhasil dikirim ke email: {$user->email}",
                'success',
                'info',
                $user->email,
                $user->id,
                null,
                null,
                null,
                $request
            );

            // Simpan email di session sementara buat ngecek di halaman OTP
            session(['otp_email' => $user->email]);

            // Arahin ke halaman input OTP
            return redirect()->route('otp.verify');
        }

        // Kalau email/password salah, kembalikan dengan error
        // LOG: Login Gagal
        $reason = $user ? 'Password salah' : 'Email tidak terdaftar';
        ActivityLogService::logLoginFailed($request->email, $reason, $request);

        return back()->withErrors(['email' => 'Kredensial tidak valid.']);
    }

    /**
     * Verify the OTP and execute actual Login.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric|digits:6']);
        $email = session('otp_email');

        // Kalau user iseng nembak URL tanpa login dulu
        if (!$email) {
            ActivityLogService::logOtpFailed('unknown', 'Session expired', $request);
            return redirect()->route('login')->withErrors(['email' => 'Sesi kedaluwarsa, silakan login ulang.']);
        }

        $user = User::where('email', $email)->first();

        // Cek kecocokan OTP dan waktu kedaluwarsa
        if ($user && Hash::check($request->otp, $user->otp_code) && Carbon::now()->isBefore($user->otp_expires_at)) {
            
            // OTP Valid! Bersihkan kolom OTP biar nggak dipake lagi
            $user->update(['otp_code' => null, 'otp_expires_at' => null]);
            
            // Hapus session email
            session()->forget('otp_email');

            // RESMI LOGIN!
            Auth::login($user);
            
            // LOG: Login Sukses
            ActivityLogService::logLoginSuccess($user, $request);
            
            // Wajib regenerate session biar aman dari serangan hacker
            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // LOG: OTP Gagal
        $reason = $user && $user->otp_code ? 
            (Carbon::now()->isAfter($user->otp_expires_at) ? 'OTP expired' : 'Invalid OTP') 
            : 'User not found';
        ActivityLogService::logOtpFailed($email, $reason, $request);

        return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kedaluwarsa.']);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // LOG: Logout (Tetap jalan)
        if ($user) {
            ActivityLogService::logLogout($user, $request);
        }

        // 1. Logout khusus guard 'web'
        Auth::guard('web')->logout();

        // 2. Cek! Kalau Admin NGGAK lagi login, baru hancurin session rumah
        if (! Auth::guard('admin')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/');
    }
}