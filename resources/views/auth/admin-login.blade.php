<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restricted Access</title>
    <style>
        :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #666666; --border: #222222; --accent: #ffffff; }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        
        .login-container { background: var(--card); border: 1px solid var(--border); padding: 40px; border-radius: 12px; width: 100%; max-width: 380px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .login-header { margin-bottom: 30px; text-align: center; }
        .login-header h1 { margin: 0; font-size: 18px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        .form-control { width: 100%; padding: 12px 15px; background: var(--bg); border: 1px solid var(--border); color: var(--text); border-radius: 6px; font-family: inherit; font-size: 14px; outline: none; transition: 0.3s; box-sizing: border-box; }
        .form-control:focus { border-color: var(--muted); }
        
        .btn-submit { width: 100%; padding: 14px; background: var(--text); color: var(--bg); border: none; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; font-family: inherit; margin-top: 10px; }
        .btn-submit:hover { opacity: 0.8; }
        
        .error-msg { color: #dc3545; font-size: 12px; margin-top: 10px; text-align: center; background: rgba(220, 53, 69, 0.1); padding: 10px; border-radius: 6px; border: 1px solid rgba(220, 53, 69, 0.2); }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <h1>System Auth</h1> 
        </div>

        <form method="POST" action="{{ route('admin.login.submit', ['token' => $token]) }}">
            @csrf

            <div class="form-group">
                <label for="email">Identity</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter identity..." value="{{ old('email') }}" required autofocus autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Passphrase</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter passphrase..." required>
            </div>

            <button type="submit" class="btn-submit">VERIFY</button>

            @if($errors->any())
                <div class="error-msg">
                    Akses ditolak. Identitas tidak valid.
                </div>
            @endif
        </form>
    </div>

</body>
</html>