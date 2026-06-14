<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Dashboard Admin</title>
    <style>
        /* TEMA NOVELKU (Bawaan Lu) */
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        * { box-sizing: border-box; }
        
        /* SIDEBAR & HEADER (Bawaan Lu) */
        aside { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; flex-shrink: 0; position: fixed; height: 100vh; z-index: 1000; transition: left 0.3s ease; }
        .logo-container { padding-bottom: 20px; border-bottom: 1px solid var(--border); margin-bottom: 10px; }
        .logo { font-size: 24px; font-weight: 900; color: var(--text); text-decoration: none; letter-spacing: -0.5px; }
        .nav-link { padding: 12px 15px; border-radius: 8px; text-decoration: none; color: var(--muted); font-weight: 600; font-size: 14px; transition: 0.2s; display: flex; align-items: center; gap: 12px; }
        .nav-link:hover { background: var(--bg); color: var(--text); }
        .nav-link.active { background: var(--text); color: var(--bg); }
        .sidebar-bottom { margin-top: auto; border-top: 1px solid var(--border); padding-top: 20px; }

        main { flex: 1; padding: 30px 40px; margin-left: 250px; overflow-y: auto; width: calc(100% - 250px); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
        .header-title { display: flex; align-items: center; gap: 15px; }
        .header-title h1 { margin: 0; font-size: 22px; font-weight: 800; }
        .hamburger-btn { display: none; background: none; border: none; color: var(--text); font-size: 24px; cursor: pointer; padding: 0; }
        
        .profile-menu { position: relative; display: inline-block; }
        .profile-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--border); color: var(--text); display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer; transition: 0.2s; border: 1px solid var(--border); }
        .dropdown-content { display: none; position: absolute; right: 0; top: 50px; background-color: var(--card); min-width: 200px; box-shadow: 0px 8px 20px rgba(0,0,0,0.15); z-index: 101; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; }
        .dropdown-content.show { display: block; }
        .dropdown-item { color: var(--text); padding: 12px 16px; text-decoration: none; display: block; font-size: 14px; border-bottom: 1px solid var(--border); transition: 0.2s; cursor: pointer; text-align: left; width: 100%; background: none; border: none; font-family: inherit; }
        .dropdown-item:hover { background-color: var(--bg); }

        /* TABEL DATA */
        .table-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .table-header { padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .table-header h3 { margin: 0; font-size: 16px; font-weight: 700; }
        
        .table-responsive { overflow-x: auto; width: 100%; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 15px 20px; font-size: 13px; font-weight: 600; color: var(--muted); border-bottom: 2px solid var(--border); white-space: nowrap; }
        td { padding: 15px 20px; font-size: 14px; border-bottom: 1px solid var(--border); color: var(--text); white-space: nowrap; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: var(--bg); }
        
        /* CUSTOM STYLES BUAT USER PAGE */
        .filter-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .form-control { background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 6px; font-size: 13px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--muted); }
        .btn-primary { background: var(--text); color: var(--bg); border: none; padding: 8px 15px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-primary:hover { opacity: 0.8; }

        .badge { padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; border: 1px solid transparent; }
        .badge-creator { background: rgba(13, 110, 253, 0.1); color: #0d6efd; border-color: rgba(13, 110, 253, 0.2); }
        .badge-reader { background: rgba(25, 135, 84, 0.1); color: #198754; border-color: rgba(25, 135, 84, 0.2); }
        @media (prefers-color-scheme: dark) {
            .badge-creator { color: #6ea8fe; }
            .badge-reader { color: #75b798; }
        }

        .action-btns { display: flex; gap: 10px; }
        .btn-icon { background: transparent; border: 1px solid var(--border); color: var(--muted); width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; font-size: 14px; }
        .btn-icon:hover { background: var(--text); color: var(--bg); border-color: var(--text); }
        .btn-icon.danger:hover { background: #dc3545; color: white; border-color: #dc3545; }

         /* TOAST NOTIFICATION - DARK MODE MUTLAK */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; pointer-events: none; }
        .toast { min-width: 320px; padding: 20px 24px; border-radius: var(--radius); color: #ffffff; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 18px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); transform: translateY(20px); transition: transform 0.3s ease, opacity 0.3s ease; opacity: 0; background-color: #111111; border: 1px solid #222222; }
        .toast.show { transform: translateY(0); opacity: 1; pointer-events: auto; }
        .toast-icon { font-size: 20px; display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 50%; border: 1px solid #222222; }
        .toast.success .toast-icon { color: #28a745; background: rgba(40, 167, 69, 0.1); border-color: rgba(40, 167, 69, 0.3); }
        .toast.error .toast-icon { color: #dc3545; background: rgba(220, 53, 69, 0.1); border-color: rgba(220, 53, 69, 0.3); }
        .toast-text { display: flex; flex-direction: column; gap: 4px; flex: 1; }
        .toast-title { color: #ffffff; font-weight: 700; font-size: 16px; }
        .toast-message { color: #a0a0a0; font-weight: 500; font-size: 14px; }

        .pagination-container { padding: 20px; border-top: 1px solid var(--border); }

        /* Custom pagination to match Novelku dashboard theme */
        .pagination { list-style: none; display: flex; gap: 8px; padding: 0; margin: 0; justify-content: flex-end; flex-wrap: wrap; }
        .pagination li { display: inline-block; }
        .pagination a, .pagination span { display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; padding: 0 12px; border-radius: 10px; background: transparent; border: 1px solid var(--border); color: var(--text); text-decoration: none; font-weight: 700; transition: background .15s, color .15s, transform .06s; }
        .pagination a:hover { background: rgba(0,0,0,0.04); transform: translateY(-1px); }
        @media (prefers-color-scheme: dark) { .pagination a:hover { background: rgba(255,255,255,0.03); } }
        .pagination .active span { background: var(--text); color: var(--bg); border-color: var(--text); box-shadow: 0 6px 18px rgba(0,0,0,0.12); }
        .pagination .disabled span { opacity: .45; cursor: default; }
        .pagination-summary { margin-top: 8px; color: var(--muted); font-size: 13px; }
        
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
            {{-- Sesuaikan route ini dengan nama route dashboard utama lu --}}
            <a href="/dashboard/admin" class="nav-link">📊 Dashboard</a>
            
            {{-- Ini Menu Kelola User (Sekarang jadi Active) --}}
            <a href="{{ route('admin.users') }}" class="nav-link active">👥 Kelola User</a>
            {{-- Tambahin menu baru buat Security Logs --}}
            <a href="{{ route('admin.logs') }}" class="nav-link">🛡️ Security Logs</a>
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
                <h1>Kelola User</h1>
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

        <div class="table-card">
            <div class="table-header">
                <h3>Daftar Pengguna</h3>
                
                <form action="{{ route('admin.users') }}" method="GET" class="filter-form">
                    <select name="role" class="form-control" onchange="this.form.submit()">
                        <option value="all">Semua Role</option>
                        <option value="creator" {{ request('role') == 'creator' ? 'selected' : '' }}>Creator</option>
                        <option value="reader" {{ request('role') == 'reader' ? 'selected' : '' }}>Reader</option>
                    </select>
                    
                    <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
                    <button type="submit" class="btn-primary">Cari</button>
                </form>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Total Karya</th>
                            <th>Tanggal Gabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td style="font-weight: 600;">{{ $user->name }}</td>
                            <td style="color: var(--muted);">{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'creator')
                                    <span class="badge badge-creator">Creator</span>
                                @else
                                    <span class="badge badge-reader">Reader</span>
                                @endif
                            </td>
                            <td>
                                {{ $user->role == 'creator' ? $user->novels_count . ' Novel' : '-' }}
                            </td>
                            <td style="color: var(--muted);">{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="action-btns">
                                    {{-- Tombol Detail / Edit (Opsional, arahin ntar) --}}
                                    <button class="btn-icon" title="Lihat Profil">👁️</button>
                                    
                                    {{-- Tombol Hapus / Banned --}}
                                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus akun ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Hapus Akun">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--muted);">
                                Tidak ada user yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="pagination-container">
                    {{ $users->links('vendor.pagination.novelku') }}
                </div>
            @endif
        </div>
    </main>

<div class="toast-container">
        <div id="appToast" class="toast @if(session('success')) success show @elseif(session('error')) error show @endif">
            <span class="toast-icon" id="toastIcon">
                @if(session('success')) ✅ @elseif(session('error')) ⚠️ @endif
            </span>
            <span class="toast-text">
                <span class="toast-title" id="toastTitle">
                    @if(session('success')) Sukses @elseif(session('error')) Error @endif
                </span>
                <span class="toast-message" id="toastMessage">
                    {{ session('success') ?? session('error') }}
                </span>
            </span>
        </div>
    </div>

    <script>
        function toggleDropdown() { document.getElementById("profileDropdown").classList.toggle("show"); }
        function toggleSidebar() { 
            document.querySelector('aside').classList.toggle('show'); 
            document.getElementById('sidebarOverlay').classList.toggle('show'); 
        }
        window.onclick = function(event) {
            if (!event.target.closest('.profile-menu')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    if (dropdowns[i].classList.contains('show')) { dropdowns[i].classList.remove('show'); }
                }
            }
        }
    </script>
</body>
</html>
