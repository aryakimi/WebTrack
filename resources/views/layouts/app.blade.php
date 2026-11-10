<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WebTrack — Manajemen Proyek Website</title>
<style>
/* ========================================== */
/* WARNA DEFAULT (MODE TERANG)                */
/* ========================================== */
:root {
    --bg: #f5f7fb;
    --panel: #fff;
    --ink: #172033;
    --muted: #7b8497;
    --line: #e9edf4;
    --primary: #6257e8;
    --soft: #eeedff;
    --green: #16a477;
    --orange: #e69a36;
    --red: #df5d68;
}

/* ========================================== */
/* PENGATURAN WARNA MODE GELAP (DARK MODE)    */
/* ========================================== */
body.dark-mode {
    --bg: #0f172a;
    --panel: #1e293b;
    --ink: #f8fafc;
    --muted: #94a3b8;
    --line: #334155;
    --soft: #334155;
}
body.dark-mode .table-wrap { background: var(--panel); border-color: var(--line); }
body.dark-mode th { background: #0f172a; color: #94a3b8; }
body.dark-mode td { border-bottom: 1px solid var(--line); }
body.dark-mode input, body.dark-mode select, body.dark-mode textarea { background: #0f172a; color: #f8fafc; border-color: #475569; }
body.dark-mode .bar { background: #334155; }
body.dark-mode .topright { background: var(--panel); }
body.dark-mode .card { box-shadow: 0 4px 6px rgba(0,0,0,0.2); }

/* ========================================== */
/* CSS DASAR                                  */
/* ========================================== */
*{box-sizing:border-box}
body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:var(--bg);color:var(--ink);transition: background 0.3s, color 0.3s;}
.app{display:flex;min-height:100vh}
.sidebar{width:245px;background:#171b2e;color:#fff;padding:26px 18px;flex-shrink:0}
.brand{font-size:22px;font-weight:800;letter-spacing:-.7px;margin:0 10px 8px}.brand span{color:#a59fff}
.tag{font-size:11px;color:#9da5bd;margin:0 10px 35px}
.nav-label{font-size:10px;color:#818aa5;font-weight:700;letter-spacing:1.4px;margin:24px 12px 10px}
.nav button{display:flex;align-items:center;gap:12px;width:100%;padding:12px;border:0;border-radius:10px;background:transparent;color:#b8bfd1;text-align:left;font-size:13px;cursor:pointer;margin:3px 0}
.nav button.active,.nav button:hover{background:#2b2e49;color:white}
.nav .ico{width:20px;text-align:center;font-size:16px}
.side-bottom{margin:45px 8px 0;padding:15px;border:1px solid #343951;border-radius:12px;font-size:11px;color:#aeb5c8;line-height:1.6}
.side-bottom b{color:#fff;display:block;font-size:12px;margin-bottom:4px}

main{flex:1;min-width:0;padding:28px 34px 50px}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px}
.crumb{font-size:12px;color:var(--muted)}
.topright{display:flex;align-items:center;gap:16px; background:var(--panel); padding:5px 15px; border-radius:30px; box-shadow:0 2px 5px rgba(0,0,0,0.05); transition:0.3s;} 
.avatar{width:36px;height:36px;border-radius:50%;background:var(--soft);color:var(--primary);display:grid;place-items:center;font-weight:800;font-size:12px;transition:0.3s}
.user{font-size:12px;font-weight:700;line-height:1.2; color:var(--ink);}
.user small{display:block;color:var(--muted);font-weight:400;margin-top:3px}

h1{font-size:25px;margin:0 0 7px;letter-spacing:-.7px}
.subtitle{font-size:13px;color:var(--muted);margin:0 0 24px}
.page{display:none}.page.active{display:block}
.grid{display:grid;gap:16px}
.stats{grid-template-columns:repeat(4,minmax(0,1fr));margin:22px 0}
.card{background:var(--panel);border:1px solid var(--line);border-radius:14px;padding:18px;transition:0.3s}
.stat-top{display:flex;justify-content:space-between;color:var(--muted);font-size:12px}
.stat-icon{width:32px;height:32px;border-radius:9px;background:var(--soft);display:grid;place-items:center;color:var(--primary)}
.stat-num{font-size:27px;font-weight:800;margin:13px 0 4px}
.stat-note{font-size:11px;color:var(--muted)}
.green{color:var(--green)}.orange{color:var(--orange)}

.two{grid-template-columns:1.35fr 1fr}
.section-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.section-head h3{font-size:14px;margin:0}
.muted{color:var(--muted);font-size:11px}
.project{padding:14px 0;border-bottom:1px solid var(--line)}
.project:last-child{border:0;padding-bottom:0}
.project-row{display:flex;justify-content:space-between;gap:10px}
.project b{font-size:13px}
.project small{display:block;color:var(--muted);font-size:11px;margin-top:5px}
.pill{font-size:10px;border-radius:20px;padding:5px 9px;background:var(--soft);color:var(--primary);white-space:nowrap}
.bar{height:7px;background:#eff1f6;border-radius:9px;margin-top:12px;overflow:hidden}
.bar i{display:block;height:100%;border-radius:9px;background:var(--primary)}
.task{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line);gap:10px}
.task:last-child{border:0}
.task b{font-size:12px}
.task small{display:block;color:var(--muted);font-size:10px;margin-top:5px}
.status{font-size:10px;padding:5px 8px;border-radius:20px;background:#fff4e5;color:#b77719}
.status.done{background:#e6f7f1;color:#168765}
.status.review{background:var(--soft);color:var(--primary)}
.status.locked{background:#fee2e2; color:#b91c1c;}

.btn{border:0;border-radius:9px;background:var(--primary);color:white;padding:10px 14px;font-size:12px;font-weight:700;cursor:pointer;transition:0.3s}
.btn:hover{filter:brightness(.96)}
.btn.secondary{background:var(--soft);color:var(--primary)}
.btn.light{background:#f3f5f9;color:#505a70}
.toolbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:16px}
.toolbar input,.toolbar select,.field input,.field select,.field textarea{border:1px solid var(--line);border-radius:8px;padding:11px 12px;background:var(--panel);color:var(--ink);font:inherit;font-size:12px;outline:none;transition:0.3s;}
.toolbar input:focus,.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--primary)}
.toolbar input{min-width:220px}

.table-wrap{overflow-x:auto;background:var(--panel);border:1px solid var(--line);border-radius:13px}
table{width:100%;border-collapse:collapse;min-width:650px}
th,td{text-align:left;padding:14px 16px;border-bottom:1px solid var(--line);font-size:11px}
th{font-size:10px;color:var(--muted);font-weight:700;background:#fafbfe}
td b{font-size:12px}
tr:last-child td{border:0}

.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.field{display:flex;flex-direction:column;gap:7px}
.field label{font-size:11px;font-weight:700;color:var(--muted)}
.field input,.field select,.field textarea{width:100%}
.field.full{grid-column:1/-1}
.field textarea{min-height:75px;resize:vertical}
.form-card{max-width:760px}
.hint{font-size:10px;color:var(--muted);line-height:1.5}
.notice{padding:12px 14px;background:var(--soft);color:var(--primary);border-radius:9px;font-size:11px;line-height:1.5;margin:14px 0}
.empty{padding:30px;text-align:center;color:var(--muted);font-size:12px}

@media(max-width:900px){.sidebar{width:205px}main{padding:22px}.stats{grid-template-columns:repeat(2,1fr)}.two{grid-template-columns:1fr}}
@media(max-width:620px){.sidebar{width:65px;padding:18px 8px}.brand{font-size:0;margin:0 7px 32px}.brand span{font-size:20px}.tag,.nav-label,.nav button .txt,.side-bottom{display:none}.nav button{justify-content:center;padding:13px 5px}.nav .ico{font-size:18px}main{padding:18px 13px}.stats{gap:9px}.card{padding:13px}.stat-num{font-size:22px}.topbar{margin-bottom:22px}.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}h1{font-size:22px}}
</style>
</head>
<body>
<div class="app">
<aside class="sidebar">
  <div class="brand">web<span>track.</span></div><div class="tag">WEBSITE PROJECT MANAGEMENT</div>
  
  <div class="nav-label">MENU UTAMA</div>
  <nav class="nav">
    {{-- JIKA YANG LOGIN ADALAH ADMIN --}}
    @if(Auth::guard('admin')->check())
        <a href="{{ route('admin.dashboard') }}" style="text-decoration:none;"><button class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="ico">▦</span><span class="txt">Dashboard Admin</span></button></a>
        <a href="{{ route('admin.projects') }}" style="text-decoration:none;"><button class="{{ request()->routeIs('admin.projects') ? 'active' : '' }}"><span class="ico">▣</span><span class="txt">Daftar Proyek</span></button></a>
        <a href="{{ route('admin.tasks') }}" style="text-decoration:none;"><button class="{{ request()->routeIs('admin.tasks') ? 'active' : '' }}"><span class="ico">☷</span><span class="txt">Pembagian Tugas</span></button></a>
        <a href="{{ route('admin.all-tasks') }}" style="text-decoration:none;"><button class="{{ request()->routeIs('admin.all-tasks') ? 'active' : '' }}"><span class="ico">📋</span><span class="txt">Daftar Tugas</span></button></a>
        <a href="{{ route('admin.team') }}" style="text-decoration:none;"><button class="{{ request()->routeIs('admin.team') ? 'active' : '' }}"><span class="ico">♙</span><span class="txt">Anggota Tim</span></button></a>
    @endif

    {{-- JIKA YANG LOGIN ADALAH MEMBER --}}
    @if(Auth::guard('member')->check())
        <a href="{{ route('member.dashboard') }}" style="text-decoration:none;"><button class="{{ request()->routeIs('member.dashboard') ? 'active' : '' }}"><span class="ico">👤</span><span class="txt">Tugas Saya</span></button></a>
    @endif
  </nav>

  <!-- TOMBOL LOGOUT -->
  <form method="POST" action="{{ route('logout') }}" style="margin-top: 30px; padding: 0 10px;">
      @csrf
      <button type="submit" style="width:100%; background:#df5d68; color:white; border:none; padding:10px; border-radius:8px; font-weight:bold; cursor:pointer; transition:0.3s;">
          Logout
      </button>
  </form>

  <div class="side-bottom"><b>WebTrack Workspace</b>Kelola proyek dan tugas dengan mudah.</div>
</aside>
<main>

<header class="topbar">
  <div class="crumb">Workspace / <span>@yield('title')</span></div>
  
  <div style="display:flex; align-items:center; gap:15px;">
      <!-- TOMBOL DARK MODE -->
      <button onclick="toggleDarkMode()" style="background:transparent; border:1px solid var(--line); color:var(--ink); padding:8px 12px; border-radius:20px; cursor:pointer; display:flex; align-items:center; gap:8px; font-size:12px; font-weight:bold; transition:0.3s;">
          <span id="darkModeIcon">🌙</span> <span id="darkModeText">Mode Gelap</span>
      </button>

      <!-- PROFIL USER -->
      <div class="topright">
        <div class="avatar" id="avatarDisplay">
            {{ substr(Auth::guard('admin')->user()->nama ?? Auth::guard('member')->user()->nama_member ?? 'U', 0, 2) }}
        </div>
        
        <div class="user" id="userDisplay">
            @if(Auth::guard('admin')->check())
                {{ Auth::guard('admin')->user()->nama }}
                <small>Administrator (Pemilik)</small>
            @elseif(Auth::guard('member')->check())
                {{ Auth::guard('member')->user()->nama_member }}
                <small>{{ Auth::guard('member')->user()->posisi }}</small>
            @endif
        </div>
      </div>
  </div>
</header>

<!-- ============================================== -->
<!-- FITUR NOTIFIKASI (FLASH MESSAGE) -->
<!-- ============================================== -->
@if(session('success'))
    <div style="background-color: #dcfce7; color: #15803d; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: bold; border: 1px solid #bbf7d0; display: flex; justify-content: space-between; align-items: center; animation: fadeIn 0.5s;">
        <span>✅ {{ session('success') }}</span>
        <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; color:#15803d; font-size:18px; cursor:pointer; font-weight:bold;">✕</button>
    </div>
@endif

<!-- INI TEMPAT KONTEN DINAMIS -->
@yield('content')

</main></div>

<!-- SCRIPT UNTUK DARK MODE -->
<script>
    // Cek memori browser, kalau sebelumnya udah milih mode gelap, langsung aktifin
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        document.getElementById('darkModeIcon').innerText = '☀️';
        document.getElementById('darkModeText').innerText = 'Mode Terang';
    }

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        
        let icon = document.getElementById('darkModeIcon');
        let text = document.getElementById('darkModeText');

        // Jika sekarang aktif (gelap)
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark'); // Simpan ingatan
            icon.innerText = '☀️';
            text.innerText = 'Mode Terang';
        } else {
            localStorage.setItem('theme', 'light'); // Simpan ingatan
            icon.innerText = '🌙';
            text.innerText = 'Mode Gelap';
        }
    }
</script>
</body>
</html>