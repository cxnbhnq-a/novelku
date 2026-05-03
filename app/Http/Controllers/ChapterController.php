<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use Illuminate\Support\Facades\Storage;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ChapterController extends Controller
{
    public function create(Request $request)
    {
        // 1. Ambil daftar semua novel milik creator buat dropdown select di form
        $novels = \App\Models\Novel::where('creator_id', auth()->id())->get();

        // 2. Ambil novel_id dari URL kalau ada (biar otomatis kepilih di form)
        $selectedNovelId = $request->query('novel_id');

        // 3. Kirim variabel $novels ke view biar @foreach di blade lu ga error lagi
        return view('dashboard.chapter-create', compact('novels', 'selectedNovelId'));
    }

    public function store(Request $request)
{

    try {
        $request->validate([
            'novel_id' => [
                'required',
                Rule::exists('novels', 'id')->where(fn($query) => $query->where('creator_id', auth()->id())),
            ],
            'chapter_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'content' => 'required|string', 
            'chapter_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('chapter_image')) {
            $imagePath = $request->file('chapter_image')->store('chapters', 'public');
        }

        $cleanContent = Purifier::clean($request->content);

        // SECURE PATCH: Gunakan Transaksi DB
        DB::beginTransaction();
        Chapter::create([
            'novel_id' => $request->novel_id,
            'chapter_number' => $request->chapter_number,
            'title' => $request->title,
            'content' => $cleanContent,
            'chapter_image' => $imagePath,
        ]);
        DB::commit();

        return redirect()->route('dashboard')->with('success', 'Bab baru berhasil dipublikasikan dengan aman!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        // SECURE PATCH: Hapus file jika validasi gagal setelah upload diproses (jarang terjadi namun antisipasi)
        if (isset($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        // SECURE PATCH: Hapus file dari disk jika DB gagal
        if (isset($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        return back()->with('error', 'Terjadi kesalahan sistem saat memproses bab. Pastikan input valid.');
    }
}

    // Menampilkan halaman edit bab
    public function edit($uuid)
    {
        // Cari bab, pastikan novelnya milik user yang sedang login
        $chapter = Chapter::whereHas('novel', function($query) {
            $query->where('creator_id', auth()->id());
        })->findOrFail($uuid);

        // Ambil daftar novel buat jaga-jaga kalau mau pindah buku
        $novels = \App\Models\Novel::where('creator_id', auth()->id())->get();

        return view('dashboard.chapter-edit', compact('chapter', 'novels'));
    }

    // Menyimpan hasil revisi bab
    public function update(Request $request, $uuid)
{
    $chapter = Chapter::whereHas('novel', function($query) {
        $query->where('creator_id', auth()->id());
    })->findOrFail($uuid);


    try {
        $request->validate([
            'novel_id' => [
                'required',
                Rule::exists('novels', 'id')->where(fn($query) => $query->where('creator_id', auth()->id())),
            ],
            'chapter_number' => 'required|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string', 
            'chapter_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $cleanContent = Purifier::clean($request->content);
        
        $data = [
            'novel_id' => $request->novel_id,
            'chapter_number' => $request->chapter_number,
            'title' => $request->title,
            'content' => $cleanContent,
        ];

        $newImagePath = null;
        if ($request->hasFile('chapter_image')) {
            // 1. Simpan gambar baru ke disk (JANGAN hapus yang lama dulu)
            $newImagePath = $request->file('chapter_image')->store('chapters', 'public');
            $data['chapter_image'] = $newImagePath;
        }

        // 2. Bungkus ke dalam transaksi DB
        DB::beginTransaction();
        $oldImagePath = $chapter->chapter_image;
        $chapter->update($data);
        DB::commit();

        // 3. Jika DB SUKSES, baru hapus file lama untuk mencegah bengkak storage
        if ($newImagePath && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('novel.edit', $chapter->novel_id)->with('success', 'Bab berhasil direvisi dan diamankan!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        // SECURE PATCH: Jika DB GAGAL, hapus file baru yang terlanjur terunggah ke disk
        if (isset($newImagePath)) {
            Storage::disk('public')->delete($newImagePath);
        }
        return back()->with('error', 'Gagal update bab. Terjadi kesalahan pada sistem.');
     }
   }
}