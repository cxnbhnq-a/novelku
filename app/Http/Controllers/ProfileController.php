<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // ========================================================
    // TAMPILAN HALAMAN EDIT PROFIL
    // ========================================================
    public function editData(): View
    {
        return view('dashboard.profile-edit');
    }

    // ========================================================
    // 1. AJAX: KIRIM OTP KE EMAIL LAMA
    // ========================================================
    public function sendOtp(Request $request)
    {
        $user = Auth::user();
        $otp = rand(100000, 999999); // Bikin 6 digit angka acak

        // Simpan OTP di Cache server, hangus dalam 5 menit
        Cache::put('otp_profile_' . $user->id, $otp, now()->addMinutes(5));

        // Kirim OTP via Email Murni (Tanpa View)
        try {
            Mail::raw("Halo {$user->name},\n\nSeseorang (semoga lu sendiri) mau ganti info sensitif di akun NovelKu lu.\n\nKode OTP lu: {$otp}\n\nKode ini cuma berlaku 5 menit ya der. Jangan dikasih ke siapa-siapa!", function ($message) use ($user) {
                $message->to($user->email)->subject('Kode OTP Keamanan - NovelKu');
            });
            
            return response()->json(['success' => true, 'message' => 'OTP berhasil dikirim ke email lama lu!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal ngirim email. Cek konfigurasi SMTP server lu.'], 500);
        }
    }

    // ========================================================
    // 2. PROSES UPDATE DATA PROFIL SUPER KETAT
    // ========================================================
    public function updateData(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Cek apakah user ngubah email atau ngisi password baru
        $isEmailChanged = $request->email !== $user->email;
        $isPasswordFilled = $request->filled('password');

        // Kalau ada data sensitif yang diubah, WAJIB CEK OTP
        if ($isEmailChanged || $isPasswordFilled) {
            
            // Validasi input OTP dari form
            $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            // Format password diubah jadi array biar bisa pake rule Password bawaan Laravel
            'password' => [
                'nullable',
                'confirmed',
                Password::min(10)->max(64)->uncompromised()
            ],
        ]);
            // Ambil OTP asli dari Cache server
            $cachedOtp = Cache::get('otp_profile_' . $user->id);

            // Kalau OTP di server udah hangus atau inputan salah
            if (!$cachedOtp || $cachedOtp != $request->otp) {
                return back()->with('error', 'OTP salah atau udah kedaluwarsa der! Silakan muat ulang halaman.')->withInput();
            }

            // Kalau bener, hapus OTP dari memory server biar aman (Gak bisa dipake 2 kali)
            Cache::forget('otp_profile_' . $user->id);
        }

        // Lanjut Validasi Normal Form
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Eksekusi Update ke Database
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($isPasswordFilled) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return back()->with('success', 'Profil lu berhasil dan AMAN diupdate der!');
    }

    // ========================================================
    // UPDATE FOTO PROFIL
    // ========================================================
    public function updatePicture(Request $request): RedirectResponse
    {
        try {
            // Validasi Gambar Super Ketat
            $request->validate([
                'profile_picture' => array_merge(
                    ['required'], 
                    \App\Services\UploadValidationService::imageRules()
                ),
            ], [
                'profile_picture.image' => 'File harus berupa gambar.',
                'profile_picture.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
                'profile_picture.max' => 'Ukuran gambar maksimal 2MB.',
            ]);

            $user = Auth::user();

            // Hapus foto lama di storage
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Simpan foto baru
            $path = $request->file('profile_picture')->store('profiles', 'public');
            
            // Simpan nama path-nya ke database (Jangan dipisah, pake cara native)
            $user->profile_picture = $path;
            $user->save();

            return Redirect::back()->with('success', 'Foto profil berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return Redirect::back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', $firstError ?: 'Validasi file gagal. Periksa file dan coba lagi.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Sistem menolak file ini. Pastikan file aman dan coba lagi.');
        }
    }

    // ========================================================
    // HAPUS FOTO PROFIL
    // ========================================================
    public function deletePicture(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            
            $user->profile_picture = null;
            $user->save();
        }

        return Redirect::back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
