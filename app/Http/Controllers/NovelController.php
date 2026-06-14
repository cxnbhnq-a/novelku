<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novel;
use App\Models\Chapter;
use App\Models\Genre;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect; // <--- INI YANG TADI KURANG

class NovelController extends Controller
{
    /**
     * LANDING PAGE
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
     */
    public function explore(Request $request)
    {
        $query = Novel::with(['genres', 'creator'])->where('status', '!=', 'draft');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('genre')) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        $novels = $query->latest()->paginate(12)->withQueryString();
        $genres = Genre::all();

        return view('novel.explore', compact('novels', 'genres'));
    }

    /**
     * HALAMAN SINOPSIS
     */
    public function show($id)
    {
        $novel = Novel::with(['genres', 'creator'])
                      ->where('id', $id)
                      ->firstOrFail();

        // SECURITY: Jangan tampilkan draft kepada yang bukan creator
        if ($novel->status === 'draft') {
            $isCreator = Auth::check() && Auth::id() === $novel->creator_id;
            if (!$isCreator) {
                abort(404);
            }
        }

        // Logika View Anti-Spam
        $sessionKey = 'viewed_novel_' . $novel->id;
        $lastViewed = session()->get($sessionKey, 0);
        $now = now()->timestamp;

        if ($now - $lastViewed > 7200) {
            $novel->increment('views'); // Gunakan increment() biar lebih efisien
            session()->put($sessionKey, $now);
        }

        $firstChapter = Chapter::where('novel_id', $novel->id)
                            ->orderBy('chapter_number', 'asc')
                            ->first();

        $previousUrl = url()->previous();
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
                'cover_image' => \App\Services\UploadValidationService::imageRules(),
            ]);

            $imagePath = null;
            if ($request->hasFile('cover_image')) {
                $imagePath = $request->file('cover_image')->store('covers', 'public');
            }

            Novel::create([
                'creator_id' => Auth::id(),
                'uuid' => (string) Str::uuid(),
                'title' => $request->title,
                'slug' => Str::slug($request->title . '-' . uniqid()),
                'synopsis' => $request->synopsis,
                'cover_image' => $imagePath,
                'status' => 'ongoing',
            ]);

            return redirect()->route('karya.saya')->with('success', 'Mantap! Novel baru berhasil dibuat.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', $firstError ?: 'Validasi gagal. Periksa file dan data Anda.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

/**
 * HALAMAN EDIT NOVEL
 */
   public function edit($id)
{
    $novel = Novel::with(['genres'])
        ->where('id', $id)
        ->where('creator_id', Auth::id())
        ->firstOrFail();

    $genres = Genre::orderBy('name')->get();

    $chapters = Chapter::where('novel_id', $novel->id)
        ->orderBy('chapter_number')
        ->get();

    return view('dashboard.novel-edit', compact(
        'novel',
        'genres',
        'chapters'
    ));
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
                'cover_image' => \App\Services\UploadValidationService::imageRules(),
            ]);

            if ($request->hasFile('cover_image')) {
                if ($novel->cover_image) {
                    Storage::disk('public')->delete($novel->cover_image);
                }
                $novel->cover_image = $request->file('cover_image')->store('covers', 'public');
            }

            $novel->update([
                'title' => $request->title,
                'synopsis' => $request->synopsis,
                'status' => $request->status,
            ]);

            return redirect()->route('novel.edit', $id)->with('success', 'Detail novel berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', $firstError ?: 'Validasi gagal. Periksa data dan file Anda.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Pembaruan gagal.');
        }
    }

    /**
     * HAPUS NOVEL PERMANEN
     */
    public function destroy($id)
    {
        $novel = Novel::where('creator_id', Auth::id())->where('id', $id)->firstOrFail();

        if ($novel->cover_image) {
            Storage::disk('public')->delete($novel->cover_image);
        }

        $novel->delete();

        return redirect()->route('karya.saya')->with('success', 'Novel berhasil dihapus!');
    }

    public function resetViews($id)
    {
        $novel = Novel::findOrFail($id);
        $novel->update(['views' => 0]);

        return back()->with('success', 'Statistik views berhasil di-reset.');
    }
}
