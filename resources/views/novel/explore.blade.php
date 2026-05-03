<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eksplorasi Novel - NovelKu</title>
    <style>
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; } }
        
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }
        nav { padding: 15px 5%; background: var(--card); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        
        .main-layout { display: grid; grid-template-columns: 250px 1fr; gap: 30px; padding: 40px 5%; }
        
        /* Sidebar Filter */
        .filter-side h4 { margin-bottom: 15px; }
        .genre-list { list-style: none; padding: 0; }
        .genre-list li { margin-bottom: 10px; }
        .genre-list a { text-decoration: none; color: var(--muted); font-size: 14px; transition: 0.2s; }
        .genre-list a:hover { color: var(--text); font-weight: 600; }

        /* Grid Novel */
        .novel-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; }
        .novel-card { text-decoration: none; color: inherit; }
        .cover { width: 100%; aspect-ratio: 2/3; background: #111; border-radius: 8px; margin-bottom: 10px; overflow: hidden; border: 1px solid var(--border); }
        .cover img { width: 100%; height: 100%; object-fit: cover; }
        .title { font-size: 14px; font-weight: 700; margin-bottom: 5px; }
        .author { font-size: 12px; color: var(--muted); }

        /* Search Bar */
        .search-box { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--card); color: var(--text); margin-bottom: 30px; }
    </style>
</head>
<body>

<nav>
    <a href="/" style="font-weight: 900; text-decoration:none; color:inherit;">NOVELKU.</a>
    <a href="{{ route('dashboard') }}" style="text-decoration:none; color:inherit; font-weight:600;">Dashboard</a>
</nav>

<div class="main-layout">
    <aside class="filter-side">
        <h4>🎭 Genre</h4>
        <ul class="genre-list">
            <li><a href="{{ route('novel.explore') }}">Semua Genre</a></li>
            @foreach($genres as $g)
                <li><a href="{{ route('novel.explore', ['genre' => $g->slug]) }}">{{ $g->name }}</a></li>
            @endforeach
        </ul>
    </aside>

    <section>
        <form action="{{ route('novel.explore') }}" method="GET">
            <input type="text" name="search" class="search-box" placeholder="Cari judul novel favoritmu..." value="{{ request('search') }}">
        </form>

        <div class="novel-grid">
            @forelse($novels as $novel)
                <a href="{{ route('novel.show', $novel->id) }}" class="novel-card">
                    <div class="cover">
                        @if($novel->cover_image)
                            <img src="{{ asset('storage/'.$novel->cover_image) }}">
                        @else
                            <div style="height:100%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:900;">{{ $novel->title[0] }}</div>
                        @endif
                    </div>
                    <div class="title">{{ $novel->title }}</div>
                    <div class="author">{{ $novel->creator->name }}</div>
                </a>
            @empty
                <p>Novel tidak ditemukan.</p>
            @endforelse
        </div>

            <div style="margin-top: 50px; display: flex; justify-content: center; gap: 20px; align-items: center;">
    @if ($novels->onFirstPage())
        <span style="color: var(--muted); cursor: not-allowed; font-weight: 600;">← Sebelumnya</span>
    @else
        <a href="{{ $novels->previousPageUrl() }}" style="color: var(--text); text-decoration: none; font-weight: 700;">← Sebelumnya</a>
    @endif

    <span style="font-size: 14px; color: var(--muted);">Halaman {{ $novels->currentPage() }} dari {{ $novels->lastPage() }}</span>

    @if ($novels->hasMorePages())
        <a href="{{ $novels->nextPageUrl() }}" style="color: var(--text); text-decoration: none; font-weight: 700;">Selanjutnya →</a>
    @else
        <span style="color: var(--muted); cursor: not-allowed; font-weight: 600;">Selanjutnya →</span>
    @endif
</div>
    </section>
</div>

</body>
</html>