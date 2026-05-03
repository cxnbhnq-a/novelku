<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - NovelKu</title>
    <style>
        /* TEMA NOVELKU (Light & Dark Mode) */
        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        
        body { 
            margin: 0; 
            font-family: 'Inter', system-ui, sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            display: flex; 
            min-height: 100vh; 
            align-items: center; 
            justify-content: center;
            padding: 20px;
        }
        * { box-sizing: border-box; }

        /* KOTAK AUTH */
        .auth-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .logo { font-size: 28px; font-weight: 900; color: var(--text); text-decoration: none; letter-spacing: -0.5px; display: block; margin-bottom: 30px; }
        
        .auth-title { font-size: 20px; font-weight: 800; margin: 0 0 10px 0; }
        .auth-desc { font-size: 14px; color: var(--muted); margin-bottom: 30px; line-height: 1.5; }

        /* INPUT OTP KHUSUS */
        .otp-input {
            width: 100%;
            background: var(--bg);
            border: 2px solid var(--border);
            color: var(--text);
            padding: 15px;
            border-radius: 8px;
            font-size: 32px;
            font-weight: 800;
            text-align: center;
            letter-spacing: 15px; /* Biar angkanya berjarak elegan */
            outline: none;
            transition: 0.3s;
            margin-bottom: 20px;
        }
        .otp-input:focus { border-color: var(--muted); box-shadow: 0 0 0 4px rgba(0,0,0,0.05); }
        @media (prefers-color-scheme: dark) { .otp-input:focus { box-shadow: 0 0 0 4px rgba(255,255,255,0.05); } }

        /* TOMBOL & ALERT */
        .btn-primary {
            width: 100%;
            background: var(--text);
            color: var(--bg);
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-primary:hover { opacity: 0.8; transform: translateY(-2px); }

        .error-msg {
            background: #ffe3e3;
            color: #dc3545;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: left;
            border: 1px solid #ffcaca;
        }
        @media (prefers-color-scheme: dark) { .error-msg { background: rgba(220, 53, 69, 0.15); border-color: rgba(220, 53, 69, 0.3); color: #ff8e99; } }
        
        .email-badge { font-weight: 700; color: var(--text); background: var(--bg); padding: 2px 8px; border-radius: 4px; border: 1px solid var(--border); }
    </style>
</head>
<body>

    <div class="auth-card">
        <a href="/" class="logo">NOVELKU.</a>
        
        <h1 class="auth-title">Cek Email Anda</h1>
        <p class="auth-desc">Kami telah mengirimkan 6 digit kode keamanan ke <br><span class="email-badge">{{ session('otp_email') }}</span></p>

        @if ($errors->any())
            <div class="error-msg">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify.post') }}">
            @csrf
            
            <input 
                type="text" 
                name="otp" 
                class="otp-input" 
                maxlength="6" 
                autocomplete="off" 
                autofocus 
                placeholder="------"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                required
            >
            
            <button type="submit" class="btn-primary">Verifikasi & Masuk</button>
        </form>
        
        <div style="margin-top: 25px; font-size: 13px; color: var(--muted);">
            Belum menerima email? <a href="/login" style="color: var(--text); font-weight: 600; text-decoration: none;">Kembali ke Login</a>
        </div>
    </div>

</body>
</html>