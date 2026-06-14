<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovelKu - Platform Kreator Novel Indonesia</title>
    <style>
        /* TEMA WARNA */
        :root { 
            --primary: #111111; --secondary: #555555; --bg: #ffffff; --text: #111111; 
            --border: #e5e7eb; --divider: #d1d5db; --card-bg: #f9fafb;
        }
        
        @media (prefers-color-scheme: dark) {
            :root { 
                --primary: #eeeeee; --secondary: #aaaaaa; --bg: #000000; --text: #f5f5f5; 
                --border: #222222; --divider: #333333; --card-bg: #0a0a0a;
            }
        }

        /* RESET & LOCK SCROLL KANAN-KIRI */
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: hidden; /* Kunci biar ga bisa geser kanan kiri */
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        * { box-sizing: border-box; }

        /* CONTAINER STANDAR (Biar semua sejajar) */
        .container {
            width: 100%;
            max-width: 100%;
            padding: 0 10%;
        }

        /* HEADER */
        nav { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 20px 10%; 
            border-bottom: 1px solid var(--divider); 
            background: var(--bg);
        }
        .logo { display: flex; align-items: center; gap: 8px; font-size: 22px; font-weight: 900; text-decoration: none; color: var(--text); letter-spacing: -0.5px; }

        /* HERO SECTION */
        .hero { 
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; 
            padding: 80px 10%; 
            border-bottom: 1px solid var(--divider);
        }
        .hero h1 { font-size: clamp(2.5rem, 8vw, 4rem); margin-bottom: 10px; line-height: 1.1; }
        .hero p { max-width: 600px; color: var(--secondary); margin-bottom: 30px; font-size: 1.1rem; }
        
        /* BUTTONS */
        .btn { padding: 12px 30px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: 0.3s; border: 1px solid var(--primary); display: inline-block; color: var(--text); }
        .btn-dark { background: var(--primary); color: var(--bg); border: 1px solid var(--primary); }
        .btn-dark:hover { opacity: 0.8; transform: translateY(-1px); }

        /* CONTENT SECTION */
        .content-section {
            padding: 60px 10% 100px; /* Jarak bawah ditambah biar ga nabrak footer */
            flex: 1;
        }
        .section-title { font-size: 24px; font-weight: 800; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; }

        /* NOVEL GRID */
        .novel-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); 
            gap: 25px; 
        }
        .novel-card { text-decoration: none; color: var(--text); display: flex; flex-direction: column; gap: 12px; }
        .cover-wrapper {
            width: 100%; aspect-ratio: 2 / 3; overflow: hidden; border-radius: 10px; 
            border: 1px solid var(--border); background: var(--card-bg);
        }
        .cover-img { width: 100%; height: 100%; object-fit: cover; transition: 0.4s; }
        .novel-card:hover .cover-img { transform: scale(1.05); }
        
        .novel-info h3 { margin: 0; font-size: 16px; font-weight: 700; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .novel-info p { margin: 4px 0 0; font-size: 13px; color: var(--secondary); }
        
        .tags { display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap; }
        .tag { background: var(--card-bg); font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 600; border: 1px solid var(--border); }

        /* FOOTER FIX */
        footer { 
            width: 100%;
            border-top: 1px solid var(--divider); 
            background: var(--bg);
        }
.footer-content {
    padding: 40px 10%;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    flex-wrap: wrap;
    gap: 20px;
}
        @media (max-width: 768px) {
            nav, .hero, .content-section, .footer-content { padding: 30px 5%; }
            .novel-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
            .footer-content { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
    
    <nav>
        <a href="/" class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
            <span>NOVELKU.</span>
        </a>
        <div style="display:flex; gap:15px; align-items:center;">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-dark">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-dark">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-dark">Daftar</a>
            @endauth
        </div>
    </nav>

    <section class="hero">
        <h1>Tulis Ceritamu, <br>Temukan Pembacamu.</h1>
        <p>Platform wadah kreatif bagi para penulis novel masa kini. Gabung dengan ribuan kreator lainnya.</p>
        <div style="display:flex; gap:15px; justify-content: center; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('novel.explore') }}" class="btn btn-dark">🚀 Jelajahi Novel</a>
            @guest
                <a href="{{ route('login') }}" class="btn btn-dark">Mulai Menulis</a>
            @endguest
        </div>
    </section>

    <section class="content-section">
        <h2 class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Trending Minggu Ini
        </h2>

        <div class="novel-grid">
            @forelse ($novels as $novel)
            <a href="{{ route('novel.show', $novel->id) }}" class="novel-card">
                <div class="cover-wrapper">
                    @if($novel->cover_image)
                        <img src="{{ asset('storage/' . $novel->cover_image) }}" alt="Cover" class="cover-img">
                    @else
                        <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:var(--card-bg); font-size:40px; font-weight:900;">
                            {{ strtoupper(substr($novel->title, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="novel-info">
                    <h3>{{ $novel->title }}</h3>
                    <p>{{ $novel->creator->name }}</p>
                    <div class="tags">
                        @foreach($novel->genres as $genre)
                            <span class="tag">{{ $genre->name }}</span>
                        @endforeach
                        <span class="tag" style="opacity: 0.7;">{{ ucfirst($novel->status) }}</span>
                    </div>
                </div>
            </a>
            @empty
                <p style="grid-column: 1/-1; text-align: center; color: var(--secondary);">Belum ada novel yang diterbitkan.</p>
            @endforelse
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div style="color: var(--secondary); font-size: 14px;">
                &copy; 2026 NovelKu. Hak cipta dilindungi.
            </div>
        </div>
    </footer>

</body>
</html>
