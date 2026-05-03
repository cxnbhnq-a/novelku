<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NovelController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Models\Novel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController; // Pastikan ini ada di paling atas file!



/*
|--------------------------------------------------------------------------
| 1. JALUR PUBLIK (Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::get('/', [NovelController::class, 'welcome'])->name('home');
Route::get('/novel/{uuid}', [NovelController::class, 'show'])->name('novel.show');
// --- ROUTES VERIFIKASI OTP ---
Route::get('/otp/verify', function () {
    if (!session()->has('otp_email')) {
        return redirect()->route('login')->withErrors(['email' => 'Akses ilegal terdeteksi!']);
    }
    return view('auth.verify-otp'); 
})->name('otp.verify');

// HANYA SATU BARIS INI UNTUK POST (Sudah include Satpam)
Route::post('/otp/verify', [AuthenticatedSessionController::class, 'verifyOtp'])
    ->middleware('throttle:3,1')
    ->name('otp.verify.post');

/*
|--------------------------------------------------------------------------
| 2. PORTAL ADMIN
|--------------------------------------------------------------------------
*/
Route::get('/portal/{token}', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/portal/{token}', [AdminLoginController::class, 'login'])
->middleware('throttle:3,1')
->name('admin.login.submit');

Route::middleware(['auth:admin'])->group(function () {
    
    // 1. Tampilan Dashboard (Biar AdminController yang mikir logikanya)
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/admin/users', [AdminController::class, 'users'])->name('admin.users');
// Opsional buat nanti kalau fitur hapus user udah mau diaktifin:
    Route::delete('/dashboard/admin/users/{uuid}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // 2. Aksi Super Admin (Ini yang bikin tombol lu berfungsi)
    Route::post('/admin/novel/{uuid}/reset', [AdminController::class, 'resetViews'])->name('admin.novel.reset');
    Route::delete('/admin/novel/{uuid}/delete', [AdminController::class, 'deleteNovel'])->name('admin.novel.delete');
    
    // 3. Logout
    Route::post('/dashboard/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

/*
|--------------------------------------------------------------------------
| 3. JALUR UMUM (Wajib Login & Verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD UTAMA (Pembeda Role) ---
    Route::get('/dashboard', function () {
        $user = auth()->user(); 

        if ($user->role === 'creator') {
            // Dashboard Creator: Ambil data ringkasan (misal 5 novel terbaru)
            // Biar variabel $novels di view dashboard.creator gak undefined
           $novels = \App\Models\Novel::where('creator_id', $user->id)->latest()->get();
        
        // 1. Total Karya
        $totalKarya = $novels->count();
        
        // 2. Total Dilihat (Narik dari kolom views yang baru kita buat)
        $totalDilihat = $novels->sum('views');
        
        // 3. Total Bookmark (Narik dari tabel bookmarks)
        // Kita hitung berapa banyak baris di tabel bookmark yang novel_id-nya milik si creator
        $totalBookmark = \App\Models\Bookmark::whereIn('novel_id', $novels->pluck('id'))->count();

        return view('dashboard.creator', compact('novels', 'totalKarya', 'totalDilihat', 'totalBookmark')); 
    } 
    
    return app(App\Http\Controllers\ReaderController::class)->index();
})->middleware(['auth', 'verified'])->name('dashboard');

    // --- FITUR EKSKLUSIF (Explore & Baca) ---
    Route::get('/explore', [NovelController::class, 'explore'])->name('novel.explore');
    Route::get('/baca/{uuid}', [ReaderController::class, 'read'])->name('baca.chapter');

    // --- PROFIL & SETTINGS ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.picture.update');
    Route::delete('/profile/picture', [ProfileController::class, 'deletePicture'])->name('profile.picture.delete');

    /*
    |--- A. SUB-JALUR KREATOR (Hanya Role Creator) ---
    */
    Route::middleware(['role:creator'])->group(function () {
        // Novel Management
        Route::get('/dashboard/novel/create', function () { return view('dashboard.novel-create'); })->name('novel.create');
        Route::post('/dashboard/novel/create', [NovelController::class, 'store'])->name('novel.store');
        Route::get('/dashboard/novel/{id}/edit', [NovelController::class, 'edit'])->name('novel.edit');
        Route::put('/dashboard/novel/{id}', [NovelController::class, 'update'])->name('novel.update');
        Route::delete('/dashboard/novel/{id}', [NovelController::class, 'destroy'])->name('novel.destroy');

        // Chapter Management
        Route::get('/dashboard/chapter/create', [ChapterController::class, 'create'])->name('chapter.create');
        Route::post('/dashboard/chapter/create', [ChapterController::class, 'store'])->name('chapter.store');
        Route::get('/dashboard/chapter/{id}/edit', [ChapterController::class, 'edit'])->name('chapter.edit');
        Route::put('/dashboard/chapter/{id}', [ChapterController::class, 'update'])->name('chapter.update');

        // Halaman List Karya (Full List)
        Route::get('/dashboard/karya', [NovelController::class, 'index'])->name('karya.saya');
    });

    /*
    |--- B. SUB-JALUR READER (Hanya Role Reader) ---
    */
    Route::middleware(['role:reader'])->group(function () {
        // Hapus duplikasi rute collection, pakai yang dari Controller saja
        Route::get('/collection', [ReaderController::class, 'collection'])->name('collection');
        Route::post('/bookmark/{uuid}', [ReaderController::class, 'bookmark'])->name('novel.bookmark');
        Route::delete('/unbookmark/{uuid}', [ReaderController::class, 'removeBookmark'])->name('novel.unbookmark');
    });
});

require __DIR__.'/auth.php';