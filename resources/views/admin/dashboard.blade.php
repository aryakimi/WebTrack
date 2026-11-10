@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<section class="page active">
  <h1>Dashboard Admin</h1>
  <p class="subtitle">Pantau aktivitas dan perkembangan seluruh proyek website kamu.</p>
  
  <!-- KOTAK STATISTIK -->
  <div class="grid stats">
    <div class="card"><div class="stat-top">Total Proyek <span class="stat-icon">▣</span></div><div class="stat-num">{{ $totalProjects }}</div><div class="stat-note"><span class="green">●</span> Proyek terdaftar</div></div>
    <div class="card"><div class="stat-top">Tugas Aktif <span class="stat-icon">☷</span></div><div class="stat-num">{{ $totalTasks }}</div><div class="stat-note">Perlu ditindaklanjuti tim</div></div>
    <div class="card"><div class="stat-top">Anggota Tim <span class="stat-icon">♙</span></div><div class="stat-num">{{ $totalMembers }}</div><div class="stat-note">Developer dan designer</div></div>
    <div class="card">
        <div class="stat-top">Tugas Terlambat <span class="stat-icon">⌛</span></div>
        <div class="stat-num">{{ $lateTasks }}</div> <!-- DINAMIS -->
        <div class="stat-note">
            @if($lateTasks > 0)
                <span style="color:#df5d68; font-weight:bold;">⚠️ Perlu perhatian!</span>
            @else
                <span class="orange">Aman terkendali</span>
            @endif
        </div>
    </div>
  </div>

  <!-- KOTAK PROYEK & TUGAS TERBARU -->
  <div class="grid two">
    <div class="card">
      <div class="section-head"><h3>Proyek Terbaru</h3></div>
      @forelse($activeProjects as $p)
        <div class="project" style="padding:16px 0; border-bottom:1px solid var(--line);">
          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
              <div>
                  <b style="font-size:14px; color:var(--ink);">{{ $p->nama_project }}</b>
                  <div class="muted" style="margin-top:4px;">
                      {{ strlen($p->deskripsi ?? '') > 5 ? $p->deskripsi : 'Belum ada deskripsi.' }}
                  </div>
              </div>
              <span class="pill" style="background:{{ $p->status == 'Done' ? '#e6f7f1' : 'var(--soft)' }}; color:{{ $p->status == 'Done' ? '#168765' : 'var(--primary)' }}; padding:5px 10px; font-size:10px; border-radius:20px; font-weight:bold;">
                  {{ $p->status }}
              </span>
          </div>
          <div style="display:flex; align-items:center; gap:6px; margin-top:12px; font-size:11px; color:var(--muted);">
              <span>📅</span>
              <span>Deadline: <b>{{ \Carbon\Carbon::parse($p->deadline)->format('d M Y') }}</b></span>
          </div>
          <div style="margin-top:12px;">
              <div style="display:flex; justify-content:space-between; font-size:10px; color:var(--muted); margin-bottom:5px;">
                  <span>Progres Proyek</span>
                  <b>{{ $p->progress }}%</b>
              </div>
              <div class="bar" style="height:6px; background:var(--line); border-radius:10px; overflow:hidden;">
                  <i style="display:block; height:100%; width:{{ $p->progress }}%; background:{{ $p->progress == 100 ? '#16a477' : '#6257e8' }}; border-radius:10px;"></i> 
              </div>
          </div>
        </div>
      @empty
        <div class="empty" style="padding:20px; text-align:center; color:var(--muted);">Belum ada proyek.</div>
      @endforelse
    </div>

    <div class="card">
      <div class="section-head"><h3>Tugas Terbaru</h3><a href="{{ route('admin.all-tasks') }}" class="btn secondary" style="font-size:10px; padding:5px 10px; text-decoration:none;">Lihat Semua</a></div>
      @forelse($recentTasks as $t)
        <div class="task" style="padding:12px 0; border-bottom:1px solid var(--line);">
          <div>
            <b style="color:var(--ink);">{{ $t->nama_task }}</b>
            <small style="color:var(--muted);">{{ $t->project->nama_project ?? '-' }} · {{ $t->member->nama_member ?? '-' }}</small>
          </div>
          <span class="status {{ $t->status == 'Done' ? 'done' : ($t->status == 'Review' ? 'review' : '') }}">{{ $t->status }}</span>
        </div>
      @empty
        <div class="empty" style="padding:20px; text-align:center; color:var(--muted);">Belum ada tugas.</div>
      @endforelse
    </div>
  </div>

  <!-- KOTAK AKTIVITAS TERBARU -->
  <div class="card" style="margin-top:16px;">
      <div class="section-head"><h3>Aktivitas Terbaru</h3></div>
      <div style="display:flex; flex-direction:column; gap:12px;">
          @forelse($recentActivities as $act)
              <div style="display:flex; align-items:center; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--line); font-size:13px;">
                  <div>
                      <b style="color:var(--primary);">{{ $act->member->nama_member ?? 'Sistem' }}</b> 
                      <span style="color:var(--muted);">memperbarui tugas</span> 
                      <b style="color:var(--ink);">"{{ $act->nama_task }}"</b>
                  </div>
                  <div style="font-size:11px; color:var(--muted); font-weight:bold;">
                      {{ \Carbon\Carbon::parse($act->updated_at)->diffForHumans() }}
                  </div>
              </div>
          @empty
              <div class="empty" style="padding:20px; text-align:center; color:var(--muted);">Belum ada aktivitas hari ini.</div>
          @endforelse
      </div>
  </div>
</section>
@endsection