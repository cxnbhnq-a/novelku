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
                    <input type="file" id="coverInput" name="cover_image" accept="image/*" style="display: none;" onchange="previewCover(event)">
                </div>

                <button type="submit" class="btn-submit">Simpan Novel & Lanjut Tulis Bab 1</button>
            </form>
        </div>
    </main>

    <script>
        // Logika Responsif Sidebar
        function toggleSidebar() {
            document.querySelector('aside').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Script untuk nampilin preview gambar saat cover dipilih
        function previewCover(event) {
            const input = event.target;
            const preview = document.getElementById('coverPreview');
            const uploadText = document.getElementById('uploadText');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    uploadText.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>