<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // <-- PENTING: Untuk ngatur file gambar
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // 🚨 PATCH KEAMANAN: Hapus fisik file sebelum datanya hilang
        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // ========================================================
    // TAMBAHAN: FUNGSI UPLOAD & HAPUS FOTO PROFIL
    // ========================================================

     public function updatePicture(Request $request): RedirectResponse
    {
        try {
            // 1. Validasi Super Ketat
            $request->validate([
                'profile_picture' => [
                    'required',
                    'image', // Cek struktur dasar gambar
                    'mimes:jpeg,png,jpg,webp', // Cek ekstensi
                    'max:2048', // Maksimal 2MB
                    // Note: 'mimetypes' gua hapus karena rule 'image' dan 'mimes' di Laravel modern 
                    // otomatis udah ngecek MIME type aslinya pake fungsi finfo() PHP.
                ],
            ], [
                'profile_picture.image' => 'File harus berupa gambar.',
                'profile_picture.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
                'profile_picture.max' => 'Ukuran gambar maksimal 2MB.',
            ]);

            $user = $request->user();

            // 2. Hapus foto lama
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // 3. Simpan foto baru
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $user->update(['profile_picture' => $path]);

            return Redirect::back()->with('success', 'Foto profil berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi (Misal: Hacker masukin shell.php.jpg)
            return Redirect::back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            // Tangkap error sistem (Mencegah Layar Merah / Information Disclosure)
            return Redirect::back()->with('error', 'Sistem menolak file ini. Pastikan file aman dan coba lagi.');
        }
    }
    public function deletePicture(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        return Redirect::back()->with('success', 'Foto profil berhasil dihapus.');
    }
}