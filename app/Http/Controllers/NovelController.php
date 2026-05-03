<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novel;
use App\Models\Chapter;
use App\Models\Genre;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NovelController extends Controller
{
    /**
     * LANDING PAGE
     * Menampilkan 8 novel terbaru untuk pengunjung umum.
     */
    public function welcome()
    {
        $novels = Novel::with(['genres', 'creator'])
                    ->whereIn('status', ['published', 'ongoing', 'completed'])
                    ->latest()
                    ->take(8)
                    ->get();

        return view('welcome', compact('novels'));
    }

    /**
     * DASHBOARD KARYA (CREATOR)
     * Menampilkan daftar semua novel milik user yang sedang login.
     */
    public function index()
    {
        $novels = Novel::with('genres')
                    ->where('creator_id', Auth::id())
                    ->latest()
                    ->get();

        return view('dashboard.karya', compact('novels'));
    }

    /**
     * EXPLORE NOVEL
     * Katalog lengkap dengan fitur pencarian dan filter genre.
     */
    public function explore(Request $request)
    {
        $query = Novel::with(['genres', 'creator'])->where('status', '!=', 'draft');

        // Fitur Search judul
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Fitur Filter per Genre
        if ($request->filled('genre')) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        // Pagination 12 data per halaman + tetap bawa query string saat pindah page
        $novels = $query->latest()->paginate(12)->withQueryString();
        $genres = Genre::all();

        return view('novel.explore', compact('novels', 'genres'));
    }

    /**
     * HALAMAN SINOPSIS
     * Menampilkan detail novel dan mencari chapter pertama untuk tombol baca.
     */
   public function show($id)
    {
        // 1. CARA BENER NYARI PAKE UUID
        $novel = Novel::with(['genres', 'creator'])
                      ->where('id', $id)
                      ->firstOrFail();
        
        // --- LOGIKA VIEW ANTI-SPAM (Versi Realistis) ---
        $sessionKey = 'viewed_novel_' . $novel->id;
        $lastViewed = session()->get($sessionKey, 0); // Ambil waktu terakhir baca
        $now = now()->timestamp; // Waktu detik ini

        // Kalau belum pernah buka ATAU udah lewat 2 jam (7200 detik)
        if ($now - $lastViewed > 7200) {
            $novel->views += 1; // Pake cara manual biar tembus $fillable kalo lupa
            $novel->save();
            
            // Catat waktu detik ini sebagai stempel baru
            session()->put($sessionKey, $now); 
        }
        
        // 2. CARA BENER AMBIL ID (Pake $novel->id dari hasil pencarian di atas)
        $firstChapter = Chapter::where('novel_id', $novel->id)
                            ->orderBy('chapter_number', 'asc')
                            ->first();
                            // AMBIL URL SEBELUMNYA
    // --- LOGIKA BACK URL DINAMIS ---
    $previousUrl = url()->previous();

    // Logika biar gak looping: kalau asal dari halaman baca atau refresh, lempar ke dashboard
    if (str_contains($previousUrl, '/baca/') || $previousUrl == request()->url()) {
        $backUrl = auth()->check() ? route('dashboard') : route('home');
    } else {
        $backUrl = $previousUrl;
    }
        return view('novel.show', compact('novel', 'firstChapter', 'backUrl'));
    }
    /**
     * SIMPAN NOVEL BARU
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'synopsis' => 'required|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            $imagePath = null;
            if ($request->hasFile('cover_image')) {
                $imagePath = $request->file('cover_image')->store('covers', 'public');
            }

            Novel::create([
                'creator_id' => Auth::id(),
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'title' => $request->title,
                'slug' => Str::slug($request->title . '-' . uniqid()),
                'synopsis' => $request->synopsis,
                'cover_image' => $imagePath,
                'status' => 'ongoing', 
            ]);

            return redirect()->route('karya.saya')->with('success', 'Mantap! Novel baru berhasil dibuat.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return Redirect::back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Terjadi kesalahan sistem saat memproses novel Anda.');
        }
    }
    /**
     * FORM EDIT NOVEL
     */
    public function edit($id)
    {
        // Pastikan hanya pemilik yang bisa akses form edit
        $novel = Novel::where('creator_id', Auth::id())->where('id', $id)->firstOrFail();
        $chapters = Chapter::where('novel_id', $novel->id)->orderBy('chapter_number', 'asc')->get();

        return view('dashboard.novel-edit', compact('novel', 'chapters'));
    }

    /**
     * PROSES UPDATE DATA NOVEL
     */
    public function update(Request $request, $id)
    {
        try {
            $novel = Novel::where('creator_id', Auth::id())->where('id', $id)->firstOrFail();

            $request->validate([
                'title' => 'required|string|max:255',
                'synopsis' => 'required|string',
                'status' => 'required|in:ongoing,completed,published',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            if ($request->hasFile('cover_image')) {
                if ($novel->cover_image) {
                    Storage::disk('public')->delete($novel->cover_image);
                }
                $novel->cover_image = $request->file('cover_image')->store('covers', 'public');
            }

            $novel->title = $request->title;
            $novel->synopsis = $request->synopsis;
            $novel->status = $request->status;
            $novel->save();

            return redirect()->route('novel.edit', $id)->with('success', 'Detail novel berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Pembaruan gagal. Cek kembali data Anda.');
        }
    }

    /**
     * HAPUS NOVEL PERMANEN
     */
    public function destroy($id)
    {
        // Kunci biar gak ada orang lain yang iseng tembak ID novel orang lewat URL
        $novel = Novel::where('creator_id', Auth::id())->where('id', $id)->firstOrFail();

        // Bersihkan storage dari gambar cover novel ini
        if ($novel->cover_image) {
            Storage::disk('public')->delete($novel->cover_image);
        }

        $novel->delete();

        return redirect()->route('karya.saya')->with('success', 'Novel berhasil dihapus selamanya!');
    }
    public function resetViews($id)
{
    $novel = Novel::where('id', $id)->firstOrFail();
    $novel->update(['views' => 0]);

    return back()->with('success', 'Statistik views novel ' . $novel->title . ' berhasil di-reset ke 0!');
}
}