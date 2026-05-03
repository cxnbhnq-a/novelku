<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Reader - NovelKu</title>
    
    <style>
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }

        .header { display: flex; justify-content: space-between; align-items: center; padding: 15px 5%; background: var(--card); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; flex-wrap: wrap; gap: 15px; }
        .logo { font-size: 22px; font-weight: 900; color: var(--text); text-decoration: none; letter-spacing: -0.5px; }
        
        .search-wrapper { position: relative; width: 100%; max-width: 400px; margin: 0 auto; flex: 1; min-width: 200px; }
        .search { width: 100%; padding: 10px 15px 10px 40px; border: 1px solid var(--border); border-radius: 50px; background: var(--bg); color: var(--text); font-family: inherit; font-size: 14px; outline: none; transition: 0.3s; }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 14px; color: var(--text); pointer-events: none; }
        
        /* PROFILE AREA */
        .profile-menu { position: relative; display: inline-block; }
        .profile-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--text); color: var(--bg); display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer; overflow: hidden; border: 1px solid var(--border); }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        
        .edit-pp-btn { position: absolute; bottom: -2px; right: -2px; background: var(--card); border: 1px solid var(--border); border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 10px; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: var(--text); z-index: 10; }
        
        .pp-menu { display: none; position: absolute; top: 45px; right: 0; background: var(--card); border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 110; flex-direction: column; overflow: hidden; min-width: 150px; }
        .pp-menu.show { display: flex; }
        .pp-menu-item { padding: 10px 15px; cursor: pointer; display: block; border: none; background: none; width: 100%; font-size: 13px; color: var(--text); font-family: inherit; transition: 0.2s; border-bottom: 1px solid var(--border); text-align: left;}
        .pp-menu-item:hover { background: var(--bg); }
        
        .dropdown-content { display: none; position: absolute; right: 0; top: 50px; background-color: var(--card); min-width: 260px; box-shadow: 0px 8px 20px rgba(0,0,0,0.15); z-index: 101; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; padding-bottom: 10px; }
        .dropdown-content.show { display: block; }
        .dropdown-header-card { padding: 20px 15px; text-align: center; border-bottom: 1px solid var(--border); display: flex; flex-direction: column; align-items: center; }
        .avatar-box-large { width: 80px; height: 80px; border-radius: 50%; border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; background: var(--bg); overflow: hidden; margin-bottom: 10px; }
        .avatar-box-large img { width: 100%; height: 100%; object-fit: cover; }
        .dropdown-item { color: var(--text); padding: 12px 16px; text-decoration: none; display: block; font-size: 14px; border-bottom: 1px solid var(--border); transition: 0.2s; text-align: left; background: none; border: none; width: 100%; font-family: inherit; cursor: pointer; }
        .dropdown-item:hover { background-color: var(--bg); }

        /* MAIN LAYOUT */
        .container { display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px; padding: 30px 5%; max-width: 1200px; margin: 0 auto; align-items: start; }
        .section-title { font-size: 18px; font-weight: 800; margin: 0 0 20px; display: flex; align-items: center; gap: 8px; }

        .novel-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 15px; margin-bottom: 15px; display: flex; gap: 15px; transition: 0.2s; text-decoration: none; color: inherit; }
        .thumb { width: 80px; height: 110px; background: var(--text); color: var(--bg); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 24px; border-radius: 8px; flex-shrink: 0; overflow: hidden; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }
        .tag { background: var(--bg); border: 1px solid var(--border); font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 600; margin-right: 5px; color: var(--text); text-decoration: none; display: inline-block; }

        /* SIDEBAR STYLING (DI GAMBAR) */
        .sidebar { display: flex; flex-direction: column; gap: 20px; }
        .side-card { background: var(--card); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); }
        .side-card h4 { margin: 0 0 18px; font-size: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
        
        .list-item { display: flex; gap: 12px; align-items: center; margin-bottom: 15px; text-decoration: none; color: inherit; transition: 0.2s; }
        .list-item:hover { opacity: 0.8; }
        .avatar-sm { width: 45px; height: 45px; border-radius: 6px; background: var(--border); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; flex-shrink: 0; overflow: hidden; }
        .avatar-sm img { width: 100%; height: 100%; object-fit: cover; }
        .list-title { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
        .list-subtitle { font-size: 12px; color: var(--muted); }

        .genre-container { display: flex; flex-wrap: wrap; gap: 8px; }
        .genre-tag { padding: 6px 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; color: var(--text); }
        .genre-tag:hover { border-color: var(--muted); }

        /* MODAL */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
        .modal-overlay.show { display: flex; }
        .modal-content { background: var(--card); padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); width: 90%; max-width: 320px; text-align: center; }
        .btn-cancel { padding: 10px 15px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); color: var(--text); cursor: pointer; flex: 1; font-weight: 600; }
        .btn-danger { padding: 10px 15px; border-radius: 8px; border: none; background: #dc3545; color: white; cursor: pointer; flex: 1; font-weight: 600; }

        /* TOAST NOTIFICATION */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; pointer-events: none; }
        .toast { min-width: 250px; padding: 15px 20px; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.15); transform: translateX(120%); transition: transform 0.4s ease, opacity 0.4s ease; opacity: 0; }
        .toast.show { transform: translateX(0); opacity: 1; pointer-events: auto; }
        .toast.success { background-color: #198754; border-left: 6px solid #146c43; }
        .toast.error { background-color: #dc3545; border-left: 6px solid #b02a37; }
    
            @media (max-width: 768px) {
                .container { grid-template-columns: 1fr; }
                .search-wrapper { order: 3; flex-basis: 100%; }
            }
    </style>
</head>
<body>

@php
    $user = Auth::user();
    $inisial = strtoupper(substr($user->name, 0, 1));
    $profile_pic = $user->profile_picture ? asset('storage/' . $user->profile_picture) : null;
@endphp

<div class="header">
    <a href="/" class="logo">NOVELKU.</a>
    
    <div class="search-wrapper">
    <span class="search-icon">🔍</span>
    <form action="{{ route('novel.explore') }}" method="GET" style="margin: 0; width: 100%; display: flex;">
        <input type="text" name="search" class="search" placeholder="Cari judul novel..." value="{{ request('search') }}" autocomplete="off" required>
    </form>
</div>
    
    <div class="profile-menu">
        <div class="profile-avatar" onclick="toggleDropdown()">
            @if($profile_pic)
                <img src="{{ $profile_pic }}" alt="Avatar">
            @else
                {{ $inisial }}
            @endif
        </div>
        <button type="button" class="edit-pp-btn" onclick="togglePpMenu()" title="Edit Foto Profil">✏️</button>
        
        <div id="ppMenu" class="pp-menu">
            <label for="ppUpload" class="pp-menu-item">🖼️ Ganti Foto</label>
            @if ($profile_pic)
            <form id="deletePpForm" action="{{ route('profile.picture.delete') }}" method="POST">
                @csrf @method('DELETE')
                <button type="button" class="pp-menu-item" style="color:#dc3545; font-weight:600;" onclick="openDeleteModal()">🗑️ Hapus Foto</button>
            </form>
            @endif
        </div>

        <div class="dropdown-content" id="profileDropdown">
            <div class="dropdown-header-card">
                <div class="avatar-box-large">
                    {!! $profile_pic ? '<img src="'.$profile_pic.'">' : $inisial !!}
                </div>
                <strong style="font-size: 14px;">{{ $user->name }}</strong>
                <span style="font-size: 12px; color: var(--muted);">{{ ucfirst($user->role) }}</span>
            </div>
            <a href="{{ route('collection') }}" class="dropdown-item" style="background: var(--bg); font-weight: bold;">📚 Koleksi Saya</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item" style="color:#dc3545; font-weight:600;">🚪 Keluar</button>
            </form>
        </div>

        <form id="ppForm" action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="file" id="ppUpload" name="profile_picture" accept="image/*" onchange="document.getElementById('ppForm').submit();">
        </form>
    </div>
</div>

<div class="container">
    <div>
        <h3 class="section-title">✨ Rekomendasi Untukmu</h3>
        <div id="readingList">
            @forelse($recommendations as $novel)
                <a href="{{ route('novel.show', $novel->id) }}" class="novel-card">
                    <div class="thumb">
                        @if($novel->cover_image)
                            <img src="{{ asset('storage/'.$novel->cover_image) }}" alt="Cover">
                        @else
                            {{ substr($novel->title, 0, 1) }}
                        @endif
                    </div>
                    <div class="novel-info">
                        <div>
                            <h4 style="margin:0 0 5px;">{{ $novel->title }}</h4>
                            <p style="margin:0; font-size:13px; color:var(--muted);">Oleh: {{ $novel->creator->name }}</p>
                            <div style="margin-top:10px;">
                                @foreach($novel->genres->take(2) as $genre)
                                    <span class="tag">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <p>Belum ada novel yang tersedia.</p>
            @endforelse
        </div>
    </div>

    <div class="sidebar">
        <div class="side-card">
            <h4>🆕 Novel Terbaru</h4>
            @foreach($recommendations->take(4) as $n)
                <a href="{{ route('novel.show', $n->id) }}" class="list-item">
                    <div class="avatar-sm">
                        @if($n->cover_image)
                            <img src="{{ asset('storage/'.$n->cover_image) }}" alt="Thumb">
                        @else
                            {{ $n->title[0] }}
                        @endif
                    </div>
                    <div>
                        <div class="list-title">{{ $n->title }}</div>
                        <div class="list-subtitle">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="side-card">
            <h4>🔥 Genre Populer</h4>
            <div class="genre-container">
                @foreach($genres ?? [] as $g)
                    <a href="{{ route('novel.explore', ['genre' => $g->slug]) }}" class="genre-tag">{{ $g->name }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal-overlay">
    <div class="modal-content">
        <h3 style="margin:0 0 10px;">Hapus Foto Profil?</h3>
        <p style="color:var(--muted); font-size:14px; margin-bottom:20px;">Foto profilmu akan diganti dengan inisial.</p>
        <div style="display:flex; gap:10px;">
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
    function toggleDropdown() {
        document.getElementById("ppMenu").classList.remove("show");
        document.getElementById("profileDropdown").classList.toggle("show");
    }

    function togglePpMenu() { 
        document.getElementById("profileDropdown").classList.remove("show");
        document.getElementById('ppMenu').classList.toggle('show'); 
    }

    const deleteModal = document.getElementById('deleteModal');
    function openDeleteModal() { 
        document.getElementById('ppMenu').classList.remove('show'); 
        deleteModal.classList.add('show'); 
    }
    function closeDeleteModal() { deleteModal.classList.remove('show'); }
    function submitDelete() { document.getElementById('deletePpForm').submit(); }

    window.onclick = function(event) {
        if (!event.target.closest('.profile-menu') && !event.target.closest('.modal-content')) {
            document.getElementById("profileDropdown").classList.remove("show");
            document.getElementById("ppMenu").classList.remove("show");
        }
    }
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