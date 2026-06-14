<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novel;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Services\ActivityLogService;

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
    public function resetViews($id, Request $request = null)
    {
        $novel = Novel::where('id',$id)->firstOrFail();
        
        // LOG: Reset views
        ActivityLogService::log(
            'admin_action',
            "Admin mereset views untuk novel: {$novel->title} dari {$novel->views} menjadi 0",
            'success',
            'info',
            auth()->user()->email ?? null,
            auth()->user()->id ?? null,
            ['novel_id' => $novel->id, 'old_views' => $novel->views],
            null,
            null,
            $request
        );

        // Paksa views jadi 0
        $novel->views = 0;
        $novel->save();

        return back()->with('success', 'Statistik Views untuk novel "' . $novel->title . '" berhasil di-reset ke 0.');
    }

    /**
     * HAPUS PAKSA NOVEL (Take Down)
     * Admin berhak menghapus novel yang melanggar tanpa izin kreator
     */
    public function deleteNovel($id, Request $request = null)
    {
        $novel = Novel::where('id', $id)->firstOrFail();
        
        // LOG: Delete novel
        ActivityLogService::log(
            'novel_deleted_by_admin',
            "Admin menghapus novel: {$novel->title} (Penulis: {$novel->creator->email})",
            'success',
            'warning',
            auth()->user()->email ?? null,
            auth()->user()->id ?? null,
            ['novel_id' => $novel->id, 'novel_title' => $novel->title, 'creator_email' => $novel->creator->email],
            null,
            null,
            $request
        );

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

    /**
     * HAPUS USER
     */
    public function deleteUser($id, Request $request = null)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // LOG: Delete user
        ActivityLogService::log(
            'user_deleted_by_admin',
            "Admin menghapus user: {$user->email} ({$user->name})",
            'success',
            'warning',
            auth()->user()->email ?? null,
            auth()->user()->id ?? null,
            ['deleted_user_id' => $user->id, 'deleted_user_email' => $user->email],
            null,
            null,
            $request
        );

        // Hapus user dari database
        $user->delete();

        // Balik lagi ke halaman sebelumnya bawa pesan sukses
        return back()->with('success', 'Akun user berhasil dimusnahkan!');
    }
}   
