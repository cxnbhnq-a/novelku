<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog; // WAJIB PANGGIL MODEL LOG KITA
use App\Services\CaptchaService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // WAJIB PANGGIL VALIDATOR
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'captchaQuestion' => CaptchaService::getQuestion(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(10)
                    ->max(64)
                    ->uncompromised(),
            ],
            'role' => [
                'required',
                'in:reader,creator'
            ],
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
            'captcha.required' => 'CAPTCHA wajib diisi.',
        ]);

        if ($validator->fails()) {
            CaptchaService::clear();
            
            // 🚨 SENSOR LOG: CATAT ORANG GAGAL DAFTAR 🚨
            ActivityLog::create([
                'log_type' => 'register_failed',
                'email' => $request->email ?? 'Tidak diisi',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'failed',
                'severity' => 'warning',
                'message' => 'Registrasi ditolak: ' . $validator->errors()->first(),
            ]);

            // Tendang balik ke halaman register bawa pesan error-nya
            return redirect()->back()->withErrors($validator)->withInput();
        }

        CaptchaService::clear();

        // 3. KALAU LOLOS: Eksekusi bikin akun seperti biasa
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // 4. 🚨 SENSOR LOG: CATAT ORANG BERHASIL DAFTAR 🚨
        ActivityLog::create([
            'log_type' => 'register_success',
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
            'severity' => 'info',
            'message' => 'User baru berhasil mendaftar ke sistem',
        ]);

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Akun berhasil dibuat! Silakan login untuk menerima kode OTP di email Anda.'
            );
    }
}