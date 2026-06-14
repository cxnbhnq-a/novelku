<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - NovelKu</title>
    <style>
        /* VARIABEL TEMA (Light Mode) */
        :root {
            --bg-color: #f4f6f8;
            --card-bg: #ffffff;
            --text-main: #111111;
            --text-muted: #555555;
            --border-color: #e5e7eb;
            --input-bg: #ffffff;
            --btn-bg: #111111; /* Tombol Hitam */
            --btn-text: #ffffff;
            --btn-hover: #333333;
            --shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        /* TEMA DARK MODE (REVISI: Navy ganti Hitam Pekat) */
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #000000; /* Hitam Pekat */
                --card-bg: #0b0b0b; /* Card abu-abu sangat gelap */
                --text-main: #f5f5f5;
                --text-muted: #aaaaaa;
                --border-color: #222222;
                --input-bg: #111111;
                --btn-bg: #f5f5f5; /* Tombol Putih di mode gelap */
                --btn-text: #000000; /* Teks Hitam di tombol putih */
                --btn-hover: #ffffff;
                --shadow: 0 10px 25px rgba(0,0,0,0.5);
            }
        }

        /* LAYOUT UTAMA */
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-color); 
            color: var(--text-main);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 40px 20px; 
            box-sizing: border-box;
            position: relative;
        }

        /* LOGO STYLING (Hapus accent biru, pake warna text utama) */
        .logo-container {
            position: absolute;
            top: 30px;
            left: 40px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 22px;
            font-weight: 900;
            text-decoration: none;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        .logo svg { color: var(--text-main); } /* Ikut warna teks */

        /* KOTAK FORM */
        .auth-card { 
            background: var(--card-bg); 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: var(--shadow); 
            width: 100%; 
            max-width: 400px; 
            box-sizing: border-box;
            border: 1px solid var(--border-color); /* Tambah border tipis */
        }

        h2 { margin-top: 0; margin-bottom: 25px; text-align: center; font-weight: 800; color: var(--text-main); }
        
        label { font-size: 14px; font-weight: 600; color: var(--text-muted); margin-top: 15px; display: block; }
        
        input { 
            width: 100%; 
            padding: 14px; 
            margin: 8px 0 0; 
            border: 1px solid var(--border-color); 
            background: var(--input-bg);
            color: var(--text-main);
            border-radius: 10px; 
            box-sizing: border-box; 
            font-family: inherit; 
            font-size: 15px;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--text-main); /* Focus ikut warna teks */
            box-shadow: 0 0 0 2px var(--border-color);
        }

        /* Checkbox Khusus Remember Me */
        .remember-me {
            display: flex;
            align-items: center;
            margin-top: 15px;
            gap: 8px;
        }
        .remember-me input {
            width: 16px;
            height: 16px;
            margin: 0;
            cursor: pointer;
            accent-color: var(--btn-bg); /* Warna checkbox ikut tombol */
        }
        .remember-me label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-muted);
        }
        
        .btn-auth { 
            width: 100%; 
            padding: 14px; 
            background: var(--btn-bg); 
            color: var(--btn-text); 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            font-weight: 700; 
            font-size: 16px;
            margin-top: 25px; 
            transition: 0.2s;
        }
        
        .btn-auth:hover { background: var(--btn-hover); transform: translateY(-1px); }
        
        .footer-link { text-align: center; margin-top: 25px; font-size: 14px; color: var(--text-muted); display: flex; flex-direction: column; gap: 10px;}
        .footer-link a { color: var(--text-main); font-weight: 700; text-decoration: none; } /* Teks a pake warna utama */
        .footer-link a:hover { text-decoration: underline; }
        
        .error-msg { background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid rgba(239, 68, 68, 0.3); }

        .captcha-group { margin-top: 18px; }
        .captcha-row { display: flex; gap: 12px; align-items: center; margin: 10px 0 8px; }
        .captcha-box {
            flex: 1;
            padding: 14px 16px;
            border: 1px solid var(--border-color);
            background: linear-gradient(135deg, rgba(255,255,255,0.14), rgba(255,255,255,0));
            border-radius: 12px;
            color: var(--text-main);
            font-weight: 700;
            letter-spacing: 1px;
            text-align: center;
            user-select: none;
            min-width: 140px;
        }
        .btn-refresh {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-main);
            cursor: pointer;
            font-weight: 700;
            transition: background 0.2s, transform 0.2s;
        }
        .btn-refresh:hover { background: rgba(255,255,255,0.1); transform: translateY(-1px); }
        .field-error { color: #f87171; margin: 8px 0 0; font-size: 13px; }

        @media (max-width: 768px) {
            .logo-container {
                position: static;
                margin-bottom: 30px;
                display: flex;
                justify-content: center;
            }
            body { flex-direction: column; padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="logo-container">
        <a href="/" class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
            </svg>
            <span>NOVELKU.</span>
        </a>
    </div>

    <div class="auth-card">
        <h2>Selamat Datang Kembali</h2>
        
        @if ($errors->any())
            <div class="error-msg">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf 
            
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email terdaftar" required autofocus>
            
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            @include('auth.partials.captcha', ['captchaQuestion' => $captchaQuestion])

            <div class="remember-me">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Ingat saya</label>
            </div>

            <button type="submit" class="btn-auth">Masuk</button>
        </form>

        <script>
            function initializeCaptchaRefresh() {
                var refreshButton = document.getElementById('refresh-captcha');
                if (! refreshButton) {
                    return;
                }

                refreshButton.addEventListener('click', function () {
                    fetch("{{ route('captcha.refresh') }}", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        var widget = document.getElementById('captcha-widget');
                        if (widget) {
                            widget.outerHTML = data.html;
                            initializeCaptchaRefresh();
                        }
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', initializeCaptchaRefresh);
        </script>

        <div class="footer-link">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Lupa password?</a>
            @endif
            
            <span>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></span>
        </div>
    </div>
</body>
</html>