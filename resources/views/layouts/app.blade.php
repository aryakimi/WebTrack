<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WebTrack — Workspace</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

:root {
    color-scheme: dark; /* Biar kalender & dropdown otomatis gelap */
    --sidebar: rgba(10, 10, 10, 0.7); 
    --main: #0a0a0a;            
    --card-bg: rgba(26, 26, 26, 0.6); 
    --text: #f8fafc;            
    --muted: #8b8b8b;           
    --line: rgba(255, 255, 255, 0.08);            
    --soft: #262626;            
    --primary: #ff6b00;         
    --primary-hover: #e65c00;
    --accent-glow: rgba(255, 107, 0, 0.15); 
    --sidebar-width: 270px;
    --sidebar-collapsed-width: 85px;
    --transition-speed: 0.4s;
}

body { 
    font-family: 'DM Sans', sans-serif; 
    background-color: var(--main); 
    color: var(--text); 
    overflow-x: hidden; 
    background-image: 
        radial-gradient(circle at 50% 0%, rgba(255, 107, 0, 0.08) 0%, transparent 60%),
        radial-gradient(var(--line) 1px, transparent 1px);
    background-size: 100% 100%, 30px 30px;
    background-attachment: fixed;
}

.layout { display: flex; min-height: 100vh; }

/* ========================================== */
/* SIDEBAR PREMIUM                            */
/* ========================================== */
.sidebar { width: var(--sidebar-width); background: var(--sidebar); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); color: white; padding: 27px 20px 20px; display: flex; flex-direction: column; min-height: 100vh; flex-shrink: 0; transition: width var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; z-index: 100; border-right: 1px solid rgba(255, 107, 0, 0.3); box-shadow: 5px 0 30px rgba(0, 0, 0, 0.5), inset -5px 0 20px rgba(255, 107, 0, 0.05);}
.sidebar.collapsed { width: var(--sidebar-collapsed-width); padding: 27px 15px 20px; }
.sidebar.collapsed .logo h2, .sidebar.collapsed .section-label, .sidebar.collapsed .nav-item span, .sidebar.collapsed .user-info, .sidebar.collapsed .logout-btn span, .sidebar.collapsed .more-icon { opacity: 0; pointer-events: none; white-space: nowrap; transition: opacity 0.2s; }
.sidebar.collapsed .logo { justify-content: center; padding: 0; margin-bottom: 30px; }
.sidebar.collapsed .nav-item { justify-content: center; padding: 0; }
.sidebar.collapsed .nav-item svg { width: 22px; height: 22px; }
.sidebar.collapsed .nav-item.active::before { left: -15px; }
.sidebar.collapsed .logout-btn { padding: 12px 0; justify-content: center; }
.sidebar.collapsed .user-profile { justify-content: center; padding: 10px 0; background: transparent; border:none; }

.logo { display: flex; align-items: center; gap: 12px; padding: 0 7px; margin-bottom: 43px; transition: 0.3s; position: relative; }
.logo-symbol { width: 39px; height: 39px; background: var(--primary); color: #000; display: grid; place-items: center; border-radius: 12px; flex-shrink: 0; box-shadow: 0 0 20px var(--accent-glow); }
.logo-symbol svg { width: 22px; }
.logo h2 { font-family: 'Manrope', sans-serif; font-size: 21px; font-weight: 800; letter-spacing: -1px; transition: opacity 0.2s; }
.logo h2 span { color: var(--primary); text-shadow: 0 0 15px var(--primary); }

.toggle-btn { position: absolute; right: -5px; top: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--line); color: var(--muted); width: 30px; height: 30px; border-radius: 8px; cursor: pointer; display: grid; place-items: center; transition: 0.3s; }
.toggle-btn:hover { color: var(--primary); background: rgba(255, 107, 0, 0.1); box-shadow: 0 0 15px var(--accent-glow); }
.toggle-btn svg { width: 16px; height: 16px; transition: transform 0.4s ease; }
.sidebar.collapsed .toggle-btn svg { transform: rotate(180deg); }

