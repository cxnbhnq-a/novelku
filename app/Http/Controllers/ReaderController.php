<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Novel;
use App\Models\Genre;
use App\Models\Bookmark;


class ReaderController extends Controller
{
    // DASHBOARD READER
    public function index()
    {
        $recommendations = Novel::with(['genres', 'creator'])
                            ->whereIn('status', ['published', 'ongoing', 'completed'])
                            ->latest()
                            ->take(6)
                            ->get();

        $genres = Genre::all();
        $collections = []; 

        return view('dashboard.reader', compact('recommendations', 'genres', 'collections'));
    }

    // RUANG BACA REAL
    public function read($uuid)
    {
        // 1. Ambil data bab beneran beserta info novelnya
        $chapter = Chapter::with('novel.creator')->findOrFail($uuid);

        $isCreator = auth()->check() && auth()->id() === $chapter->novel->creator_id;
        if ($chapter->novel->status === 'draft' && !$isCreator) {
            abort(404); 
        }

        // 2. Logic Tombol "Sebelumnya"
        // Cari bab yang novel_id-nya sama, tapi nomor babnya lebih kecil 1
        $prevChapter = Chapter::where('novel_id', $chapter->novel_id)
                        ->where('chapter_number', $chapter->chapter_number - 1)
                        ->first();

        // 3. Logic Tombol "Selanjutnya"
        // Cari bab yang novel_id-nya sama, tapi nomor babnya lebih besar 1
        $nextChapter = Chapter::where('novel_id', $chapter->novel_id)
                        ->where('chapter_number', $chapter->chapter_number + 1)
                        ->first();

        return view('reader.read', compact('chapter', 'prevChapter', 'nextChapter'));
    }
    // Fungsi Simpan ke Koleksi
public function bookmark($uuid)
{
    $userId = auth()->id();
    $bookmark = Bookmark::where('user_id', $userId)->where('novel_id', $uuid)->first();

    if ($bookmark) {
        $bookmark->delete();
        return response()->json(['status' => 'removed', 'message' => 'Dihapus dari koleksi']);
    } else {
        Bookmark::create(['user_id' => $userId, 'novel_id' => $uuid]);
        return response()->json(['status' => 'added', 'message' => 'Berhasil ditambah ke koleksi!']);
    }
}
// Halaman Koleksi Saya
public function collection()
{
    // Ambil novel yang dibookmark oleh user login
    $bookmarks = Bookmark::with('novel.creator')
                ->where('user_id', auth()->id())
                ->latest()
                ->get();

    return view('dashboard.collection', compact('bookmarks'));
}

// Hapus dari Koleksi
public function removeBookmark($uuid)
{
    Bookmark::where('user_id', auth()->id())->where('novel_id', $uuid)->delete();
    return redirect()->back()->with('success', 'Novel dihapus dari koleksi.');
}
}