<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebTrack — Workspace</title>
    
    <!-- Fonts & Library Eksternal -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Panggil CSS Utama (Hasil Pindahan dari <style> lama) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Stack untuk CSS halaman spesifik (seperti Kalender) -->
    @stack('styles')
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
                <a href="{{ route('admin.clients') }}" class="nav-item {{ request()->routeIs('admin.clients') ? 'active' : '' }}" title="Daftar Client">
                    <i data-lucide="briefcase"></i><span>Daftar Client</span>
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
                <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}" title="Laporan">
                    <i data-lucide="bar-chart-3"></i><span>Laporan</span>
                </a>
            @endif

            @if(Auth::guard('member')->check())
                <a href="{{ route('member.dashboard') }}" class="nav-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}" title="Tugas Saya">
                    <i data-lucide="check-square"></i><span>Tugas Saya</span>
                </a>
                <a href="{{ route('member.projects') }}" class="nav-item {{ request()->routeIs('member.projects') ? 'active' : '' }}" title="Proyek Saya">
                    <i data-lucide="folder"></i><span>Proyek Saya</span>
                </a>
                <a href="{{ route('member.team') }}" class="nav-item {{ request()->routeIs('member.team') ? 'active' : '' }}" title="Rekan Tim">
                    <i data-lucide="users"></i><span>Rekan Tim</span>
                </a>
                <a href="{{ route('member.calendar') }}" class="nav-item {{ request()->routeIs('member.calendar') ? 'active' : '' }}" title="Kalender">
                    <i data-lucide="calendar"></i><span>Kalender</span>
                </a>
            @endif
        </nav>

        <div class="sidebar-bottom">
            <div class="profile-dropdown" id="profileMenu">
                <a href="{{ Auth::guard('admin')->check() ? route('admin.profile') : route('member.profile') }}" class="dropdown-item">
                    <i data-lucide="user-cog"></i> Pengaturan Profil
                </a>
                <div style="height:1px; background:var(--line); margin:4px 8px;"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item danger">
                        <i data-lucide="log-out"></i> Logout
                    </button>
                </form>
            </div>

            <div class="user-profile" onclick="toggleProfileMenu()" title="Menu Profil">
                <div class="avatar">
                    @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->foto)
                        <img src="{{ asset('storage/' . Auth::guard('admin')->user()->foto) }}" alt="Profile">
                    @elseif(Auth::guard('member')->check() && Auth::guard('member')->user()->foto)
                        <img src="{{ asset('storage/' . Auth::guard('member')->user()->foto) }}" alt="Profile">
                    @else
                        {{ substr(Auth::guard('admin')->user()->nama ?? Auth::guard('member')->user()->nama_member ?? 'U', 0, 2) }}
                    @endif
                    <span class="status-dot"></span>
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
            @if(session('success'))
                <div class="toast-notification" id="toastMessage">
                    <div class="toast-icon">
                        <i data-lucide="check-circle" width="20"></i>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div data-aos="fade-up" data-aos-duration="600">
                @yield('content')
            </div>
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

    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        menu.classList.toggle('show');
    }

    document.addEventListener('click', function(event) {
        const menu = document.getElementById('profileMenu');
        const profile = document.querySelector('.user-profile');
        
        if (menu && !menu.contains(event.target) && !profile.contains(event.target)) {
            menu.classList.remove('show');
        }
    });

    const toast = document.getElementById('toastMessage');
    if (toast) {
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
</script>

<!-- Script kecil buat animasi Toast -->
<style>
    @keyframes fadeOut {
        to { opacity: 0; transform: translateY(-20px); }
    }
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
      duration: 800,
      once: true,
  });
</script>
</body>
</html>