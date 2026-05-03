<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - NovelKu</title>
    <style>
        /* TEMA NOVELKU (Light & Dark Mode) */
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        * { box-sizing: border-box; }
        
        /* SIDEBAR */
        aside { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; flex-shrink: 0; position: fixed; height: 100vh; z-index: 1000; transition: left 0.3s ease; }
        .logo-container { padding-bottom: 20px; border-bottom: 1px solid var(--border); margin-bottom: 10px; }
        .logo { font-size: 24px; font-weight: 900; color: var(--text); text-decoration: none; letter-spacing: -0.5px; }
        
        .nav-link { padding: 12px 15px; border-radius: 8px; text-decoration: none; color: var(--muted); font-weight: 600; font-size: 14px; transition: 0.2s; display: flex; align-items: center; gap: 12px; }
        .nav-link:hover { background: var(--bg); color: var(--text); }
        .nav-link.active { background: var(--text); color: var(--bg); }
        
        .sidebar-bottom { margin-top: auto; border-top: 1px solid var(--border); padding-top: 20px; }

        /* MAIN CONTENT */
        main { flex: 1; padding: 30px 40px; margin-left: 250px; overflow-y: auto; width: calc(100% - 250px); }
        
        /* HEADER */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
        .header-title { display: flex; align-items: center; gap: 15px; }
        .header-title h1 { margin: 0; font-size: 22px; font-weight: 800; }
        .hamburger-btn { display: none; background: none; border: none; color: var(--text); font-size: 24px; cursor: pointer; padding: 0; }
        
        /* DROPDOWN PROFIL */
        .profile-menu { position: relative; display: inline-block; }
        .profile-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--border); color: var(--text); display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer; transition: 0.2s; border: 1px solid var(--border); }
        .dropdown-content { display: none; position: absolute; right: 0; top: 50px; background-color: var(--card); min-width: 200px; box-shadow: 0px 8px 20px rgba(0,0,0,0.15); z-index: 101; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; }
        .dropdown-content.show { display: block; }
        .dropdown-item { color: var(--text); padding: 12px 16px; text-decoration: none; display: block; font-size: 14px; border-bottom: 1px solid var(--border); transition: 0.2s; cursor: pointer; text-align: left; width: 100%; background: none; border: none; font-family: inherit; }
        .dropdown-item:hover { background-color: var(--bg); }

        /* STATS GRID */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--card); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); display: flex; flex-direction: column; gap: 10px; transition: 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .stat-title { font-size: 13px; font-weight: 600; color: var(--muted); }
        .stat-value { font-size: 28px; font-weight: 900; color: var(--text); margin: 0; }

        /* TABEL DATA */
        .table-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .table-header { padding: 20px; border-bottom: 1px solid var(--border); }
        .table-header h3 { margin: 0; font-size: 16px; font-weight: 700; }
        
        .table-responsive { overflow-x: auto; width: 100%; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 15px 20px; font-size: 13px; font-weight: 600; color: var(--muted); border-bottom: 2px solid var(--border); white-space: nowrap; }
        td { padding: 15px 20px; font-size: 14px; border-bottom: 1px solid var(--border); color: var(--text); white-space: nowrap; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: var(--bg); }
        
        .title-cell { font-weight: 600; }
        
        /* BADGES STATUS */
        .badge { padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; border: 1px solid transparent; }
        .badge-published { background: #d1e7dd; color: #0f5132; border-color: #badbcc; }
        .badge-draft { background: var(--bg); color: var(--muted); border-color: var(--border); }
        .badge-review { background: #fff3cd; color: #856404; border-color: #ffeeba; }
        
        @media (prefers-color-scheme: dark) {
            .badge-published { background: rgba(25, 135, 84, 0.2); color: #75b798; border-color: #198754; }
            .badge-review { background: rgba(255, 193, 7, 0.2); color: #ffda6a; border-color: #ffc107; }
        }

        /* TOMBOL AKSI */
        .action-btns { display: flex; gap: 10px; }
        .btn-icon { background: transparent; border: 1px solid var(--border); color: var(--muted); width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; font-size: 14px; }
        .btn-icon:hover { background: var(--text); color: var(--bg); border-color: var(--text); }
        .btn-icon.danger:hover { background: #dc3545; color: white; border-color: #dc3545; }

        /* RESPONSIVE MOBILE */
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 998; opacity: 0; transition: 0.3s; }
        
        @media (max-width: 900px) {
            aside { left: -260px; box-shadow: 4px 0 15px rgba(0,0,0,0.2); }
            aside.show { left: 0; }
            main { margin-left: 0; width: 100%; padding: 20px; }
            .hamburger-btn { display: block; }
            .sidebar-overlay.show { display: block; opacity: 1; }
        }
    </style>
</head>
<body>

    <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <aside>
        <div class="logo-container">
            <a href="/" class="logo">NOVELKU.</a>
        </div>
        
        <nav style="display:flex; flex-direction:column; gap:8px;">
            <a href="#" class="nav-link active">📊 Dashboard</a>
            <a href="{{ route('admin.users') }}" class="nav-link">👥 Kelola User</a>
        </nav>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-link" style="width: 100%; border: none; background: transparent; cursor: pointer; text-align: left; padding: 0;">
                    🚪 Keluar
                </button>
            </form>
        </div>
    </aside>

    <main>
        <div class="header">
            <div class="header-title">
                <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
                <h1>Dashboard Admin</h1>
            </div>
            
            <div class="profile-menu">
                <div class="profile-avatar" onclick="toggleDropdown()">
                    {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 1)) }}
                </div>
                
                <div class="dropdown-content" id="profileDropdown">
                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--border);">
                        <strong style="display:block; font-size: 14px;">{{ auth()->user()->name ?? 'Administrator' }}</strong>
                        <span style="font-size: 12px; color: var(--muted);">{{ auth()->user()->email ?? 'admin@novelku.com' }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">🚪 Keluar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-title">Total User</span>
                <h3 class="stat-value">{{ number_format($totalUser) }}</h3>
            </div>
            <div class="stat-card">
                <span class="stat-title">Total Novel</span>
                <h3 class="stat-value">{{ number_format($totalNovel) }}</h3>
            </div>
            <div class="stat-card">
                <span class="stat-title">Reader Aktif</span>
                <h3 class="stat-value">{{ number_format($totalReader) }}</h3>
            </div>
            <div class="stat-card">
                <span class="stat-title">Creator Aktif</span>
                <h3 class="stat-value">{{ number_format($totalCreator) }}</h3>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Novel Terbaru</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Judul Novel</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                  <tbody>
                        @forelse($latestNovels as $novel)
                        <tr>
                            <td class="title-cell">{{ $novel->title }}</td>
                            <td>{{ $novel->creator->name ?? 'Unknown' }}</td>
                            <td>
                                @if($novel->status == 'published')
                                    <span class="badge badge-published">Published</span>
                                @elseif($novel->status == 'completed')
                                    <span class="badge badge-published">Completed</span>
                                @else
                                    <span class="badge badge-draft">{{ ucfirst($novel->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-btns">
                                    <form action="{{ route('admin.novel.delete', $novel->id) }}" method="POST" onsubmit="return confirm('Yakin hapus novel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Hapus">🗑️</button>
                                    </form>
                                    <form action="{{ route('admin.novel.reset', $novel->id) }}" method="POST" onsubmit="return confirm('Yakin mau reset views novel ini jadi 0?')">
    @csrf
    <button type="submit" class="btn-icon" title="Reset Views">🔄</button>
</form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: var(--muted);">Belum ada novel yang dibuat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                            
                           
                </table>
            </div>
        </div>
    </main>

    <script>
        // Logika Dropdown Profil
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }

        // Logika Sidebar Mobile
        function toggleSidebar() {
            document.querySelector('aside').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Tutup dropdown kalau klik di luar
        window.onclick = function(event) {
            if (!event.target.closest('.profile-menu')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    if (dropdowns[i].classList.contains('show')) {
                        dropdowns[i].classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>