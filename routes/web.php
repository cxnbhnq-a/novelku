<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NovelController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\AdminLogsController;
use App\Models\Novel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController; 

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

Route::post('/otp/verify', [AuthenticatedSessionController::class, 'verifyOtp'])
    ->middleware('throttle:3,1')
    ->name('otp.verify.post');

/*
|--------------------------------------------------------------------------
| 2. PORTAL ADMIN
|--------------------------------------------------------------------------
*/
Route::get('/portal/{token}', [AdminLoginController::class, 'showLoginForm'])
    ->middleware('throttle:5,1')
    ->name('admin.login');
Route::post('/portal/{token}', [AdminLoginController::class, 'login'])
    ->middleware('throttle:3,1')
    ->name('admin.login.submit');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::delete('/dashboard/admin/users/{uuid}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    Route::post('/admin/novel/{uuid}/reset', [AdminController::class, 'resetViews'])->name('admin.novel.reset');
    Route::delete('/admin/novel/{uuid}/delete', [AdminController::class, 'deleteNovel'])->name('admin.novel.delete');
    Route::post('/dashboard/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

    // ========== ACTIVITY LOGS ROUTES ==========
    Route::prefix('dashboard/admin/logs')->group(function () {
        Route::get('/', [AdminLogsController::class, 'index'])->name('admin.logs');
        Route::get('/detail/{id}', [AdminLogsController::class, 'detail'])->name('admin.logs.detail');
        Route::get('/export', [AdminLogsController::class, 'export'])->name('admin.logs.export');
        Route::post('/delete-old', [AdminLogsController::class, 'deleteOldLogs'])->name('admin.logs.deleteOld');
        Route::delete('/delete/{id}', [AdminLogsController::class, 'delete'])->name('admin.logs.delete');
        Route::get('/statistics', [AdminLogsController::class, 'statistics'])->name('admin.logs.statistics');
        Route::post('/clear-all', [AdminLogsController::class, 'clearAll'])->name('admin.logs.clearAll');
    });
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
            $novels = \App\Models\Novel::where('creator_id', $user->id)->latest()->get();
            $totalKarya = $novels->count();
            $totalDilihat = $novels->sum('views');
            $totalBookmark = \App\Models\Bookmark::whereIn('novel_id', $novels->pluck('id'))->count();

            return view('dashboard.creator', compact('novels', 'totalKarya', 'totalDilihat', 'totalBookmark'));
        }

        return app(App\Http\Controllers\ReaderController::class)->index();
    })->name('dashboard');

    // --- FITUR EKSKLUSIF (Explore & Baca) ---
    Route::get('/explore', [NovelController::class, 'explore'])->name('novel.explore');
    Route::get('/baca/{uuid}', [ReaderController::class, 'read'])->name('baca.chapter');

    // ====================================================================
    // --- PROFIL & SETTINGS (GLOBAL BUAT KREATOR & READER) ---
    // (Ditaruh di sini biar gak perlu di-copy 2 kali ke dalem role)
    // ====================================================================
    Route::get('/dashboard/profile', [ProfileController::class, 'editData'])->name('profile.edit');
    Route::put('/dashboard/profile', [ProfileController::class, 'updateData'])->name('profile.updateData')->middleware('throttle:5,1');
    Route::post('/dashboard/profile/send-otp', [ProfileController::class, 'sendOtp'])->name('profile.sendOtp')->middleware('throttle:3,1');
    Route::post('/dashboard/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.picture.update');
    Route::delete('/dashboard/profile/picture', [ProfileController::class, 'deletePicture'])->name('profile.picture.delete');


    /*
    |--- A. SUB-JALUR KREATOR (Hanya Role Creator) ---
    */
    Route::middleware(['role:creator'])->group(function () {
        Route::get('/dashboard/novel/create', function () { return view('dashboard.novel-create'); })->name('novel.create');
        Route::post('/dashboard/novel/create', [NovelController::class, 'store'])->name('novel.store');
        Route::get('/dashboard/novel/{id}/edit', [NovelController::class, 'edit'])->name('novel.edit');
        Route::put('/dashboard/novel/{id}', [NovelController::class, 'update'])->name('novel.update');
        Route::delete('/dashboard/novel/{id}', [NovelController::class, 'destroy'])->name('novel.destroy');

        Route::get('/dashboard/chapter/create', [ChapterController::class, 'create'])->name('chapter.create');
        Route::post('/dashboard/chapter/create', [ChapterController::class, 'store'])->name('chapter.store');
        Route::get('/dashboard/chapter/{id}/edit', [ChapterController::class, 'edit'])->name('chapter.edit');
        Route::put('/dashboard/chapter/{id}', [ChapterController::class, 'update'])->name('chapter.update');

        Route::get('/dashboard/karya', [NovelController::class, 'index'])->name('karya.saya');
    });

    /*
    |--- B. SUB-JALUR READER (Hanya Role Reader) ---
    */
    Route::middleware(['role:reader'])->group(function () {
        Route::get('/collection', [ReaderController::class, 'collection'])->name('collection');
        Route::post('/bookmark/{uuid}', [ReaderController::class, 'bookmark'])->name('novel.bookmark');
        Route::delete('/unbookmark/{uuid}', [ReaderController::class, 'removeBookmark'])->name('novel.unbookmark');
    });
});

/*
|--------------------------------------------------------------------------
| MODIFIKASI 404 LU (AMAN)
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post', 'put', 'patch', 'delete'], '/_ignition/{any}', function () {
    return response()->view('errors.404', [], 404);
})->where('any', '.*');

require __DIR__.'/auth.php';
