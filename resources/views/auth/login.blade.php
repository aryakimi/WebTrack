<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — WebTrack</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
    :root {
        /* Tema Gelap Midnight Orange */
        --main-bg: #0a0a0a;       
        --card-bg: rgba(26, 26, 26, 0.6);
        --text-main: #f8fafc;
        --text-muted: #8b8b8b;
        --accent-orange: #ff6b00; 
        --accent-hover: #e65c00;
        --border-color: rgba(255, 255, 255, 0.08);
        
        /* Biar kalender/dropdown otomatis gelap */
        color-scheme: dark; 
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'DM Sans', sans-serif; }

    body {
        background-color: var(--main-bg);
        /* Efek background titik-titik & pendar oranye di tengah */
        background-image: 
            radial-gradient(circle at 50% 0%, rgba(255, 107, 0, 0.15) 0%, transparent 60%),
            radial-gradient(var(--border-color) 1px, transparent 1px);
        background-size: 100% 100%, 30px 30px;
        background-attachment: fixed;
        
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        color: var(--text-main);
    }

    .login-wrapper {
        width: 100%;
        max-width: 420px;
        padding: 20px;
        position: relative;
        z-index: 2;
    }

    /* Card Kaca (Glassmorphism) */
    .login-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 45px 40px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        position: relative;
    }

    /* Garis oranye nyala di atas card */
    .login-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--accent-orange);
        border-radius: 20px 20px 0 0;
        box-shadow: 0 0 15px var(--accent-orange);
    }

    .logo-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 35px;
    }

    .logo-icon {
        width: 55px;
        height: 55px;
        background: var(--accent-orange);
        color: #000;
        display: grid;
        place-items: center;
        border-radius: 14px;
        margin-bottom: 15px;
        box-shadow: 0 0 20px rgba(255, 107, 0, 0.3);
    }

    .logo-icon svg { width: 28px; height: 28px; }

    .logo-text {
        font-family: 'Manrope', sans-serif;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .logo-text span { color: var(--accent-orange); }

    .login-subtitle {
        text-align: center;
        color: var(--text-muted);
        font-size: 13px;
        margin-top: 5px;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper svg {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: var(--text-muted);
        transition: 0.3s;
        z-index: 10;
    }

    .form-control {
        width: 100%;
        padding: 15px 15px 15px 45px; 
        border: 1px solid var(--border-color);
        border-radius: 12px;
        font-size: 14px;
        color: var(--text-main);
        background: rgba(0, 0, 0, 0.3); /* Input agak gelap transparan */
        transition: all 0.3s ease;
        outline: none;
    }

    /* Matikan warna biru autofill browser */
    .form-control:-webkit-autofill,
    .form-control:-webkit-autofill:hover, 
    .form-control:-webkit-autofill:focus, 
    .form-control:-webkit-autofill:active{
        -webkit-box-shadow: 0 0 0 30px #121212 inset !important;
        -webkit-text-fill-color: var(--text-main) !important;
    }

    .form-control::placeholder { color: #555; }

    .form-control:focus {
        border-color: var(--accent-orange);
        background: rgba(0, 0, 0, 0.5);
        box-shadow: 0 0 15px rgba(255, 107, 0, 0.15);
    }

    .form-control:focus + svg { color: var(--accent-orange); }

    .btn-login {
        width: 100%;
        padding: 15px;
        background: var(--accent-orange);
        color: #000; /* Teks tombol gelap biar kontras */
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-login:hover {
        background: var(--accent-hover);
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(255, 107, 0, 0.3);
    }

    .alert-error {
        background: rgba(255, 107, 107, 0.1);
        color: #ff6b6b;
        padding: 12px 15px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 107, 107, 0.3);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .footer-text {
        text-align: center;
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 35px;
    }
</style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        
        <div class="logo-area">
            <div class="logo-icon">
                <i data-lucide="activity"></i>
            </div>
            <div class="logo-text">WebTrack<span>.</span></div>
            <div class="login-subtitle">Masuk ke Workspace Manajemen Proyek</div>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <i data-lucide="alert-circle" width="18"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <input type="email" name="email" class="form-control" required autofocus placeholder="Masukkan email Anda">
                    <i data-lucide="mail"></i>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" class="form-control" required placeholder="Masukkan password Anda">
                    <i data-lucide="lock"></i>
                </div>
            </div>

            <button type="submit" class="btn-login">
                Masuk ke Sistem <i data-lucide="arrow-right" width="18"></i>
            </button>
        </form>

        <div class="footer-text">
            &copy; 2026 WebTrack System. All rights reserved.
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>