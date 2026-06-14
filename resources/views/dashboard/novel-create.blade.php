<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Novel Baru - NovelKu</title>
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
        
        /* SIDEBAR (Sama dengan Dashboard Utama) */
        aside { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; flex-shrink: 0;}
        .nav-link { padding: 12px 15px; border-radius: var(--radius); text-decoration: none; color: var(--text); font-weight: 500; transition: 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--bg); border: 1px solid var(--border); }
        
        /* MAIN CONTENT */
        main { flex: 1; padding: 40px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* FORM STYLING */
        .form-card { background: var(--card); border: 1px solid var(--border); padding: 30px; border-radius: var(--radius); max-width: 800px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; }
        input[type="text"], textarea, select { 
            width: 100%; padding: 12px; border: 1px solid var(--border); background: var(--bg); 
            color: var(--text); border-radius: 8px; font-family: inherit; font-size: 15px; box-sizing: border-box;
        }
        input:focus, textarea:focus, select:focus { outline: none; border-color: var(--text); }
        
        textarea { resize: vertical; min-height: 150px; }

        /* UPLOAD COVER STYLING */
        .cover-upload-box {
            border: 2px dashed var(--border); border-radius: 8px; padding: 30px; text-align: center;
            background: var(--bg); cursor: pointer; transition: 0.3s; position: relative;
        }
        .cover-upload-box:hover { border-color: var(--text); }
        .cover-preview { max-width: 150px; border-radius: 6px; display: none; margin: 10px auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        .btn-submit { background: var(--accent); color: var(--bg); border: none; padding: 14px 24px; font-weight: 600; border-radius: 8px; cursor: pointer; font-size: 15px; width: 100%; transition: 0.2s; }
        .btn-submit:hover { opacity: 0.8; }

        /* TOAST NOTIFICATION - DARK MODE */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; pointer-events: none; }
        .toast { min-width: 320px; padding: 20px 24px; border-radius: var(--radius); color: #ffffff; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 18px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); transform: translateY(20px); transition: transform 0.3s ease, opacity 0.3s ease; opacity: 0; background-color: #111111; border: 1px solid #222222; }
        .toast.show { transform: translateY(0); opacity: 1; pointer-events: auto; }
        .toast-icon { font-size: 20px; display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 50%; border: 1px solid #222222; }
        .toast.success .toast-icon { color: #28a745; background: rgba(40, 167, 69, 0.1); border-color: rgba(40, 167, 69, 0.3); }
        .toast.error .toast-icon { color: #dc3545; background: rgba(220, 53, 69, 0.1); border-color: rgba(220, 53, 69, 0.3); }
        .toast-text { display: flex; flex-direction: column; gap: 4px; flex: 1; }
        .toast-title { color: #ffffff; font-weight: 700; font-size: 16px; }
        .toast-message { color: #a0a0a0; font-weight: 500; font-size: 14px; }
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

    <aside>
        <a href="/" style="font-size: 22px; font-weight: 900; margin-bottom: 10px; text-decoration: none; color: inherit; display: block;">NOVELKU.</a>
        <nav style="display:flex; flex-direction:column; gap:5px; margin-top: 20px;">
            <a href="{{ route('dashboard') }}" class="nav-link">📊 Dashboard</a>
            <a href="{{ route('karya.saya') }}" class="nav-link">📚 Karya Saya</a>
            <a href="{{ route('novel.create') }}" class="nav-link active">✍️ Tambah Novel</a>
            <a href="{{ route('profile.edit') }}" class="nav-link">⚙️ Edit Profil</a>
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
                    <h1 style="margin:0;">Buat Novel Baru</h1>
                    <p style="color:var(--muted); margin:5px 0 0;">Mulai perjalanan karyamu di sini. Siapkan wadah bukunya dulu.</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" style="color: var(--text); font-weight: 600; text-decoration: none;">&larr; Kembali</a>
        </div>

        <div class="form-card">
            <form action="{{ route('novel.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label>Judul Novel</label>
                    <input type="text" name="title" placeholder="Masukkan judul yang menarik..." required>
                </div>

                <div class="form-group">
                    <label>Sinopsis / Deskripsi Cerita</label>
                    <textarea name="synopsis" placeholder="Tuliskan sinopsis singkat yang bikin pembaca penasaran..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Genre Utama</label>
                    <select name="genre">
                        <option value="">-- Pilih Genre --</option>
                        <option value="romance">Romansa</option>
                        <option value="fantasy">Fantasi</option>
                        <option value="sci-fi">Fiksi Ilmiah (Sci-Fi)</option>
                        <option value="thriller">Misteri / Thriller</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cover Novel (Opsional)</label>
                    <div class="cover-upload-box" onclick="document.getElementById('coverInput').click()">
                        <div id="uploadText">
                            <span style="font-size: 24px;">📸</span><br>
                            Klik untuk memilih gambar cover<br>
                            <small style="color: var(--muted);">Format JPG, PNG, WEBP (Maks 2MB)</small>
                        </div>
                        <img id="coverPreview" class="cover-preview" src="" alt="Preview Cover">
                    </div>
                    <input type="file" id="coverInput" name="cover_image" accept="image/png, image/jpeg, image/jpg, image/webp" style="display: none;" onchange="previewCover(event)">
                </div>

                <button type="submit" class="btn-submit">Simpan Novel & Lanjut Tulis Bab 1</button>
            </form>
        </div>
    </main>
<div class="toast-container">
            @if ($errors->any() || session('success') || session('error'))
                <div id="appToast" class="toast {{ session('success') ? 'success' : 'error' }} show">
                    <span class="toast-icon">
                        {{ session('success') ? '✅' : '⚠️' }}
                    </span>
                    <span class="toast-text">
                        <span class="toast-title">{{ session('success') ? 'Sukses' : 'Error' }}</span>
                        <span class="toast-message">{{ session('success') ?? session('error') ?? $errors->first() }}</span>
                    </span>
                </div>
            @endif
        </div>
    </main>

    <script>
        // Logika Responsif Sidebar
        function toggleSidebar() {
            document.querySelector('aside').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // FUNGSI TOAST CLIENT-SIDE
        function showCustomToast(type, title, message) {
            const container = document.querySelector('.toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type} show`;
            toast.innerHTML = `
                <span class="toast-icon">${type === 'success' ? '✅' : '⚠️'}</span>
                <span class="toast-text">
                    <span class="toast-title">${title}</span>
                    <span class="toast-message">${message}</span>
                </span>
            `;
            container.appendChild(toast);
            setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3500);
        }

        // FUNGSI PREVIEW GAMBAR
        function previewCover(event) {
            const input = event.target;
            const preview = document.getElementById('coverPreview');
            const uploadText = document.getElementById('uploadText');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                const allowedExtensions = ['jpeg', 'jpg', 'png', 'webp'];
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (!allowedTypes.includes(file.type) || !allowedExtensions.includes(fileExtension)) {
                    showCustomToast('error', 'Format File Salah', 'Pilih file gambar JPG, PNG, atau WEBP yang aman.');
                    input.value = '';
                    preview.style.display = 'none';
                    if (uploadText) uploadText.style.display = 'block';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    showCustomToast('error', 'Ukuran Terlalu Besar', 'Maksimal ukuran cover novel cuma 2MB ya.');
                    input.value = '';
                    preview.style.display = 'none';
                    if (uploadText) uploadText.style.display = 'block';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (uploadText) uploadText.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        // AUTO HIDE TOAST SERVER-SIDE
        document.addEventListener('DOMContentLoaded', () => {
            const serverToast = document.getElementById('appToast');
            if (serverToast) {
                setTimeout(() => { 
                    serverToast.classList.remove('show'); 
                }, 3500);
            }
        });
    </script>
</body>
</html>