.section-label { color: #555; font-size: 9px; font-weight: 700; letter-spacing: 1.8px; padding: 0 10px; margin-bottom: 13px; transition: 0.3s; }

/* NAVIGASI */
.nav-list { display: flex; flex-direction: column; gap: 6px; }
.nav-item { position: relative; height: 44px; display: flex; align-items: center; gap: 13px; padding: 0 15px; color: #a0a0a0; text-decoration: none; border-radius: 10px; font-size: 12px; font-weight: 500; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.nav-item svg { width: 18px; height: 18px; stroke-width: 1.7; flex-shrink: 0; transition: all 0.3s ease; }
.nav-item:hover { background: rgba(255, 255, 255, 0.05); color: #ffffff; transform: translateX(5px); }
.nav-item:hover svg { color: #ffffff; transform: scale(1.15) rotate(-5deg); }
.nav-item.active { background: linear-gradient(90deg, rgba(255, 107, 0, 0.15) 0%, rgba(255, 107, 0, 0.05) 100%); color: var(--primary); font-weight: 700; box-shadow: inset 0 0 10px rgba(255, 107, 0, 0.1); }
.nav-item.active svg { color: var(--primary); filter: drop-shadow(0 0 8px var(--primary)); }
.nav-item.active::before { content: ""; position: absolute; left: 0; height: 24px; width: 3px; background: var(--primary); border-radius: 0 4px 4px 0; box-shadow: 0 0 15px var(--primary), 0 0 5px var(--primary); }
.nav-item:active { transform: scale(0.98); }

/* SIDEBAR BOTTOM */
.sidebar-bottom { margin-top: auto; transition: 0.3s; }
.logout-btn { width: 100%; background: transparent; border: 1px solid var(--line); color: #ff6b6b; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 12px; margin-bottom: 20px; overflow: hidden; white-space: nowrap; }
.logout-btn:hover { background: rgba(255, 107, 107, 0.1); border-color: #ff6b6b; box-shadow: 0 0 20px rgba(255, 107, 107, 0.2); transform: translateY(-2px); }
.user-profile { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px; background: rgba(255,255,255,0.03); cursor: pointer; transition: 0.3s; margin-top: 15px; overflow: hidden; white-space: nowrap; border: 1px solid var(--line);}
.user-profile:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.3);}
.avatar { width: 38px; height: 38px; background: #333; color: var(--primary); border-radius: 50%; display: grid; place-items: center; font-size: 12px; font-weight: 800; position: relative; flex-shrink: 0; box-shadow: 0 0 10px rgba(255, 107, 0, 0.2);}
.user-info { flex: 1; transition: 0.3s; }
.user-info strong { display: block; font-size: 11px; color: white; }
.user-info small { color: var(--muted); font-size: 9px; display: block; margin-top: 4px; }
.more-icon { color: var(--muted); flex-shrink: 0; transition: 0.3s;}

/* ========================================== */
/* MAIN CONTENT & TOPBAR                      */
/* ========================================== */
.main { flex: 1; min-width: 0; display: flex; flex-direction: column; transition: var(--transition-speed); }
.topbar { height: 75px; background: transparent; border-bottom: 1px solid var(--line); padding: 0 42px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s; position: sticky; top: 0; z-index: 50; backdrop-filter: blur(10px); }
.breadcrumb { font-size: 11px; color: var(--muted); }
.breadcrumb strong { color: var(--text); }
.content { padding: 43px; flex: 1; }

/* ========================================== */
/* CSS PENDUKUNG HALAMAN (CARD, TABEL, DLL)   */
/* ========================================== */
h1 { font-family: 'Manrope', sans-serif; font-size: 30px; letter-spacing: -1.5px; margin: 10px 0 7px; color: var(--text); }
.subtitle { color: var(--muted); font-size: 12px; margin-bottom: 24px; }
.grid { display: grid; gap: 16px; }
.stats { grid-template-columns: repeat(4, minmax(0, 1fr)); margin: 22px 0; }
.card { background: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--line); border-radius: 14px; padding: 20px; transition: 0.3s; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); }
.stat-top { display: flex; justify-content: space-between; color: var(--muted); font-size: 12px; }
.stat-icon { width: 32px; height: 32px; border-radius: 9px; background: rgba(255, 107, 0, 0.1); display: grid; place-items: center; color: var(--primary); box-shadow: 0 0 10px rgba(255, 107, 0, 0.1); border: 1px solid rgba(255, 107, 0, 0.2); }
.stat-num { font-family: 'Manrope', sans-serif; font-size: 27px; font-weight: 800; margin: 13px 0 4px; color: var(--text); }
.stat-note { font-size: 11px; color: var(--muted); }
.two { grid-template-columns: 1.35fr 1fr; }
.section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.section-head h3 { font-family: 'Manrope', sans-serif; font-size: 14px; margin: 0; color: var(--text); }
.muted { color: var(--muted); font-size: 11px; }
.project { padding: 14px 0; border-bottom: 1px solid var(--line); }
.project:last-child { border: 0; padding-bottom: 0; }
.project b { font-size: 13px; color: var(--text); }
.project small { display: block; color: var(--muted); font-size: 11px; margin-top: 5px; }
.pill { font-size: 10px; border-radius: 20px; padding: 5px 9px; background: rgba(255, 107, 0, 0.1); color: var(--primary); border: 1px solid rgba(255, 107, 0, 0.2); font-weight: bold; white-space: nowrap; }
.bar { height: 7px; background: rgba(255,255,255,0.1); border-radius: 9px; margin-top: 12px; overflow: hidden; }
.bar i { display: block; height: 100%; border-radius: 9px; background: var(--primary); transition: 0.3s; box-shadow: 0 0 10px var(--primary); }
.task { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--line); gap: 10px; }
.task:last-child { border: 0; }
.task b { font-size: 12px; color: var(--text); }
.task small { display: block; color: var(--muted); font-size: 10px; margin-top: 5px; }

/* BADGE STATUS (INI YANG TADI HILANG) */
.status { font-size: 10px; padding: 5px 8px; border-radius: 20px; background: rgba(255, 107, 107, 0.1); color: #ff8888; font-weight: bold; border: 1px solid rgba(255, 107, 107, 0.2);}
.status.done { background: rgba(22, 164, 119, 0.15); color: #4ade80; border: 1px solid rgba(22, 164, 119, 0.3);}
.status.review { background: rgba(255, 107, 0, 0.15); color: var(--primary); border: 1px solid rgba(255, 107, 0, 0.3);}

/* TOMBOL (INI JUGA YANG TADI HILANG) */
.btn { border: 0; border-radius: 9px; background: var(--primary); color: #000; padding: 10px 14px; font-size: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; }
.btn:hover { background: var(--primary-hover); box-shadow: 0 0 20px var(--accent-glow); transform: translateY(-1px); }
.btn.secondary { background: rgba(255,255,255,0.05); color: var(--text); border: 1px solid var(--line); }
.btn.light { background: rgba(255,255,255,0.05); color: var(--text); }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.field { display: flex; flex-direction: column; gap: 7px; }
.field label { font-size: 11px; font-weight: 700; color: var(--muted); }
.field input, .field select, .field textarea { width: 100%; border: 1px solid var(--line); border-radius: 8px; padding: 11px 12px; background: #1a1a1a !important; color: #f8fafc !important; font: inherit; font-size: 12px; outline: none; transition: 0.3s;}
.field input:focus, .field select:focus, .field textarea:focus { border-color: var(--primary); box-shadow: 0 0 15px var(--accent-glow); background: #222 !important; }
.field.full { grid-column: 1 / -1; }
.field textarea { min-height: 75px; resize: vertical; }
.hint { font-size: 10px; color: var(--muted); line-height: 1.5; }
.form-card { max-width: 760px; }

.table-wrap { overflow-x: auto; background: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--line); border-radius: 13px; }
table { width: 100%; border-collapse: collapse; min-width: 650px; }
th, td { text-align: left; padding: 14px 16px; border-bottom: 1px solid var(--line); font-size: 11px; }
th { font-size: 10px; color: var(--muted); font-weight: 700; background: rgba(0,0,0,0.3); }
.empty { padding: 30px; text-align: center; color: var(--muted); font-size: 12px; }

/* ========================================== */
/* CSS TOAST NOTIFICATION (POP-UP)            */
/* ========================================== */
.toast-notification {
    position: fixed;
    top: 30px;
    right: 30px;
    background: rgba(26, 26, 26, 0.95);
    backdrop-filter: blur(15px);
    border: 1px solid #166534;
    border-left: 4px solid #4ade80;
    padding: 16px 24px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 20px rgba(74, 222, 128, 0.1);
    z-index: 9999;
    color: #f8fafc;
    font-size: 13px;
    font-weight: 500;
    animation: slideDown 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    transform: translateY(-150%); 
}

@keyframes slideDown {
    to { transform: translateY(0); }
}

.toast-icon {
    color: #4ade80;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(74, 222, 128, 0.1);
    padding: 8px;
    border-radius: 50%;
}

@media(max-width: 900px) {
    .sidebar { width: var(--sidebar-collapsed-width); padding: 27px 15px 20px; }
    .sidebar .logo h2, .sidebar .section-label, .sidebar .nav-item span, .sidebar .user-info, .sidebar .logout-btn span, .sidebar .more-icon { opacity: 0; pointer-events: none; }
    .sidebar .logo { justify-content: center; padding: 0; margin-bottom: 30px; }
    .sidebar .nav-item { justify-content: center; padding: 0; }
    .sidebar .nav-item.active::before { left: -15px; }
    .sidebar .logout-btn { padding: 12px 0; justify-content: center; }
    .sidebar .user-profile { justify-content: center; padding: 10px 0; background: transparent; border:none; }
    .toggle-btn { display: none; }
    .content { padding: 25px; }
    .stats { grid-template-columns: repeat(2, 1fr); }
    .two { grid-template-columns: 1fr; }
}
@media(max-width: 620px) {
    .form-grid { grid-template-columns: 1fr; }
    .field.full { grid-column: auto; }
}
</style>
</head>
<body>

<div class="layout">
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <div class="logo-symbol"><i data-lucide="activity"></i></div>
            <h2>WebTrack<span>.</span></h2>
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i data-lucide="menu" id="toggleIcon"></i>
            </button>
        </div>

        <div class="section-label">MENU UTAMA</div>
        <nav class="nav-list">
            @if(Auth::guard('admin')->check())
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard Admin">
                    <i data-lucide="grid-2x2"></i><span>Dashboard Admin</span>
                </a>
                <a href="{{ route('admin.projects') }}" class="nav-item {{ request()->routeIs('admin.projects') ? 'active' : '' }}" title="Daftar Proyek">
                    <i data-lucide="folder"></i><span>Daftar Proyek</span>
                </a>
                <a href="{{ route('admin.tasks') }}" class="nav-item {{ request()->routeIs('admin.tasks') ? 'active' : '' }}" title="Pembagian Tugas">
                    <i data-lucide="list-checks"></i><span>Pembagian Tugas</span>
                </a>
                <a href="{{ route('admin.all-tasks') }}" class="nav-item {{ request()->routeIs('admin.all-tasks') ? 'active' : '' }}" title="Daftar Tugas">
                    <i data-lucide="clipboard-list"></i><span>Daftar Tugas</span>
                </a>
                <a href="{{ route('admin.team') }}" class="nav-item {{ request()->routeIs('admin.team') ? 'active' : '' }}" title="Anggota Tim">
                    <i data-lucide="users"></i><span>Anggota Tim</span>
                </a>
            @endif

            @if(Auth::guard('member')->check())
                <a href="{{ route('member.dashboard') }}" class="nav-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}" title="Tugas Saya">
                    <i data-lucide="check-square"></i><span>Tugas Saya</span>
                </a>
            @endif
        </nav>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i data-lucide="log-out" width="16"></i> <span>Logout</span>
                </button>
            </form>

            <div class="user-profile" title="{{ Auth::guard('admin')->user()->nama ?? Auth::guard('member')->user()->nama_member ?? 'User' }}">
                <div class="avatar">
                    {{ substr(Auth::guard('admin')->user()->nama ?? Auth::guard('member')->user()->nama_member ?? 'U', 0, 2) }}
                    <span style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background: var(--primary); border: 2px solid #1a1a1a; border-radius: 50%; box-shadow: 0 0 5px var(--primary);"></span>
                </div>
                <div class="user-info">
                    <strong>
                        @if(Auth::guard('admin')->check())
                            {{ Auth::guard('admin')->user()->nama }}
                        @elseif(Auth::guard('member')->check())
                            {{ Auth::guard('member')->user()->nama_member }}
                        @endif
                    </strong>
                    <small>
                        @if(Auth::guard('admin')->check())
                            Administrator
                        @elseif(Auth::guard('member')->check())
                            {{ Auth::guard('member')->user()->posisi }}
                        @endif
                    </small>
                </div>
                <i data-lucide="more-vertical" class="more-icon" style="width: 16px;"></i>
            </div>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="breadcrumb">
                Workspace / <strong>@yield('title')</strong>
            </div>
            <div class="top-actions"></div>
        </header>

        <section class="content">
            <!-- NOTIFIKASI POP-UP (TOAST) -->
            @if(session('success'))
                <div class="toast-notification" id="toastMessage">
                    <div class="toast-icon">
                        <i data-lucide="check-circle" width="20"></i>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</div>

<script>
    lucide.createIcons();

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const icon = document.getElementById('toggleIcon');
        sidebar.classList.toggle('collapsed');
        if(sidebar.classList.contains('collapsed')) { icon.setAttribute('data-lucide', 'chevron-right'); } 
        else { icon.setAttribute('data-lucide', 'menu'); }
        lucide.createIcons();
    }

    // SCRIPT OTOMATIS HILANGKAN TOAST SETELAH 3 DETIK
    const toast = document.getElementById('toastMessage');
    if (toast) {
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
</script>
<style>
    @keyframes fadeOut {
        to { opacity: 0; transform: translateY(-20px); }
    }
</style>
</body>
</html>