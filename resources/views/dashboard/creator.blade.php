<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kreator - NovelKu</title>
    <style>
        :root {
            --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d;
            --border: #e0e0e0; --accent: #111111; --radius: 12px;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0;
                --border: #222222; --accent: #ffffff;
            }
        }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        /* SIDEBAR */
        aside { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; flex-shrink: 0;}
        .nav-link { padding: 12px 15px; border-radius: var(--radius); text-decoration: none; color: var(--text); font-weight: 500; transition: 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--bg); border: 1px solid var(--border); }
        
        /* MAIN CONTENT */
        main { flex: 1; padding: 40px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card { background: var(--card); border: 1px solid var(--border); padding: 24px; border-radius: var(--radius); }
        
        /* STATISTIK ANALISA */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { display: flex; align-items: center; gap: 15px; padding: 20px; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); }
        .stat-icon { font-size: 32px; background: var(--bg); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 1px solid var(--border); }
        .stat-info h3 { margin: 0; font-size: 24px; font-weight: 800; }
        .stat-info p { margin: 0; font-size: 14px; color: var(--muted); font-weight: 500; }

        /* LIST NOVEL TERBARU */
        .section-title { font-size: 18px; font-weight: 700; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .btn-sm { padding: 8px 15px; background: var(--accent); color: var(--bg); border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: 0.2s; }
        .btn-sm:hover { opacity: 0.8; }
        .btn-outline { background: transparent; color: var(--text); border: 1px solid var(--border); }
        .btn-outline:hover { background: var(--bg); }
        
        .novel-list { display: flex; flex-direction: column; gap: 15px; }
        .novel-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); }
        .novel-meta h4 { margin: 0 0 5px; font-size: 16px; }
        .novel-meta p { margin: 0; font-size: 13px; color: var(--muted); }
        .novel-actions { display: flex; gap: 10px; }

        /* CSS Avatar & Menu Edit */
        .avatar-wrapper { position: relative; display: inline-block; margin: 0 auto 15px; }
        .avatar-box { width: 100px; height: 100px; border-radius: 50%; border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: bold; background: var(--bg); overflow: hidden; margin: 0; }
        .avatar-box img { width: 100%; height: 100%; object-fit: cover; }
        .edit-pp-btn { position: absolute; bottom: 0; right: 0; background: var(--card); border: 1px solid var(--border); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: var(--text); padding: 0; }
        .edit-pp-btn:hover { background: var(--bg); transform: scale(1.05); }
        .pp-menu { display: none; position: absolute; bottom: -70px; right: -50px; background: var(--card); border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 100; flex-direction: column; overflow: hidden; min-width: 150px; }
        .pp-menu.show { display: flex; }
        .pp-menu-item { padding: 10px 15px; cursor: pointer; display: block; text-align: left; border: none; background: none; width: 100%; font-size: 14px; color: var(--text); font-family: inherit; transition: 0.2s; }
        .pp-menu-item:hover { background: var(--bg); }
        .text-danger { color: #dc3545; font-weight: 600; }

        /* Modal Hapus */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(2px); opacity: 0; transition: opacity 0.2s ease; }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content { background: var(--card); padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); width: 90%; max-width: 320px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2); transform: translateY(20px); transition: transform 0.2s ease; }
        .modal-overlay.show .modal-content { transform: translateY(0); }
        .modal-title { font-size: 18px; font-weight: 700; margin: 0 0 10px; color: var(--text); }
        .modal-text { color: var(--muted); font-size: 14px; margin: 0 0 20px; line-height: 1.5; }
        .modal-actions { display: flex; gap: 10px; justify-content: center; }
        .btn-cancel { padding: 10px 15px; border-radius: 8px; cursor: pointer; flex: 1; border: 1px solid var(--border); background: var(--bg); color: var(--text); font-weight: 600; transition: 0.2s; }
        .btn-cancel:hover { filter: brightness(0.9); }
        .btn-danger { padding: 10px 15px; border-radius: 8px; cursor: pointer; flex: 1; border: none; background: #dc3545; color: white; font-weight: 600; transition: 0.2s; }
        .btn-danger:hover { background: #c82333; }

        /* TOAST NOTIFICATION */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; pointer-events: none; }
        .toast { min-width: 250px; padding: 15px 20px; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.15); transform: translateX(120%); transition: transform 0.4s ease, opacity 0.4s ease; opacity: 0; }
        .toast.show { transform: translateX(0); opacity: 1; pointer-events: auto; }
        .toast.success { background-color: #198754; border-left: 6px solid #146c43; }
        .toast.error { background-color: #dc3545; border-left: 6px solid #b02a37; }
    
        /* --- TAMBAHAN CSS RESPONSIF HP --- */
        .hamburger-btn { display: none; background: none; border: none; color: var(--text); font-size: 28px; cursor: pointer; padding: 0; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 998; opacity: 0; transition: opacity 0.3s ease; }
        
        @media (max-width: 768px) {
            /* Sembunyikan Sidebar, jadikan melayang */
            aside { 
                position: fixed; top: 0; left: -300px; /* Sembunyi di kiri */
                height: 100vh; z-index: 999; box-shadow: 4px 0 15px rgba(0,0,0,0.5);
                transition: left 0.3s ease;
            }
            /* Class untuk nampilin sidebar */
            aside.show { left: 0; }
            
            /* Tampilkan tombol dan overlay */
            .hamburger-btn { display: block; }
            .sidebar-overlay.show { display: block; opacity: 1; }

            /* Rapiin konten utama di HP */
            main { padding: 20px; }
            .header { flex-wrap: wrap; gap: 15px; }
            
            /* Khusus card/grid biar gak gepeng */
            .manager-grid, .row-group, .stats-grid { display: flex; flex-direction: column; gap: 15px; }
            .form-card { padding: 20px; }
        }
    </style>
</head>
<body>

    @php
        $user = Auth::user();
        $inisial = strtoupper(substr($user->name, 0, 1));
        $profile_pic = $user->profile_picture ? asset('storage/' . $user->profile_picture) : null;
    @endphp

    <aside>
        <a href="/" style="font-size: 22px; font-weight: 900; margin-bottom: 10px; text-decoration: none; color: inherit; display: block;">NOVELKU.</a>
        
        <div class="card" style="text-align: center; padding: 20px 10px;">
            <div class="avatar-wrapper">
                <div class="avatar-box" id="avatarDisplay">
                    @if ($profile_pic)
                        <img src="{{ $profile_pic }}" alt="Foto Profil">
                    @else
                        {{ $inisial }}
                    @endif
                </div>
                
                <button type="button" class="edit-pp-btn" onclick="togglePpMenu()" title="Edit Foto Profil">✏️</button>
                
                <div id="ppMenu" class="pp-menu">
                    <label for="ppUpload" class="pp-menu-item" style="margin:0;">🖼️ Ganti Foto</label>
                    
                    @if ($profile_pic)
                    <form id="deletePpForm" action="{{ route('profile.picture.delete') }}" method="POST" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="pp-menu-item text-danger" onclick="openDeleteModal()">🗑️ Hapus Foto</button>
                    </form>
                    @endif
                </div>
            </div>
            
            <div style="font-weight: 700;">{{ $user->name }}</div>
            <div style="font-size: 12px; color: var(--muted);">Penulis (Creator)</div>

            <form id="ppForm" action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="file" id="ppUpload" name="profile_picture" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="document.getElementById('ppForm').submit();">
            </form>
        </div>
        
        <nav style="display:flex; flex-direction:column; gap:5px;">
            <a href="{{ route('dashboard') }}" class="nav-link active">📊 Dashboard</a>
            <a href="{{ route('karya.saya') }}" class="nav-link">📚 Karya Saya</a>
            <a href="{{ route('novel.create') }}" class="nav-link">✍️ Tambah Novel</a>
            
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="nav-link" style="width: 100%; border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: inherit; text-align: left;">
                    🚪 Keluar
                </button>
            </form>
        </nav>
    </aside>

    <main>
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <div class="header">
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
                <div>
                    <h1 style="margin:0;">Halo, {{ $user->name }}!</h1>
                    <p style="color:var(--muted); margin:5px 0 0;">Berikut adalah ringkasan analitik ceritamu.</p>
                </div>
            </div>
            <a href="{{ route('novel.create') }}" class="btn-sm" style="padding: 12px 20px; font-size: 14px;">Buat Novel</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <div class="stat-info">
                    <h3>{{ $totalKarya }}</h3>
                    <p>Total Karya</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👀</div>
                <div class="stat-info">
                    <h3>{{ number_format($totalDilihat) }}</h3>
                    <p>Total Dilihat</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-info">
                    <h3>{{ number_format($totalBookmark) }}</h3>
                    <p>Total Bookmark</p>
                </div>
            </div>
        </div>

        <div class="section-title">
            <span>Karya Terakhir Diperbarui</span>
            <a href="{{ route('karya.saya') }}" style="font-size: 14px; color: var(--muted); text-decoration: none;">Lihat Semua &rarr;</a>
        </div>

        <div class="novel-list">
            @forelse ($novels as $novel)
                <div class="novel-item">
                    <div class="novel-meta">
                        <h4>{{ $novel->title }}</h4>
                        <p>Dibuat: {{ $novel->created_at->diffForHumans() }} &bull; Status: {{ ucfirst($novel->status) }}</p>
                    </div>
                    <div class="novel-actions">
                        <a href="{{ route('novel.show', $novel->id) }}" class="btn-sm btn-outline" target="_blank" title="Preview">👁️ Preview</a>
                        <a href="{{ route('chapter.create', ['novel' => $novel->id]) }}" class="btn-sm btn-outline">Tambah Bab</a>
                        <a href="{{ route('novel.edit', $novel->id) }}" class="btn-sm btn-outline">Edit Novel</a>
                    </div>
                </div>
            @empty
                <div class="card" style="text-align: center; padding: 40px; color: var(--muted);">
                    <p style="margin-bottom: 15px;">Kamu belum punya karya apa-apa nih.</p>
                    <a href="{{ route('novel.create') }}" class="btn-sm" style="text-decoration: none;">Mulai Tulis Novel Pertamamu</a>
                </div>
            @endforelse
        </div>
    </main> 

    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="modal-title">Hapus Foto Profil?</h3>
            <p class="modal-text">Foto profilmu akan dihapus secara permanen dan diganti dengan inisial nama. Lanjutkan?</p>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <button class="btn-danger" onclick="submitDelete()">Ya, Hapus</button>
            </div>
        </div>
    </div>

    @if (session('success') || session('error'))
        <div class="toast-container">
            <div id="appToast" class="toast {{ session('success') ? 'success' : 'error' }} show">
                <span class="toast-icon">{{ session('success') ? '✅' : '⚠️' }}</span>
                <span>{{ session('success') ?? session('error') }}</span>
            </div>
        </div>
    @endif

    <script>
        // Logika Responsif Sidebar
        function toggleSidebar() {
            document.querySelector('aside').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Logika Dropdown Menu PP
        function togglePpMenu() { document.getElementById('ppMenu').classList.toggle('show'); }
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('ppMenu');
            const btn = document.querySelector('.edit-pp-btn');
            if (menu && btn && !menu.contains(event.target) && !btn.contains(event.target)) {
                menu.classList.remove('show');
            }
        });

        // Logika Modal Hapus
        const deleteModal = document.getElementById('deleteModal');
        function openDeleteModal() { document.getElementById('ppMenu').classList.remove('show'); deleteModal.classList.add('show'); }
        function closeDeleteModal() { deleteModal.classList.remove('show'); }
        function submitDelete() {
            event.target.innerText = "Menghapus...";
            event.target.style.opacity = "0.7";
            event.target.disabled = true;
            document.getElementById('deletePpForm').submit();
        }
        deleteModal.addEventListener('click', function(event) { if (event.target === deleteModal) closeDeleteModal(); });

        // Auto Hide Toast Laravel
        @if (session('success') || session('error'))
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('appToast');
            setTimeout(() => { toast.classList.remove('show'); }, 3500);
        });
        @endif
    </script>
</body>
</html>