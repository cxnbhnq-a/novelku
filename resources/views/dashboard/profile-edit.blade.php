<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - NovelKu</title>
    <style>
        /* RESET DASAR BIAR GAK MELUBER */
        *, *::before, *::after { box-sizing: border-box; }

        :root { --bg: #f8f9fa; --card: #ffffff; --text: #1a1a1a; --muted: #6c757d; --border: #e0e0e0; --accent: #111111; --radius: 12px; }
        @media (prefers-color-scheme: dark) { :root { --bg: #050505; --card: #111111; --text: #f5f5f5; --muted: #a0a0a0; --border: #222222; --accent: #ffffff; } }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; overflow-x: hidden; }

        /* SIDEBAR */
        aside { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 30px 20px; display: flex; flex-direction: column; gap: 20px; flex-shrink: 0;}
        .nav-link { padding: 12px 15px; border-radius: var(--radius); text-decoration: none; color: var(--text); font-weight: 500; transition: 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--bg); border: 1px solid var(--border); }

        main { flex: 1; padding: 40px; overflow-y: auto; width: 100%; }

        /* HEADER RAPI */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 15px; flex-wrap: wrap; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .header-left h1 { margin: 0; font-size: 24px; line-height: 1.2; }
        .header-left p { color: var(--muted); margin: 4px 0 0; font-size: 14px; }
        .btn-back { color: var(--text); font-weight: 600; text-decoration: none; font-size: 14px; white-space: nowrap; }

        /* FORM EDIT PROFIL */
        .form-card { background: var(--card); border: 1px solid var(--border); padding: 30px; border-radius: var(--radius); max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 12px; border: 1px solid var(--border); background: var(--bg); color: var(--text); border-radius: 8px; font-family: inherit; font-size: 15px; }
        input:focus { outline: none; border-color: var(--text); }
        .text-hint { font-size: 12px; color: var(--muted); margin-top: 5px; display: block; }

        .btn-submit { background: var(--accent); color: var(--bg); border: none; padding: 14px 24px; font-weight: 600; border-radius: 8px; cursor: pointer; font-size: 15px; width: 100%; transition: 0.2s; margin-top: 10px; }
        .btn-submit:hover { opacity: 0.8; }

        /* MODAL OTP GELAP */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(2px); opacity: 0; transition: opacity 0.2s ease; }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content { background: var(--card); padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); width: 90%; max-width: 320px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2); transform: translateY(20px); transition: transform 0.2s ease; }
        .modal-overlay.show .modal-content { transform: translateY(0); }
        .modal-title { font-size: 18px; font-weight: 700; margin: 0 0 10px; color: var(--text); }
        .modal-text { color: var(--muted); font-size: 14px; margin: 0 0 20px; line-height: 1.5; }
        .modal-actions { display: flex; gap: 10px; justify-content: center; }
        .btn-cancel { padding: 10px 15px; border-radius: 8px; cursor: pointer; flex: 1; border: 1px solid var(--border); background: var(--bg); color: var(--text); font-weight: 600; transition: 0.2s; }
        .btn-cancel:hover { filter: brightness(0.9); }
        .btn-danger { padding: 10px 15px; border-radius: 8px; cursor: pointer; flex: 1; border: none; font-weight: 600; transition: 0.2s; }
        .btn-danger:hover { opacity: 0.8; }

        /* TOAST NOTIFICATION - DARK MODE */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; pointer-events: none; }
        .toast { min-width: 320px; padding: 20px 24px; border-radius: var(--radius); color: #ffffff; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 18px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); transform: translateY(20px); transition: transform 0.3s ease, opacity 0.3s ease; opacity: 0; background-color: #111111; border: 1px solid #222222; }
        .toast.show { transform: translateY(0); opacity: 1; pointer-events: auto; }
        .toast-icon { font-size: 20px; display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 50%; border: 1px solid #222222; }
        .toast.success .toast-icon { color: #28a745; background: rgba(40, 167, 69, 0.1); border-color: rgba(40, 167, 69, 0.3); }
        .toast.error .toast-icon { color: #dc3545; background: rgba(220, 53, 69, 0.1); border-color: rgba(220, 53, 69, 0.3); }
        .toast-text { display: flex; flex-direction: column; gap: 4px; flex: 1; }
        .toast-title { color: #ffffff; font-weight: 700; font-size: 16px; }
        .toast-message { color: #a0a0a0; font-weight: 500; font-size: 14px; }

        /* RESPONSIF HP */
        .hamburger-btn { display: none; background: none; border: none; color: var(--text); font-size: 26px; cursor: pointer; padding: 0; line-height: 1; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 998; opacity: 0; transition: opacity 0.3s ease; }

        @media (max-width: 768px) {
            aside { position: fixed; top: 0; left: -300px; height: 100vh; z-index: 999; box-shadow: 4px 0 15px rgba(0,0,0,0.5); transition: left 0.3s ease; }
            aside.show { left: 0; }
            .hamburger-btn { display: block; }
            .sidebar-overlay.show { display: block; opacity: 1; }
            main { padding: 20px; }
            .header { align-items: flex-start; flex-direction: column; }
            .header-left h1 { font-size: 20px; }
            .form-card { padding: 20px; }
        }
    </style>
</head>
<body>

    <aside>
        <a href="/" style="font-size: 22px; font-weight: 900; margin-bottom: 10px; text-decoration: none; color: inherit; display: block;">NOVELKU.</a>
        <nav style="display:flex; flex-direction:column; gap:5px; margin-top: 20px;">

            <a href="{{ route('dashboard') }}" class="nav-link active">📊 Dashboard</a>
            <a href="{{ route('karya.saya') }}" class="nav-link">📚 Karya Saya</a>
            <a href="{{ route('novel.create') }}" class="nav-link">✍️ Tambah Novel</a>
            <a href="{{ route('profile.edit') }}" class="nav-link">⚙️ Edit Profil</a>

            <form method="POST" action="{{ route('logout') }}" style="margin: 0; margin-top: auto;">
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
            <div class="header-left">
                <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
                <div>
                    <h1>Pengaturan Profil</h1>
                    <p>Atur informasi akun <strong>{{ Auth::user()->name }}</strong></p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-back">&larr; Kembali ke Dashboard</a>
        </div>

        <div class="form-card">
            <form id="profileForm" action="{{ route('profile.updateData') }}" method="POST">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="otp" id="hiddenOtpInput" value="">

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border); margin: 30px 0;">
                <h3 style="font-size: 16px; margin-top: 0;">Ganti Password</h3>

                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin ganti password">
                    <span class="text-hint">Minimal 10 karakter.</span>
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru">
                </div>

                <button type="submit" class="btn-submit">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </main>

    <div id="otpModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="modal-title">Keamanan Ekstra 🛡️</h3>
            <p class="modal-text">Lu ganti data sensitif (Email/Password). Gua udah kirim kode OTP ke <strong>email lama lu</strong>. Cek inbox/spam ya!</p>
            
            <input type="text" id="modalOtpInput" placeholder="Masukkan 6 Digit OTP" style="width: 100%; padding: 12px; margin-bottom: 20px; text-align: center; font-size: 20px; letter-spacing: 5px; border: 1px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text);" autocomplete="off">
            
            <div class="modal-actions">
                <button class="btn-cancel" type="button" onclick="closeOtpModal()">Batal</button>
                <button class="btn-danger" type="button" style="background: var(--accent); color: var(--bg);" onclick="submitOtp()">Verifikasi</button>
            </div>
        </div>
    </div>

    <div class="toast-container">
        <div id="appToast" class="toast @if(session('success') || $errors->any() || session('error')) @if(session('success')) success show @else error show @endif @endif">
            <span class="toast-icon" id="toastIcon">
                @if(session('success')) ✅ @else ⚠️ @endif
            </span>
            <span class="toast-text">
                <span class="toast-title" id="toastTitle">
                    @if(session('success')) Sukses @else Error @endif
                </span>
                <span class="toast-message" id="toastMessage">
                    @if(session('success'))
                        {{ session('success') }}
                    @elseif(session('error'))
                        {{ session('error') }}
                    @elseif($errors->any())
                        {{ $errors->first() }}
                    @endif
                </span>
            </span>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('aside').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // AUTO HIDE TOAST DARI SERVER
        @if (session('success') || session('error') || $errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { document.getElementById('appToast').classList.remove('show'); }, 3500);
        });
        @endif

        // ==========================================
        // LOGIKA AJAX OTP
        // ==========================================
        const currentEmail = "{{ Auth::user()->email }}";
        const form = document.getElementById('profileForm');
        const otpModal = document.getElementById('otpModal');

        form.addEventListener('submit', function(e) {
            const newEmail = document.querySelector('input[name="email"]').value;
            const newPassword = document.querySelector('input[name="password"]').value;
            const hiddenOtp = document.getElementById('hiddenOtpInput').value;

            // Kalau ngubah Email atau ngisi Password TAPI belum ada OTP
            if ((newEmail !== currentEmail || newPassword !== '') && hiddenOtp === '') {
                e.preventDefault(); // Tahan form biar gak nge-reload!
                
                // Ubah teks tombol
                const btn = document.querySelector('.btn-submit');
                const oldText = btn.innerHTML;
                btn.innerHTML = 'Mengirim OTP...';
                btn.style.opacity = '0.7';
                btn.disabled = true;

                // Tembak rute send-otp via AJAX
fetch("{{ route('profile.sendOtp') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                }).then(res => {
                    // TANGKEP ERROR 429 DARI RATE LIMITER LARAVEL
                    if (res.status === 429) {
                        throw new Error('429'); 
                    }
                    if(!res.ok) throw new Error('network');
                    return res.json();
                }).then(data => {
                    // Balikin tombol normal
                    btn.innerHTML = 'Simpan Perubahan';
                    btn.style.opacity = '1';
                    btn.disabled = false;

                    // Munculin Modal & Toast sukses kirim
                    otpModal.classList.add('show');
                    showCustomToast('success', 'Email Terkirim', data.message);
                }).catch(err => {
                    // CEK JENIS ERRORNYA
                    if (err.message === '429') {
                        showCustomToast('error', 'Sabar Der!', 'Lu ngeklik terlalu cepet. Tunggu semenit lagi ya.');
                    } else {
                        showCustomToast('error', 'Gagal', 'Sistem email lagi gangguan der.');
                    }
                    
                    // Balikin tombol
                    btn.innerHTML = 'Simpan Perubahan'; 
                    btn.style.opacity = '1';
                    btn.disabled = false;
                });
            }
        });

        function closeOtpModal() {
            otpModal.classList.remove('show');
        }

        function submitOtp() {
            const userOtp = document.getElementById('modalOtpInput').value;
            if(userOtp.length < 6) {
                showCustomToast('error', 'Waduh', 'OTP harus 6 digit der!');
                return;
            }
            
            // Masukin input OTP ke form utama
            document.getElementById('hiddenOtpInput').value = userOtp;
            
            // Ubah tombol verifikasi biar keliatan loading
            event.target.innerHTML = 'Memproses...';
            event.target.style.opacity = '0.7';
            
            // GAS SUBMIT KE SERVER!
            form.submit();
        }

        // FUNGSI MANGGIL TOAST VIA JS
        function showCustomToast(type, title, message) {
            const toast = document.getElementById('appToast');
            const icon = document.getElementById('toastIcon');
            const titleEl = document.getElementById('toastTitle');
            const msgEl = document.getElementById('toastMessage');

            toast.className = 'toast';
            if (type === 'error') {
                toast.classList.add('error');
                icon.innerText = '⚠️';
            } else {
                toast.classList.add('success');
                icon.innerText = '✅';
            }

            titleEl.innerText = title;
            msgEl.innerText = message;
            toast.classList.add('show');

            setTimeout(() => { toast.classList.remove('show'); }, 3500);
        }
    </script>
</body>
</html>
