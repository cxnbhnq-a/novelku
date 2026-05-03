<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $chapter->title }} - {{ $chapter->novel->title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --bg: #f9f9f6; --text: #222222; --muted: #777777; --border: #e2e2e2; --accent: #1a1a1a; }
        @media (prefers-color-scheme: dark) { :root { --bg: #121212; --text: #e0e0e0; --muted: #888888; --border: #333333; --accent: #ffffff; } }
        
        body { margin: 0; font-family: 'Merriweather', 'Georgia', serif; background: var(--bg); color: var(--text); line-height: 1.8; padding-top: 60px; transition: 0.3s; }
        * { box-sizing: border-box; }

        .reader-nav { position: fixed; top: 0; left: 0; width: 100%; background: var(--bg); border-bottom: 1px solid var(--border); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; z-index: 100; box-shadow: 0 2px 10px rgba(0,0,0,0.05); font-family: 'Inter', system-ui, sans-serif; }
        .nav-left { display: flex; align-items: center; gap: 15px; }
        .back-btn { text-decoration: none; color: var(--text); font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 5px; }
        
        /* Tombol Bookmark Style */
        .bookmark-btn { background: none; border: none; cursor: pointer; padding: 5px; display: flex; align-items: center; transition: transform 0.2s; }
        .bookmark-btn:active { transform: scale(0.8); }
        .bookmark-btn svg { width: 24px; height: 24px; transition: 0.3s; }

        .novel-info { display: flex; flex-direction: column; }
        .novel-title { font-size: 14px; font-weight: 700; margin: 0; }
        .author-name { font-size: 12px; color: var(--muted); margin: 0; }

        .reader-container { max-width: 700px; margin: 0 auto; padding: 40px 20px; }
        .chapter-header { text-align: center; margin-bottom: 50px; border-bottom: 1px solid var(--border); padding-bottom: 30px; }
        .chapter-title { font-size: 28px; font-weight: 900; margin: 0 0 10px; line-height: 1.3; }
        .chapter-meta { font-size: 13px; color: var(--muted); font-family: 'Inter', system-ui, sans-serif; }

        .story-content { font-size: 18px; color: var(--text); }
        .story-content p { margin-bottom: 25px; }

        .reader-footer { max-width: 700px; margin: 40px auto 80px; padding: 0 20px; display: flex; justify-content: space-between; font-family: 'Inter', system-ui, sans-serif; }
        .btn-nav { display: inline-block; padding: 12px 25px; border: 1px solid var(--border); border-radius: 50px; text-decoration: none; color: var(--text); font-weight: 600; font-size: 14px; transition: 0.2s; background: transparent; }
        .btn-nav:hover { background: var(--text); color: var(--bg); border-color: var(--text); }
        .btn-nav.disabled { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>

    <nav class="reader-nav">
        @php
            $isBookmarked = \App\Models\Bookmark::where('user_id', auth()->id())
                            ->where('novel_id', $chapter->novel_id)
                            ->exists();
        @endphp

        <div class="nav-left">
            <a href="{{ route('novel.show', $chapter->novel->id) }}" class="back-btn">← kembali</a>
            
            <button id="bookmarkBtn" class="bookmark-btn" data-novel-id="{{ $chapter->novel_id }}">
                    <svg id="love-icon" viewBox="0 0 24 24" fill="{{ $isBookmarked ? '#dc3545' : 'none' }}" stroke="{{ $isBookmarked ? '#dc3545' : 'currentColor' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l8.78-8.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                     </svg>
            </button>

            <div class="novel-info">
                <p class="novel-title">{{ $chapter->novel->title }}</p>
                <p class="author-name">Karya: {{ $chapter->novel->creator->name }}</p>
            </div>
        </div>
    </nav>

    <main class="reader-container">
        <header class="chapter-header">
            <h1 class="chapter-title">{{ $chapter->title }}</h1>
            <span class="chapter-meta">Bab {{ $chapter->chapter_number }} dari "{{ $chapter->novel->title }}"</span>
        </header>

        <article class="story-content">
            {!! $chapter->content !!}
        </article>
    </main>

    <footer class="reader-footer">
        @if($prevChapter)
            <a href="{{ route('baca.chapter', $prevChapter->id) }}" class="btn-nav">← Bab Sebelumnya</a>
        @else
            <span class="btn-nav disabled">← Bab Pertama</span>
        @endif

        @if($nextChapter)
            <a href="{{ route('baca.chapter', $nextChapter->id) }}" class="btn-nav">Bab Selanjutnya →</a>
        @else
            <span class="btn-nav disabled">Bab Terakhir 🎉</span>
        @endif
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookmarkBtn = document.getElementById('bookmarkBtn');
        const icon = document.getElementById('love-icon');
        // Ambil token keamanan dari tag <meta> di header
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if (bookmarkBtn) {
            bookmarkBtn.addEventListener('click', function() {
                // 1. KUNCI TOMBOL SEMENTARA (Anti-Spam / Race Condition)
                bookmarkBtn.disabled = true;
                bookmarkBtn.style.opacity = '0.5';
                
                // Ambil ID secara aman dari atribut data
                const novelId = this.getAttribute('data-novel-id');

                fetch(`/bookmark/${novelId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Koneksi terputus');
                    return response.json();
                })
                .then(data => {
                    // 2. UBAH WARNA LOGO SESUAI STATUS
                    if (data.status === 'added') {
                        icon.setAttribute('fill', '#dc3545');
                        icon.setAttribute('stroke', '#dc3545');
                        showToast(data.message, 'success');
                    } else if (data.status === 'removed') {
                        icon.setAttribute('fill', 'none');
                        icon.setAttribute('stroke', 'currentColor');
                        showToast(data.message, 'info');
                    }
                })
                .catch(error => {
                    console.error('Error keamanan/jaringan:', error);
                    showToast('Gagal memproses, coba lagi!', 'error');
                })
                .finally(() => {
                    // 3. BUKA KUNCI TOMBOL SETELAH SELESAI
                    bookmarkBtn.disabled = false;
                    bookmarkBtn.style.opacity = '1';
                });
            });
        }
    });

    // Fungsi Toast SweetAlert (Sudah Aman)
    function showToast(message, iconType) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            background: getComputedStyle(document.documentElement).getPropertyValue('--bg').trim() || '#fff',
            color: getComputedStyle(document.documentElement).getPropertyValue('--text').trim() || '#000'
        });
        
        Toast.fire({
            icon: iconType,
            title: message // SweetAlert 'title' kebal dari injeksi tag HTML
        });
    }
</script>
</body>
</html>