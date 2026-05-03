<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bab - {{ $chapter->title }} - NovelKu</title>
    
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #000000; --card: #111111; --text: #f5f5f5; --muted: #aaaaaa; --border: #222222; --accent: #ffffff; } }
        
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        /* SIDEBAR */
        aside { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; flex-shrink: 0;}
        .nav-link { padding: 12px 15px; border-radius: var(--radius); text-decoration: none; color: var(--text); font-weight: 500; transition: 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--bg); border: 1px solid var(--border); }
        
        /* CONTENT */
        main { flex: 1; padding: 40px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .form-card { background: var(--card); border: 1px solid var(--border); padding: 30px; border-radius: var(--radius); max-width: 900px; margin: 0 auto; }
        .form-group { margin-bottom: 25px; }
        label { display: block; font-weight: 600; margin-bottom: 10px; font-size: 14px; }
        
        input[type="text"], input[type="number"], select { width: 100%; padding: 14px; border: 1px solid var(--border); background: var(--bg); color: var(--text); border-radius: 8px; font-family: inherit; font-size: 15px; box-sizing: border-box; }
        
        /* QUILL EDITOR */
        .editor-container { background: #ffffff; color: #000000; border-radius: 0 0 8px 8px; }
        .ql-toolbar { background: #f1f1f1; border-radius: 8px 8px 0 0; border-color: var(--border) !important; }
        .ql-container { border-color: var(--border) !important; font-size: 16px; min-height: 500px; font-family: 'Inter', sans-serif; }
        
        .btn-submit { background: var(--accent); color: var(--bg); border: none; padding: 16px 30px; font-weight: 700; border-radius: 8px; cursor: pointer; font-size: 16px; width: 100%; transition: 0.2s; margin-top: 10px;}
        .btn-submit:hover { opacity: 0.8; }

        /* ALERT ERROR */
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fecaca; font-weight: 600; }

        .hamburger-btn { display: none; background: none; border: none; color: var(--text); font-size: 28px; cursor: pointer; padding: 0; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 998; }
        
        @media (max-width: 768px) {
            aside { position: fixed; top: 0; left: -300px; height: 100vh; z-index: 999; box-shadow: 4px 0 15px rgba(0,0,0,0.5); transition: 0.3s; }
            aside.show { left: 0; }
            .hamburger-btn { display: block; }
            .sidebar-overlay.show { display: block; opacity: 1; }
            main { padding: 20px; }
            .form-card { padding: 20px; }
        }
    </style>
</head>
<body>

    <aside id="sidebar">
        <a href="/" style="font-size: 22px; font-weight: 900; margin-bottom: 10px; text-decoration: none; color: inherit; display: block;">NOVELKU.</a>
        <nav style="display:flex; flex-direction:column; gap:5px; margin-top: 20px;">
            <a href="{{ route('dashboard') }}" class="nav-link">📊 Dashboard</a>
            <a href="{{ route('novel.edit', $chapter->novel_id) }}" class="nav-link active">📚 Manajemen Bab</a>
        </nav>
    </aside>

    <main>
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <div class="header">
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
                <h1 style="margin:0;">Revisi Bab</h1>
            </div>
            <a href="{{ route('novel.edit', $chapter->novel_id) }}" style="color: var(--text); font-weight: 600; text-decoration: none;">&larr; Batal</a>
        </div>

        <div class="form-card">
            @if(session('error'))
                <div class="alert-error">⚠️ {{ session('error') }}</div>
            @endif

            <form id="chapterForm" action="{{ route('chapter.update', $chapter->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Pilih Novel</label>
                    <select name="novel_id" required>
                        @foreach($novels as $novel)
                            <option value="{{ $novel->id }}" {{ $chapter->novel_id == $novel->id ? 'selected' : '' }}>{{ $novel->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Bab Ke-</label>
                    <input type="number" name="chapter_number" value="{{ old('chapter_number', $chapter->chapter_number) }}" required>
                </div>

                <div class="form-group">
                    <label>Judul Bab</label>
                    <input type="text" name="title" value="{{ old('title', $chapter->title) }}" required>
                </div>

                <div class="form-group">
                    <label>Isi Cerita</label>
                    <div id="editor" class="editor-container">
                        {!! clean(old('content', $chapter->content)) !!}
                    </div>
                    <input type="hidden" name="content" id="contentInput">
                </div>

                <button type="submit" class="btn-submit">Simpan Perubahan Bab</button>
            </form>
        </div>
    </main>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Quill
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Tulis revisi ceritamu di sini...',
        modules: { 
            toolbar: [
                [{ 'header': [2, 3, false] }], 
                ['bold', 'italic', 'underline', 'strike'], 
                ['blockquote'], 
                [{ 'list': 'ordered'}, { 'list': 'bullet' }], 
                ['clean']
            ] 
        }
    });

    // 2. ANTI-IMAGE (Hanya buang image, teks tetep aman)
    quill.clipboard.addMatcher(Node.ELEMENT_NODE, function(node, delta) {
        delta.ops = delta.ops.filter(op => {
            return !(op.insert && op.insert.image);
        });
        return delta;
    });

    // 3. ANTI-DROP
    quill.container.addEventListener('drop', function(e) {
        e.preventDefault();
    }, false);

    // 4. SYNC KE INPUT HIDDEN
    var form = document.getElementById('chapterForm');
    form.onsubmit = function() {
        // Ambil HTML dari editor
        var html = quill.root.innerHTML;
        
        // Cek kalau isinya cuma tag kosong (biar nggak lolos required)
        if (quill.getText().trim().length === 0) {
            alert("Isi cerita tidak boleh kosong!");
            return false;
        }

        document.getElementById('contentInput').value = html;
    };

    // 5. FIX: Mastiin kursor bisa masuk pas diklik
    quill.on('selection-change', function(range) {
        if (range) {
            console.log('User is typing...');
        }
    });
});
    </script>
</body>
</html>