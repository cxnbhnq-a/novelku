<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - NovelKu</title>
    <style>
        /* TEMA GLOBAL */
        :root {
            --bg-color: #f4f6f8;
            --card-bg: #ffffff;
            --text-main: #111111;
            --text-muted: #555555;
            --border-color: #e5e7eb;
            --input-bg: #ffffff;
            --btn-bg: #111111;
            --btn-text: #ffffff;
            --btn-hover: #333333;
            --shadow: 0 10px 25px rgba(0,0,0,0.05);
            --success-bg: #d4edda;
            --success-border: #c3e6cb;
            --success-text: #155724;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #000000;
                --card-bg: #0b0b0b;
                --text-main: #f5f5f5;
                --text-muted: #aaaaaa;
                --border-color: #222222;
                --input-bg: #111111;
                --btn-bg: #f5f5f5;
                --btn-text: #000000;
                --btn-hover: #ffffff;
                --shadow: 0 10px 25px rgba(0,0,0,0.5);
                --success-bg: #1e4620;
                --success-border: #2d5a2d;
                --success-text: #95d5b2;
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

        /* LOGO STYLING */
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

        .logo svg { color: var(--text-main); }

        /* KOTAK FORM */
        .auth-card { 
            background: var(--card-bg); 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: var(--shadow); 
            width: 100%; 
            max-width: 420px; 
            box-sizing: border-box;
            border: 1px solid var(--border-color);
        }

        h2 { margin-top: 0; margin-bottom: 10px; text-align: center; font-weight: 800; color: var(--text-main); }
        
        .form-description { 
            text-align: center; 
            font-size: 14px; 
            color: var(--text-muted); 
            margin-bottom: 25px; 
            line-height: 1.5;
        }

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
            border-color: var(--text-main);
            box-shadow: 0 0 0 2px var(--border-color);
        }

        .btn-submit { 
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
        
        .btn-submit:hover { background: var(--btn-hover); transform: translateY(-1px); }

        .footer-link { text-align: center; margin-top: 25px; font-size: 14px; color: var(--text-muted); }
        .footer-link a { color: var(--text-main); font-weight: 700; text-decoration: none; }
        .footer-link a:hover { text-decoration: underline; }

        /* STATUS MESSAGE */
        .success-msg { 
            background: var(--success-bg); 
            color: var(--success-text); 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-size: 14px; 
            border: 1px solid var(--success-border);
        }

        .error-msg { 
            background: rgba(239, 68, 68, 0.1); 
            color: #ef4444; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-size: 14px; 
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        @media (max-width: 768px) {
            .logo-container {
                position: static;
                margin-bottom: 30px;
                display: flex;
                justify-content: center;
            }
            body { flex-direction: column; padding: 30px 20px; }
            .auth-card { padding: 30px; }
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
        <h2>Lupa Password?</h2>
        <p class="form-description">Masukkan email Anda untuk reset Password</p>

        @if (session('status'))
            <div class="success-msg">
                ✓ {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-msg">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label for="email">Email Terdaftar</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus autocomplete="email">

            <button type="submit" class="btn-submit">Reset Password</button>
        </form>

        <div class="footer-link">
            <span>Masuk dengan password? <a href="{{ route('login') }}">Masuk</a></span>
        </div>
    </div>

</body>
</html>
