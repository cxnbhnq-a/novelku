<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Koleksi Saya - NovelKu</title>
    
    <style>
        /* TEMA NOVELKU */
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }

        /* --- HEADER --- */
        .header { display: flex; justify-content: space-between; align-items: center; padding: 15px 5%; background: var(--card); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; flex-wrap: wrap; gap: 15px; }
        .logo { font-size: 22px; font-weight: 900; color: var(--text); text-decoration: none; letter-spacing: -0.5px; }
    
        /* DROPDOWN & AVATAR */
        .profile-menu { position: relative; display: inline-block; }
        .profile-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--text); color: var(--bg); display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer; border: 2px solid transparent; transition: 0.2s; overflow: hidden; }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .dropdown-content { display: none; position: absolute; right: 0; top: 50px; background-color: var(--card); min-width: 260px; box-shadow: 0px 8px 20px rgba(0,0,0,0.15); z-index: 101; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; padding-bottom: 10px; }
        .dropdown-content.show { display: block; }
        .dropdown-item { color: var(--text); padding: 12px 16px; text-decoration: none; display: block; font-size: 14px; border-bottom: 1px solid var(--border); transition: 0.2s; cursor: pointer; text-align: left; width: 100%; background: none; border-top: none; border-left: none; border-right: none; font-family: inherit; }
        .dropdown-item:hover { background-color: var(--bg); }
        .text-danger { color: #dc3545 !important; font-weight: 600; }
        .dropdown-header-card { padding: 20px 15px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; border-bottom: 1px solid var(--border); }
        .avatar-wrapper { position: relative; display: inline-block; margin-bottom: 15px; }
        .avatar-box { width: 80px; height: 80px; border-radius: 50%; border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; background: var(--bg); overflow: hidden; margin: 0; }
        .avatar-box img { width: 100%; height: 100%; object-fit: cover; }
        .edit-pp-btn { position: absolute; bottom: 0; right: 0; background: var(--card); border: 1px solid var(--border); border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: var(--text); padding: 0; }
        .edit-pp-btn:hover { background: var(--bg); transform: scale(1.05); }
        .pp-menu { display: none; position: absolute; bottom: -70px; right: -50px; background: var(--card); border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 100; flex-direction: column; overflow: hidden; min-width: 150px; }
        .pp-menu.show { display: flex; }
        .pp-menu-item { padding: 10px 15px; cursor: pointer; display: block; text-align: left; border: none; background: none; width: 100%; font-size: 14px; color: var(--text); font-family: inherit; transition: 0.2s; text-decoration: none; }
        .pp-menu-item:hover { background: var(--bg); }

        /* --- KONTEN KOLEKSI --- */
        .container { padding: 40px 5%; max-width: 1200px; margin: 0 auto; }
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .page-title { font-size: 24px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 10px; }
        .btn-back { color: var(--muted); text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.2s; }
        .btn-back:hover { color: var(--text); }

        /* GRID RAK BUKU */
        .collection-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
        
        .collection-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; display: flex; flex-direction: column; gap: 15px; transition: 0.2s; position: relative; overflow: hidden; }
        .collection-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        
        .btn-remove { position: absolute; top: 15px; right: 15px; background: var(--bg); border: 1px solid var(--border); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--muted); transition: 0.2s; font-size: 14px; }
        .btn-remove:hover { color: #dc3545; border-color: #dc3545; background: #fff0f0; }

        .book-info { display: flex; gap: 15px; align-items: stretch; }
        .book-thumb { width: 75px; height: 110px; background: var(--text); color: var(--bg); border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 24px; }
        .book-details { display: flex; flex-direction: column; justify-content: center; }
        .book-title { margin: 0 0 5px; font-size: 16px; font-weight: 700; line-height: 1.3; }
        .book-author { margin: 0 0 8px; font-size: 13px; color: var(--muted); }
        .book-tag { display: inline-block; background: var(--bg); border: 1px solid var(--border); font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 600; align-self: flex-start; }

        /* PROGRESS BACA */
        .progress-container { margin-top: auto; }
        .progress-text { display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--muted); }
        .progress-bar-bg { width: 100%; height: 6px; background: var(--border); border-radius: 10px; overflow: hidden; }
        .progress-bar-fill { height: 100%; background: var(--text); border-radius: 10px; }

        .btn-continue { width: 100%; padding: 10px; background: var(--text); color: var(--bg); border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 10px; text-align: center; text-decoration: none; display: inline-block; }
        .btn-continue:hover { opacity: 0.8; }

        /* --- MODAL HAPUS CSS --- */
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

        /* --- TOAST NOTIFICATION CSS --- */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; pointer-events: none; }
        .toast { min-width: 250px; padding: 15px 20px; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.15); transform: translateX(120%); transition: transform 0.4s ease, opacity 0.4s ease; opacity: 0; }
        .toast.show { transform: translateX(0); opacity: 1; pointer-events: auto; }
        .toast.success { background-color: #198754; border-left: 6px solid #146c43; }
        .toast.error { background-color: #dc3545; border-left: 6px solid #b02a37; }

        @media (max-width: 768px) {
            .search-wrapper { order: 3; flex-basis: 100%; max-width: 100%; margin-top: 5px; }
            .dropdown-content { right: -20px; }
            .collection-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="header">
    <a href="/" class="logo">NOVELKU.</a>
    

    
    <div class="profile-menu">
        <div class="profile-avatar" onclick="toggleDropdown()">
            @if(auth()->user()->profile_picture)
                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Avatar">
            @else
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            @endif
        </div>
        
        <div class="dropdown-content" id="profileDropdown">
            <div class="dropdown-header-card">
                <div class="avatar-wrapper">
                    <div class="avatar-box">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <button type="button" class="edit-pp-btn" onclick="togglePpMenu(event)" title="Edit Foto Profil">✏️</button>
                    <div id="ppMenu" class="pp-menu">
                        <label for="inputFotoProfil" class="pp-menu-item" style="margin:0;">🖼️ Ganti Foto</label>
                        @if(auth()->user()->profile_picture)
                        <form id="deletePpForm" action="{{ route('profile.picture.delete') }}" method="POST" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="pp-menu-item text-danger" onclick="openDeleteModal()">🗑️ Hapus Foto</button>
                        </form>
                        @endif
                    </div>
                </div>
                <strong style="display:block; font-size: 14px;">{{ auth()->user()->name }}</strong>
                <span style="font-size: 12px; color: var(--muted);">{{ auth()->user()->email }}</span>
            </div>

            <a href="{{ route('dashboard') }}" class="dropdown-item">🏠 Dashboard Utama</a>
            <a href="#" class="dropdown-item" style="background: var(--bg); font-weight: bold;">📚 Koleksi Saya</a>
            
            <form id="formFotoProfil" action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="file" id="inputFotoProfil" name="profile_picture" accept=".jpg, .jpeg, .png, .gif, .webp" onchange="document.getElementById('formFotoProfil').submit();">
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger" style="border-bottom: none;">🚪 Keluar</button>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <div class="page-header">
        <h2 class="page-title">📚 Koleksi Novel Saya</h2>
        <a href="{{ route('dashboard') }}" class="btn-back">&larr; Kembali ke Dashboard</a>
    </div>

   <div class="collection-grid" id="collectionList">
    @forelse($bookmarks as $b)
        <div class="collection-card">
            <form action="{{ route('novel.unbookmark', $b->novel_id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-remove" title="Hapus dari Koleksi">✖</button>
            </form>

            <div class="book-info">
                <div class="book-thumb">
                    @if($b->novel->cover_image)
                        <img src="{{ asset('storage/'.$b->novel->cover_image) }}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
                    @else
                        {{ strtoupper(substr($b->novel->title, 0, 1)) }}
                    @endif
                </div>
                <div class="book-details">
                    <h4 class="book-title">{{ $b->novel->title }}</h4>
                    <p class="book-author">{{ $b->novel->creator->name }}</p>
                    <span class="book-tag">Novel Terpilih</span>
                </div>
            </div>
            
            <div class="progress-container">
                <div class="progress-text">
                    <span>Status: {{ ucfirst($b->novel->status) }}</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: 100%;"></div>
                </div>
            </div>
            
            <a href="{{ route('novel.show', $b->novel->id) }}" class="btn-continue">Lihat Detail</a>
        </div>
    @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
            <p style="color: var(--muted);">Koleksimu masih kosong. Yuk cari novel seru!</p>
            <a href="{{ route('novel.explore') }}" class="btn-continue" style="width: auto;">Jelajahi Novel</a>
        </div>
    @endforelse
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
const collections = [
    { title: "Bayang di Balik Terali", author: "Ahmad Reza", genre: "Misteri", chapter: "Bab 14", progress: 65 },
    { title: "Kode Biner Sang Peretas", author: "N. Albana", genre: "Sci-Fi", chapter: "Bab 22", progress: 80 },
    { title: "Senja di Ujung Halmahera", author: "Siti Nurhaliza", genre: "Romansa", chapter: "Bab 5", progress: 15 },
    { title: "Detektif Partikelir", author: "Rina Nose", genre: "Thriller", chapter: "Bab 1", progress: 0 }
];

function renderCollections() {
    const el = document.getElementById("collectionList");
    el.innerHTML = collections.map(n => `
        <div class="collection-card">
            <button class="btn-remove" title="Hapus dari Koleksi">✖</button>
            <div class="book-info">
                <div class="book-thumb">${n.title[0]}</div>
                <div class="book-details">
                    <h4 class="book-title">${n.title}</h4>
                    <p class="book-author">${n.author}</p>
                    <span class="book-tag">${n.genre}</span>
                </div>
            </div>
            <div class="progress-container">
                <div class="progress-text">
                    <span>Terakhir: ${n.chapter}</span>
                    <span>${n.progress}%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: ${n.progress}%;"></div>
                </div>
            </div>
            <a href="#" class="btn-continue">${n.progress === 0 ? 'Mulai Baca' : 'Lanjutkan Baca'}</a>
        </div>
    `).join("");
}

function toggleDropdown() { document.getElementById("profileDropdown").classList.toggle("show"); }
function togglePpMenu(event) { event.stopPropagation(); document.getElementById('ppMenu').classList.toggle('show'); }

window.onclick = function(event) {
    if (!event.target.closest('.profile-menu') && !event.target.closest('.modal-content')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) { dropdowns[i].classList.remove('show'); }
    }
    const menuPp = document.getElementById('ppMenu');
    const btnPp = document.querySelector('.edit-pp-btn');
    if (menuPp && btnPp && !menuPp.contains(event.target) && !btnPp.contains(event.target)) { menuPp.classList.remove('show'); }
}

const deleteModal = document.getElementById('deleteModal');
function openDeleteModal() { document.getElementById('ppMenu').classList.remove('show'); document.getElementById("profileDropdown").classList.remove("show"); deleteModal.classList.add('show'); }
function closeDeleteModal() { deleteModal.classList.remove('show'); }
function submitDelete() { event.target.innerText = "Menghapus..."; event.target.disabled = true; document.getElementById('deletePpForm').submit(); }
deleteModal.addEventListener('click', function(event) { if (event.target === deleteModal) closeDeleteModal(); });

// LOGIKA TOAST AUTO-HIDE
@if (session('success') || session('error'))
document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('appToast');
    setTimeout(() => { toast.classList.remove('show'); }, 3500);
});
@endif

renderCollections();
</script>

</body>
</html>