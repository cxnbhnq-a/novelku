<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novel;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * TAMPILAN HALAMAN DASHBOARD ADMIN
     * Mengirim semua data statistik ke admin.blade.php
     */
    public function index()
    {
        $totalUser = User::count();
        $totalNovel = Novel::count();
        
        // Logika sederhana: Creator adalah user yang minimal punya 1 novel
        $totalCreator = Novel::distinct('creator_id')->count('creator_id');
        
        // Reader adalah sisa user yang belum pernah bikin novel
        $totalReader = $totalUser - $totalCreator; 

        // Ambil 10 novel terbaru untuk ditampilkan di tabel
        $latestNovels = Novel::with('creator')->latest()->take(10)->get();

        return view('dashboard.admin', compact(
            'totalUser', 
            'totalNovel', 
            'totalReader', 
            'totalCreator', 
            'latestNovels'
        ));
    }

    /**
     * RESET VIEWS (ADMIN Bypassing)
     * Bebas reset views tanpa harus jadi pemilik novel
     */
    public function resetViews($uuid)
    {
        $novel = Novel::where('uuid', $uuid)->firstOrFail();
        
        // Paksa views jadi 0
        $novel->views = 0;
        $novel->save();

        return back()->with('success', 'Statistik Views untuk novel "' . $novel->title . '" berhasil di-reset ke 0.');
    }

    /**
     * HAPUS PAKSA NOVEL (Take Down)
     * Admin berhak menghapus novel yang melanggar tanpa izin kreator
     */
    public function deleteNovel($uuid)
    {
        $novel = Novel::where('uuid', $uuid)->firstOrFail();
        
        // Hapus file gambarnya dari server biar gak menuh-menuhin disk
        if ($novel->cover_image) {
            Storage::disk('public')->delete($novel->cover_image);
        }

        $novel->delete();

        return back()->with('success', 'Take Down Berhasil: Novel telah dihapus paksa dari sistem.');
    }
    public function users(Request $request)
    {
        $query = \App\Models\User::query();

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        // Fitur Filter Role
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

        // Ambil data user, hitung jumlah novelnya, lalu paginate 10 per halaman
        $users = $query->withCount('novels')->latest()->paginate(10);

        return view('dashboard.admin-users', compact('users')); // Pastikan nama folder dan file blade sesuai
    }
    // Fungsi buat ngehapus user
    public function deleteUser($uuid)
    {
        $user = \App\Models\User::findOrFail($uuid);
        
        // Hapus user dari database
        $user->delete();

        // Balik lagi ke halaman sebelumnya bawa pesan sukses
        return back()->with('success', 'Akun user berhasil dimusnahkan!');
    }
}   