<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $novel->title }} - NovelKu</title>
    <style>
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }
        * { box-sizing: border-box; }

        nav { padding: 20px 5%; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); background: var(--card); }
        .logo { font-size: 24px; font-weight: 900; color: var(--text); text-decoration: none; }
        .nav-links a { margin-left: 20px; color: var(--text); text-decoration: none; font-weight: 600; font-size: 14px; }

        /* AREA TOMBOL BACK (Berdiri Sendiri di Atas) */
        .top-navigation { max-width: 900px; margin: 40px auto 0; padding: 0 20px; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--muted); text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.2s; }
        .btn-back:hover { color: var(--text); transform: translateX(-3px); }

        .container { max-width: 900px; margin: 20px auto 80px; padding: 0 20px; display: grid; grid-template-columns: 250px 1fr; gap: 40px; }
        
        .cover-area { text-align: center; }
        .novel-cover { width: 100%; aspect-ratio: 2/3; background: var(--accent); color: var(--bg); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 60px; font-weight: 900; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .novel-cover img { width: 100%; height: 100%; object-fit: cover; }
        
        .btn-read { display: block; width: 100%; padding: 15px; background: var(--text); color: var(--bg); border: none; border-radius: 8px; font-weight: 700; font-size: 16px; cursor: pointer; text-decoration: none; text-align: center; transition: 0.2s; }
        .btn-read:hover { opacity: 0.8; transform: translateY(-2px); }

        .info-area h1 { margin: 0 0 10px; font-size: 36px; font-weight: 900; line-height: 1.2; }
        .author { font-size: 18px; color: var(--muted); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        .meta-tags { display: flex; gap: 12px; margin-bottom: 30px; flex-wrap: wrap; }
        .meta-tag { padding: 6px 14px; background: var(--card); border: 1px solid var(--border); border-radius: 50px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; }

        .synopsis h3 { font-size: 20px; margin-bottom: 15px; border-bottom: 2px solid var(--border); padding-bottom: 10px; display: inline-block; }
        .synopsis p { font-size: 16px; color: var(--muted); text-align: justify; white-space: pre-line; margin: 0; }

        @media (max-width: 768px) {
            .container { grid-template-columns: 1fr; gap: 30px; }
            .cover-area { max-width: 250px; margin: 0 auto; }
            .info-area { text-align: center; }
            .meta-tags { justify-content: center; }
            .top-navigation { text-align: left; }
        }
    </style>
</head>
<body>

    <nav>
        <a href="/" class="logo">NOVELKU.</a>
        <div class="nav-links">
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}" style="padding: 8px 15px; border: 1px solid var(--border); border-radius: 6px;">Daftar</a>
            @endauth
        </div>
    </nav>

    <div class="top-navigation">
        <a href="{{ $backUrl }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali
        </a>
    </div>

    <main class="container">
        <div class="cover-area">
            <div class="novel-cover">
                @if($novel->cover_image)
                    <img src="{{ asset('storage/' . $novel->cover_image) }}" alt="Cover">
                @else
                    {{ strtoupper(substr($novel->title, 0, 1)) }}
                @endif
            </div>

            @if($firstChapter)
                <a href="{{ route('baca.chapter', $firstChapter->id) }}" class="btn-read">📖 Mulai Baca</a>
            @else
                <button class="btn-read" style="background: var(--muted); cursor: not-allowed;" disabled>Belum ada bab</button>
            @endif
        </div>

        <div class="info-area">
            <h1>{{ $novel->title }}</h1>
            <div class="author">
                <span>✍️ {{ $novel->creator->name }}</span>
            </div>

            <div class="meta-tags">
                @foreach($novel->genres as $genre)
                    <div class="meta-tag">🎭 {{ $genre->name }}</div>
                @endforeach
                <div class="meta-tag">👁️ {{ number_format($novel->views) }} Views</div>
                <div class="meta-tag">📌 {{ ucfirst($novel->status) }}</div>
            </div>

            <div class="synopsis">
                <h3>Sinopsis</h3>
                <p>{{ $novel->synopsis }}</p>
            </div>
        </div>
    </main>

</body>
</html>