<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — WebTrack</title>
<style>
:root{--bg:#f5f7fb;--panel:#fff;--ink:#172033;--muted:#7b8497;--line:#e9edf4;--primary:#6257e8;--soft:#eeedff;}
*{box-sizing:border-box}body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:var(--bg);color:var(--ink);display:flex;align-items:center;justify-content:center;min-height:100vh;}
.login-card{background:var(--panel);padding:40px;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.05);width:100%;max-width:400px;border:1px solid var(--line);}
.brand{font-size:28px;font-weight:800;letter-spacing:-.7px;margin-bottom:8px;text-align:center;}.brand span{color:var(--primary)}
.subtitle{text-align:center;font-size:13px;color:var(--muted);margin-bottom:30px;}
.field{display:flex;flex-direction:column;gap:7px;margin-bottom:18px;}
.field label{font-size:12px;font-weight:700;color:#505a70;}
.field input{border:1px solid #e1e5ee;border-radius:8px;padding:12px;font-size:14px;outline:none;transition:0.3s;}
.field input:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--soft);}
.btn{width:100%;border:0;border-radius:9px;background:var(--primary);color:white;padding:12px;font-size:14px;font-weight:700;cursor:pointer;margin-top:10px;transition:0.3s;}
.btn:hover{filter:brightness(.95);}
.alert{background:#fee2e2;color:#b91c1c;padding:12px;border-radius:8px;font-size:12px;margin-bottom:15px;text-align:center;}
</style>
</head>
<body>

<div class="login-card">
    <div class="brand">web<span>track.</span></div>
    <div class="subtitle">Masuk ke Workspace Manajemen Proyek</div>

    {{-- Menampilkan pesan error jika email/password salah --}}
    @if ($errors->any())
        <div class="alert">
            @foreach ($errors->all() as $error)
                {{ $error }} <br>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="field">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@webtrack.com">
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn">Masuk ke Sistem</button>
    </form>
</div>

</body>
</html>