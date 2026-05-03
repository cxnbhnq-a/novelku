<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Novel - NovelKu</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* RESET DASAR BIAR GAK MELUBER */
        *, *::before, *::after { box-sizing: border-box; }
        
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; overflow-x: hidden; }
        
        aside { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; flex-shrink: 0;}
        .nav-link { padding: 12px 15px; border-radius: var(--radius); text-decoration: none; color: var(--text); font-weight: 500; transition: 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--bg); border: 1px solid var(--border); }
        
        main { flex: 1; padding: 40px; overflow-y: auto; width: 100%; }
        
        /* HEADER RAPI */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 15px; flex-wrap: wrap; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .header-left h1 { margin: 0; font-size: 24px; line-height: 1.2; }
        .header-left p { color: var(--muted); margin: 4px 0 0; font-size: 14px; }
        .btn-back { color: var(--text); font-weight: 600; text-decoration: none; font-size: 14px; white-space: nowrap; }
        
        /* GRID & FORM */
        .manager-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start; }
        .form-card { background: var(--card); border: 1px solid var(--border); padding: 30px; border-radius: var(--radius); width: 100%; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; }
        input[type="text"], textarea, select { width: 100%; padding: 12px; border: 1px solid var(--border); background: var(--bg); color: var(--text); border-radius: 8px; font-family: inherit; font-size: 15px; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: var(--text); }
        textarea { resize: vertical; min-height: 120px; }
        .cover-preview { width: 100px; border-radius: 6px; display: block; margin-top: 10px; border: 1px solid var(--border); }
        .btn-submit { background: var(--accent); color: var(--bg); border: none; padding: 14px 24px; font-weight: 600; border-radius: 8px; cursor: pointer; font-size: 15px; width: 100%; transition: 0.2s; }
        .btn-submit:hover { opacity: 0.8; }

        /* DAFTAR BAB */
        .chapter-list { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; width: 100%; }
        .chapter-header { padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.02); flex-wrap: wrap; gap: 10px; }
        .chapter-item { padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; transition: 0.2s; gap: 10px; }
        .chapter-item:hover { background: var(--bg); }
        .chapter-item:last-child { border-bottom: none; }
        .btn-sm { padding: 10px 15px; background: transparent; color: var(--text); border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: 0.2s; text-align: center; white-space: nowrap; }
        .btn-sm:hover { background: var(--bg); }
        .btn-primary { background: var(--accent); color: var(--bg); border: none; }
        .btn-primary:hover { background: var(--text); color: var(--bg); }

        /* RESPONSIF HP */
        .hamburger-btn { display: none; background: none; border: none; color: var(--text); font-size: 26px; cursor: pointer; padding: 0; line-height: 1; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 998; opacity: 0; transition: opacity 0.3s ease; }
        
        @media (max-width: 900px) {
            .manager-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            aside { position: fixed; top: 0; left: -300px; height: 100vh; z-index: 999; box-shadow: 4px 0 15px rgba(0,0,0,0.5); transition: left 0.3s ease; }
            aside.show { left: 0; }
            .hamburger-btn { display: block; }
            .sidebar-overlay.show { display: block; opacity: 1; }

            main { padding: 20px; }
            .header { align-items: flex-start; flex-direction: column; }
            .header-left h1 { font-size: 20px; }
            
            .form-card { padding: 20px; }
            .chapter-header { flex-direction: column; align-items: stretch; }
            .chapter-header .btn-sm { width: 100%; }
            .chapter-item { flex-direction: column; align-items: stretch; text-align: left; }
            .chapter-item .btn-sm { width: 100%; }
        }
    </style>
</head>
<body>

    <aside>
        <a href="/" style="font-size: 22px; font-weight: 900; margin-bottom: 10px; text-decoration: none; color: inherit; display: block;">NOVELKU.</a>
        <nav style="display:flex; flex-direction:column; gap:5px; margin-top: 20px;">
            <a href="{{ route('dashboard') }}" class="nav-link">📊 Dashboard</a>
            <a href="#" class="nav-link active">📚 Manajemen Karya</a>
            <a href="{{ route('novel.create') }}" class="nav-link">✍️ Tambah Novel</a>
        </nav>
    </aside>

    <main>
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <div class="header">
            <div class="header-left">
                <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
                <div>
                    <h1>Manajemen Novel</h1>
                    <p>Pengaturan penuh: <strong>{{ $novel->title }}</strong></p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-back">&larr; Kembali ke Dashboard</a>
        </div>

        <div class="manager-grid">
            <div class="form-card">
                <form action="{{ route('novel.update', $novel->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Judul Novel</label>
                        <input type="text" name="title" value="{{ $novel->title }}" required>
                    </div>
                    <div class="form-group">
                        <label>Sinopsis</label>
                        <textarea name="synopsis" required>{{ $novel->synopsis }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Status Publikasi</label>
                        <select name="status" required>
                            <option value="ongoing" {{ $novel->status == 'ongoing' ? 'selected' : '' }}>Sedang Berjalan (Ongoing)</option>
                            <option value="completed" {{ $novel->status == 'completed' ? 'selected' : '' }}>Tamat (Completed)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ganti Cover (Opsional)</label>
                        <input type="file" name="cover_image" accept="image/*" style="border: none; padding: 0;">
                        @if($novel->cover_image)
                            <img src="{{ asset('storage/' . $novel->cover_image) }}" class="cover-preview" alt="Cover">
                        @endif
                    </div>
                    <button type="submit" class="btn-submit" onclick="this.innerHTML='Menyimpan...'; this.style.opacity='0.7';">
    Simpan Perubahan Novel
</button>
                </form>
            </div>

            <div class="chapter-list">
                <div class="chapter-header">
                    <h3 style="margin:0; font-size: 16px;">Daftar Bab ({{ $chapters->count() }})</h3>
                    <a href="{{ route('chapter.create') }}" class="btn-sm btn-primary">+ Tambah Bab Baru</a>
                </div>
                @forelse ($chapters as $chapter)
                    <div class="chapter-item">
                        <div>
                            <div style="font-weight: 600; font-size: 15px;">Bab {{ $chapter->chapter_number }}: {{ $chapter->title }}</div>
                            <div style="font-size: 12px; color: var(--muted);">Dibuat: {{ $chapter->created_at->format('d M Y') }}</div>
                        </div>
                        <a href="{{ route('chapter.edit', $chapter->id) }}" class="btn-sm">Edit Teks</a>
                    </div>
                @empty
                    <div style="padding: 30px; text-align: center; color: var(--muted);">Belum ada bab yang dirilis.</div>
                @endforelse
            </div>
        </div>
    </main>
    
    <script>
        function toggleSidebar() {
            document.querySelector('aside').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
        
    // Cek apakah ada session success dari Laravel
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            // Mode Gelap otomatis sesuai tema
            background: getComputedStyle(document.documentElement).getPropertyValue('--card').trim() || '#fff',
            color: getComputedStyle(document.documentElement).getPropertyValue('--text').trim() || '#000'
        });
    @endif

    // Cek juga kalau ada error validasi
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Waduh...',
            text: 'Ada yang salah nih, cek inputan lu lagi der!',
            background: getComputedStyle(document.documentElement).getPropertyValue('--card').trim() || '#fff',
            color: getComputedStyle(document.documentElement).getPropertyValue('--text').trim() || '#000'
        });
    @endif

    </script>
</body>
</html>