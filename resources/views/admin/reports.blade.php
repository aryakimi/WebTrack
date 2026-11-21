@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<style>
    /* CSS Khusus Biar Pas Di-Print Kelihatan Rapi (Hitam Putih) */
    @media print {
        .sidebar, .topbar, .no-print { display: none !important; }
        body { background: white !important; color: black !important; background-image: none !important; }
        .main { margin: 0 !important; padding: 0 !important; }
        .content { padding: 0 !important; }
        .card { border: 1px solid #ccc !important; background: white !important; box-shadow: none !important; color: black !important; }
        .stat-num, h1, h3, b, span, td, th { color: black !important; }
        .pill { border: 1px solid #000 !important; color: black !important; background: transparent !important; }
        th { background: #eee !important; }
    }
</style>

<section class="page active">
  <div class="no-print" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
      <div>
          <h1>Laporan Kinerja & Proyek</h1>
          <p class="subtitle" style="margin:0;">Rekapitulasi data seluruh proyek, tugas, dan performa anggota tim.</p>
      </div>
      <button class="btn" onclick="window.print()" style="display:flex; align-items:center; gap:8px;">
          <i data-lucide="printer" style="width:16px;"></i> Cetak Laporan
      </button>
  </div>

  <!-- REKAP PROYEK -->
  <div class="section-head">
      <h3>Rekap Proyek</h3>
  </div>
  <div class="grid stats" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
      <div class="card" style="padding:16px; border-left: 4px solid var(--primary);">
          <div class="stat-top">Total Proyek</div>
          <div class="stat-num" style="font-size:22px;">{{ $totalProjects }}</div>
      </div>
      <div class="card" style="padding:16px; border-left: 4px solid #4ade80;">
          <div class="stat-top">Proyek Selesai (Done)</div>
          <div class="stat-num" style="font-size:22px; color:#4ade80;">{{ $doneProjects }}</div>
      </div>
      <div class="card" style="padding:16px; border-left: 4px solid #ffb020;">
          <div class="stat-top">Sedang Berjalan</div>
          <div class="stat-num" style="font-size:22px; color:#ffb020;">{{ $onProgressProjects }}</div>
      </div>
  </div>

  <!-- REKAP TUGAS -->
  <div class="section-head">
      <h3>Rekap Status Tugas</h3>
  </div>
  <div class="grid stats" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 30px;">
      <div class="card" style="padding:16px; text-align:center;">
          <div class="stat-top" style="justify-content:center;">Total Tugas</div>
          <div class="stat-num" style="font-size:22px;">{{ $totalTasks }}</div>
      </div>
      <div class="card" style="padding:16px; text-align:center;">
          <div class="stat-top" style="justify-content:center; color:#4ade80;">Done</div>
          <div class="stat-num" style="font-size:22px; color:#4ade80;">{{ $doneTasks }}</div>
      </div>
      <div class="card" style="padding:16px; text-align:center;">
          <div class="stat-top" style="justify-content:center; color:var(--primary);">Review</div>
          <div class="stat-num" style="font-size:22px; color:var(--primary);">{{ $reviewTasks }}</div>
      </div>
      <div class="card" style="padding:16px; text-align:center;">
          <div class="stat-top" style="justify-content:center; color:#ff6b6b;">To Do / Progress</div>
          <div class="stat-num" style="font-size:22px; color:#ff6b6b;">{{ $todoTasks + $inProgressTasks }}</div>
      </div>
  </div>

  <!-- TABEL KINERJA ANGGOTA -->
  <div class="section-head">
      <h3>Rekap Kinerja Anggota Tim</h3>
  </div>
  
  <div class="table-wrap">
      <table>
          <thead>
              <tr>
                  <th style="padding: 12px 16px;">Nama Anggota</th>
                  <th style="padding: 12px 16px;">Posisi</th>
                  <th style="padding: 12px 16px; text-align:center;">Total Tugas</th>
                  <th style="padding: 12px 16px; text-align:center;">Selesai (Done)</th>
                  <th style="padding: 12px 16px; text-align:center;">Tersisa</th>
                  <th style="padding: 12px 16px; text-align:center;">Tingkat Penyelesaian</th>
              </tr>
          </thead>
          <tbody>
              @forelse($teamPerformance as $t)
              <tr style="transition:0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                  <td style="padding: 12px 16px;">
                      <div style="display:flex; align-items:center; gap:10px;">
                          <div style="width:30px; height:30px; background:rgba(255,107,0,0.1); color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:bold;">
                              {{ strtoupper(substr($t->nama_member, 0, 2)) }}
                          </div>
                          <b style="font-size:12px; color:var(--text);">{{ $t->nama_member }}</b>
                      </div>
                  </td>
                  <td style="padding: 12px 16px;">
                      <span style="color:var(--muted); font-size:12px;">{{ $t->posisi }}</span>
                  </td>
                  <td style="padding: 12px 16px; text-align:center; font-size:13px; font-weight:bold;">
                      {{ $t->total_tasks }}
                  </td>
                  <td style="padding: 12px 16px; text-align:center;">
                      <span class="pill" style="background:rgba(74,222,128,0.1); color:#4ade80; border:1px solid rgba(74,222,128,0.3);">{{ $t->done_tasks }}</span>
                  </td>
                  <td style="padding: 12px 16px; text-align:center;">
                      @if($t->pending_tasks > 0)
                          <span class="pill" style="background:rgba(255,107,0,0.1); color:var(--primary); border:1px solid rgba(255,107,0,0.3);">{{ $t->pending_tasks }}</span>
                      @else
                          <span style="color:var(--muted);">-</span>
                      @endif
                  </td>
                  <td style="padding: 12px 16px; text-align:center;">
                      <div style="display:flex; align-items:center; gap:8px; justify-content:center;">
                          <div style="width:60px; height:6px; background:var(--line); border-radius:10px; overflow:hidden;">
                              <div style="width:{{ $t->completion_rate }}%; height:100%; background:#4ade80;"></div>
                          </div>
                          <span style="font-size:11px; font-weight:bold; color:var(--text);">{{ $t->completion_rate }}%</span>
                      </div>
                  </td>
              </tr>
              @empty
              <tr>
                  <td colspan="6" style="text-align:center; padding: 30px; color:var(--muted);">
                      Belum ada data anggota tim.
                  </td>
              </tr>
              @endforelse
          </tbody>
      </table>
  </div>
</section>
@endsection