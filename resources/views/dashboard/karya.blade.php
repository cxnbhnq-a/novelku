<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karya Saya - NovelKu</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; overflow-x: hidden; }
        * { box-sizing: border-box; }
        
        aside { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; flex-shrink: 0; position: fixed; height: 100vh; z-index: 1000; }
        .nav-link { padding: 12px 15px; border-radius: var(--radius); text-decoration: none; color: var(--text); font-weight: 500; transition: 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover { background: var(--bg); border: 1px solid var(--border); }
        .nav-link.active { background: var(--bg); border: 1px solid var(--border); font-weight: 700; }
        
        .avatar-box { width: 100px; height: 100px; border-radius: 50%; border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: bold; background: var(--bg); overflow: hidden; margin: 0 auto 10px; }
        .avatar-box img { width: 100%; height: 100%; object-fit: cover; }
        
        main { flex: 1; padding: 40px; margin-left: 250px; width: calc(100% - 250px); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title { margin: 0; font-size: 24px; font-weight: 800; }
        
        .btn-primary { padding: 10px 20px; background: var(--text); color: var(--bg); border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-outline { padding: 8px 15px; background: transparent; color: var(--text); border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: 0.2s; text-align: center; }
        .btn-danger { color: #dc3545; border-color: #dc3545; }
        .btn-danger:hover { background: #dc3545; color: #fff; }

        .karya-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .karya-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; display: flex; flex-direction: column; gap: 15px; }
        
        .karya-header { display: flex; gap: 15px; }
        .karya-thumb { width: 80px; height: 110px; background: var(--accent); color: var(--bg); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 900; flex-shrink: 0; overflow: hidden; }
        .karya-thumb img { width: 100%; height: 100%; object-fit: cover; }
        
        .karya-info { flex: 1; display: flex; flex-direction: column; }
        .karya-linktxt { text-decoration: none; color: var(--text); display: flex; flex-direction: column; gap: 12px; }
        .karya-title { margin: 0 0 5px; font-size: 16px; font-weight: 700; }
        .karya-meta { font-size: 12px; color: var(--muted); margin-bottom: 5px; }
        
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; width: fit-content; }
        .badge-published { background: #d1e7dd; color: #0f5132; }
        .badge-draft { background: var(--bg); color: var(--muted); border: 1px solid var(--border); }

        .karya-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: auto; border-top: 1px solid var(--border); padding-top: 15px; }
        .full-width { grid-column: span 2; }
    </style>
</head>
<body>

    <aside>
        <a href="/" style="font-size: 22px; font-weight: 900; margin-bottom: 10px; text-decoration: none; color: inherit;">NOVELKU.</a>
        <div style="text-align: center;">
            <div class="avatar-box">
                @if (Auth::user()->profile_picture)
                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Foto">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </div>
            <div style="font-weight: 700;">{{ Auth::user()->name }}</div>
            <div style="font-size: 11px; color: var(--muted);">PENULIS</div>
        </div>
        
        <nav style="display:flex; flex-direction:column; gap:5px; margin-top: 20px;">
            <a href="{{ route('dashboard') }}" class="nav-link">📊 Dashboard</a>
            <a href="{{ route('karya.saya') }}" class="nav-link active">📚 Karya Saya</a>
            <a href="{{ route('novel.create') }}" class="nav-link">✍️ Tambah Novel</a>
            <a href="{{ route('profile.edit') }}" class="nav-link">⚙️ Edit Profil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link" style="width: 100%; border: none; background: transparent; cursor: pointer; color: #dc3545;">🚪 Keluar</button>
            </form>
        </nav>
    </aside>

    <main>
        <div class="header">
            <h1 class="page-title">📚 Karya Saya</h1>
            <a href="{{ route('novel.create') }}" class="btn-primary">✍️ Buat Novel Baru</a>
        </div>

        <div class="karya-grid">
            @forelse ($novels as $novel)
                <div class="karya-card">
                    <div class="karya-header">
                        <div class="karya-thumb">
                            @if($novel->cover_image)
                                <img src="{{ asset('storage/' . $novel->cover_image) }}" alt="Cover">
                            @else
                                {{ strtoupper(substr($novel->title, 0, 1)) }}
                            @endif
                        </div>
                        
                        <div class="karya-info">
                            <a href="{{ route('novel.show', $novel->id) }}" class="karya-linktxt">
                            <h3 class="karya-title">{{ $novel->title }}</h3>
                            <div class="karya-meta">
                                👁️ {{ number_format($novel->views ?? 0) }} kali dibaca
                                <div style="display:flex; gap:4px; margin-top:4px; flex-wrap:wrap;">
                                    @foreach($novel->genres as $genre)
                                        <span style="font-size: 9px; background: var(--bg); border: 1px solid var(--border); padding: 1px 5px; border-radius: 3px;">#{{ $genre->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <span class="badge {{ in_array($novel->status, ['published', 'ongoing']) ? 'badge-published' : 'badge-draft' }}">
                                {{ strtoupper($novel->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="karya-actions">
                        <a href="{{ route('chapter.create', ['novel_id' => $novel->id]) }}" class="btn-outline full-width">➕ Kelola Bab</a>
                        <a href="{{ route('novel.edit', $novel->id) }}" class="btn-outline">✏️ Edit</a>

                        <form action="{{ route('novel.destroy', $novel->id) }}" method="POST" id="delete-form-{{ $novel->id }}" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>

                        <button type="button" class="btn-outline btn-danger btn-delete" data-id="{{ $novel->id }}">
                        🗑️ Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background: var(--card); border: 1px dashed var(--border); border-radius: var(--radius);">
                    <h3>Belum ada karya nih. Yuk mulai nulis!</h3>
                    <a href="{{ route('novel.create') }}" class="btn-primary" style="margin-top: 15px;">Mulai Nulis Novel</a>
                </div>
            @endforelse
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Ambil ID secara aman dari atribut data
                const id = this.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Yakin mau hapus?',
                    text: "Karya ini dan seluruh bab di dalamnya akan lenyap permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    background: getComputedStyle(document.documentElement).getPropertyValue('--card').trim() || '#ffffff',
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text').trim() || '#000000'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Selector di-escape otomatis oleh getElementById, aman dari DOM based injection
                        const form = document.getElementById('delete-form-' + id);
                        if(form) form.submit();
                    }
                });
            });
        });
    });
</script>
</body>
</html>
