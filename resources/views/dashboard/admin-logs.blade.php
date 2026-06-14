<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Logs - Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* TABEL & FILTER KHUSUS LOGS */
        .table-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .table-header { padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .table-header h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .table-responsive { overflow-x: auto; width: 100%; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 15px 20px; font-size: 13px; font-weight: 600; color: var(--muted); border-bottom: 2px solid var(--border); white-space: nowrap; }
        td { padding: 15px 20px; font-size: 14px; border-bottom: 1px solid var(--border); color: var(--text); white-space: nowrap; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: var(--bg); }
        
        .filter-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .form-control { background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 6px; font-size: 13px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--muted); }
        .btn-primary { background: var(--text); color: var(--bg); border: none; padding: 8px 15px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-primary:hover { opacity: 0.8; }
        .btn-danger { background: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-danger:hover { background: #c82333; }

        /* BADGES */
        .badge { padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; border: 1px solid transparent; }
        .badge-red { background: rgba(220, 53, 69, 0.1); color: #dc3545; border-color: rgba(220, 53, 69, 0.2); }
        .badge-green { background: rgba(40, 167, 69, 0.1); color: #28a745; border-color: rgba(40, 167, 69, 0.2); }
        .badge-yellow { background: rgba(255, 193, 7, 0.1); color: #ffc107; border-color: rgba(255, 193, 7, 0.2); }
        .badge-blue { background: rgba(13, 110, 253, 0.1); color: #0d6efd; border-color: rgba(13, 110, 253, 0.2); }
        .badge-purple { background: rgba(111, 66, 193, 0.1); color: #6f42c1; border-color: rgba(111, 66, 193, 0.2); }
        @media (prefers-color-scheme: dark) {
            .badge-red { color: #ff6b6b; } .badge-green { color: #51cf66; } .badge-yellow { color: #ffd43b; } .badge-blue { color: #74c0fc; } .badge-purple { color: #da77f2; }
        }

        .action-btns { display: flex; gap: 10px; }
        .btn-icon { background: transparent; border: 1px solid var(--border); color: var(--muted); width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; font-size: 14px; }
        .btn-icon:hover { background: var(--text); color: var(--bg); border-color: var(--text); }
        .btn-icon.danger:hover { background: #dc3545; color: white; border-color: #dc3545; }

        /* STATS GRID */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--card); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); display: flex; flex-direction: column; gap: 10px; transition: 0.2s; }
        .stat-title { font-size: 13px; font-weight: 600; color: var(--muted); }
        .stat-value { font-size: 28px; font-weight: 900; color: var(--text); margin: 0; }

        /* MODAL */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
        .modal-overlay.show { display: flex; }
        .modal-content { background: var(--card); padding: 30px; border-radius: var(--radius); border: 1px solid var(--border); max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); }
        .modal-header h2 { margin: 0; font-size: 20px; font-weight: 700; }
        .modal-close { background: none; border: none; font-size: 24px; color: var(--muted); cursor: pointer; }
        .detail-row { display: grid; grid-template-columns: 120px 1fr; gap: 15px; padding: 12px 0; border-bottom: 1px solid var(--border); font-size: 14px; }
        .detail-label { font-weight: 600; color: var(--muted); }

        /* TOAST & RESPONSIVE */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; pointer-events: none; }
        .toast { min-width: 320px; padding: 20px 24px; border-radius: var(--radius); color: #ffffff; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 18px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); transform: translateY(20px); transition: 0.3s; opacity: 0; background-color: #111111; border: 1px solid #222222; margin-top: 10px;}
        .toast.show { transform: translateY(0); opacity: 1; pointer-events: auto; }
        .toast-icon { font-size: 20px; width: 44px; height: 44px; border-radius: 50%; border: 1px solid #222222; display: flex; align-items: center; justify-content: center;}
        .toast.success .toast-icon { color: #28a745; background: rgba(40, 167, 69, 0.1); border-color: rgba(40, 167, 69, 0.3); }
        .toast.error .toast-icon { color: #dc3545; background: rgba(220, 53, 69, 0.1); border-color: rgba(220, 53, 69, 0.3); }

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
            <a href="{{ route('admin.dashboard') }}" class="nav-link">📊 Dashboard</a>
            <a href="{{ route('admin.users') }}" class="nav-link">👥 Kelola User</a>
            <a href="{{ route('admin.logs') }}" class="nav-link active">🛡️ Security Logs</a>
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
                <h1>Security Logs</h1>
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
                        <button type="submit" class="dropdown-item" style="color: #dc3545;">🚪 Keluar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><span class="stat-title">Total Logs</span><h2 class="stat-value" id="stat-total">-</h2></div>
            <div class="stat-card"><span class="stat-title">Hari Ini</span><h2 class="stat-value" id="stat-today">-</h2></div>
            <div class="stat-card"><span class="stat-title">Gagal Hari Ini</span><h2 class="stat-value" id="stat-failed">-</h2></div>
            <div class="stat-card"><span class="stat-title">Critical</span><h2 class="stat-value" id="stat-critical">-</h2></div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Aktivitas Sistem</h3>
                <form action="{{ route('admin.logs') }}" method="GET" class="filter-form">
                    <select name="log_type" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        @foreach($logTypes ?? [] as $type)
                            <option value="{{ $type }}" {{ request('log_type') === $type ? 'selected' : '' }}>{{ App\Models\ActivityLog::getLogLabel($type) }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="search" class="form-control" placeholder="Cari IP/Email..." value="{{ request('search') }}">
                    <button type="submit" class="btn-primary">Cari</button>
                    <button type="button" class="btn-danger" onclick="openClearAllModal()">Hapus Semua Log</button>
                </form>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Tipe</th>
                            <th>Email/User</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>Pesan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs ?? [] as $log)
                        <tr>
                            <td>{{ $log->created_at->timezone('Asia/Jakarta')->format('d M H:i') }}</td>
                            <td>
                                <span class="badge {{ match(App\Models\ActivityLog::getLogColor($log->log_type)) { 'danger' => 'badge-red', 'success' => 'badge-green', 'warning' => 'badge-yellow', 'info' => 'badge-purple', default => 'badge-blue' } }}">
                                    {{ App\Models\ActivityLog::getLogIcon($log->log_type) }} {{ App\Models\ActivityLog::getLogLabel($log->log_type) }}
                                </span>
                            </td>
                            <td>{{ $log->email ?? ($log->user_id ?? '-') }}</td>
                            <td><code style="background: var(--bg); padding: 4px; border-radius: 4px;">{{ $log->ip_address ?? '-' }}</code></td>
                            <td><span class="badge {{ $log->status === 'success' ? 'badge-green' : ($log->status === 'failed' ? 'badge-red' : 'badge-yellow') }}">{{ ucfirst($log->status) }}</span></td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($log->message, 40) }}</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-icon" onclick="viewDetail({{ $log->id }})" title="Detail">👁️</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--muted);">Aman terkendali. Belum ada log mencurigakan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($logs) && $logs->hasPages())
                <div class="pagination-container">
                    {{ $logs->links('vendor.pagination.novelku') }}
                </div>
            @endif
        </div>
    </main>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Aktivitas</h2>
                <button class="modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div id="detailContent">Loading...</div>
        </div>
    </div>

    <div class="modal-overlay" id="clearAllModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>⚠️ Clear All Logs</h2>
                <button class="modal-close" onclick="closeClearAllModal()">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.logs.clearAll') }}">
                @csrf
                <p style="color: #dc3545; font-weight: 600; margin-top: 0;">Yakin mau menghapus SEMUA log?</p>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                    <label>Ketik "yes" untuk konfirmasi:</label>
                    <input type="text" name="confirm" id="confirmInput" class="form-control" autocomplete="off" required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn-primary" onclick="closeClearAllModal()" style="flex: 1;">Batal</button>
                    <button type="submit" class="btn-danger" style="flex: 1; display: none;" id="submitClearBtn">Clear All</button>
                </div>
            </form>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        function toggleDropdown() { document.getElementById("profileDropdown").classList.toggle("show"); }
        function toggleSidebar() { document.querySelector('aside').classList.toggle('show'); document.getElementById('sidebarOverlay').classList.toggle('show'); }
        window.onclick = function(e) {
            if (!e.target.closest('.profile-menu')) {
                document.querySelectorAll(".dropdown-content").forEach(d => d.classList.remove('show'));
            }
        }

        // STATS
        function loadStatistics() {
            fetch("{{ route('admin.logs.statistics') }}").then(r => r.json()).then(data => {
                document.getElementById('stat-total').textContent = data.total_logs;
                document.getElementById('stat-today').textContent = data.today_logs;
                document.getElementById('stat-failed').textContent = data.failed_today;
                document.getElementById('stat-critical').textContent = data.critical_logs;
            });
        }
        loadStatistics(); setInterval(loadStatistics, 30000);

        // MODAL LOGIC
        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value ?? '-';
            return element.innerHTML;
        }

        function viewDetail(id) { 
            document.getElementById('detailContent').innerHTML = 'Loading...';
            document.getElementById('detailModal').classList.add('show'); 
            fetch(`{{ url('/dashboard/admin/logs/detail') }}/${id}`).then(r => r.json()).then(data => {
                document.getElementById('detailContent').innerHTML = `
                    <div class="detail-row"><span class="detail-label">Tipe:</span><span>${escapeHtml(data.log_type_label)}</span></div>
                    <div class="detail-row"><span class="detail-label">Status:</span><span>${escapeHtml(data.status)}</span></div>
                    <div class="detail-row"><span class="detail-label">Email:</span><span>${escapeHtml(data.email)}</span></div>
                    <div class="detail-row"><span class="detail-label">IP:</span><span>${escapeHtml(data.ip_address)}</span></div>
                    <div class="detail-row"><span class="detail-label">Pesan:</span><span>${escapeHtml(data.message)}</span></div>
                    <div class="detail-row"><span class="detail-label">User Agent:</span><span style="font-size:12px; color:var(--muted)">${escapeHtml(data.user_agent)}</span></div>
                `;
            });
        }
        function closeDetailModal() { document.getElementById('detailModal').classList.remove('show'); }
        function openClearAllModal() { document.getElementById('clearAllModal').classList.add('show'); document.getElementById('confirmInput').value=''; document.getElementById('submitClearBtn').style.display='none'; }
        function closeClearAllModal() { document.getElementById('clearAllModal').classList.remove('show'); }
        document.getElementById('confirmInput')?.addEventListener('input', e => { document.getElementById('submitClearBtn').style.display = (e.target.value.toLowerCase() === 'yes') ? 'block' : 'none'; });

        // TOAST
        function showToast(msg, type='success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type} show`;
            toast.innerHTML = `<span class="toast-icon">${type==='success'?'✅':'⚠️'}</span><span class="toast-text"><span class="toast-title">${type==='success'?'Sukses':'Gagal'}</span><span class="toast-message">${msg}</span></span>`;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }
        @if($errors->any()) showToast(@json($errors->first()), 'error'); @endif
        @if(session('success')) showToast(@json(session("success")), 'success'); @endif
    </script>
</body>
</html>
