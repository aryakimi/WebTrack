@extends('layouts.app')
@section('title', 'Proyek Saya')

@section('content')
<section class="page active">
  <div style="margin-bottom:24px;">
      <h1>Proyek Saya</h1>
      <p class="subtitle" style="margin:0;">Daftar proyek di mana Anda terlibat di dalamnya.</p>
  </div>
  
  <div class="grid" style="grid-template-columns: repeat(2, 1fr);">
      @forelse($projects as $p)
      <div class="card" style="padding: 20px; display: flex; flex-direction: column; gap: 15px;">
          
          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
              <div style="display:flex; align-items:center; gap:12px;">
                  <div style="width:40px; height:40px; background:rgba(255,107,0,0.1); color:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px;">
                      📁
                  </div>
                  <div>
                      <b style="font-size:15px; color:var(--text); display:block;">{{ $p->nama_project }}</b>
                      <span style="color:var(--muted); font-size:11px;">Deadline: {{ \Carbon\Carbon::parse($p->deadline)->format('d M Y') }}</span>
                  </div>
              </div>
              <span class="pill" style="background:{{ $p->status == 'Done' ? 'rgba(74,222,128,0.1)' : 'rgba(255,107,0,0.1)' }}; color:{{ $p->status == 'Done' ? '#4ade80' : 'var(--primary)' }}; border:1px solid {{ $p->status == 'Done' ? 'rgba(74,222,128,0.3)' : 'rgba(255,107,0,0.3)' }};">
                  {{ $p->status }}
              </span>
          </div>

          <p style="color:var(--muted); font-size:12px; line-height:1.5; margin:0;">
              {{ $p->deskripsi ? Str::limit($p->deskripsi, 80) : 'Tidak ada deskripsi.' }}
          </p>

          <div>
              <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--muted); margin-bottom:6px;">
                  <span>Progres Proyek</span>
                  <span style="color:var(--text); font-weight:bold;">{{ $p->progress }}%</span>
              </div>
              <div class="bar" style="height:8px; background:var(--line); border-radius:10px; overflow:hidden; margin-top:0;">
                  <i style="display:block; height:100%; width:{{ $p->progress }}%; background:{{ $p->progress == 100 ? '#4ade80' : 'var(--primary)' }}; border-radius:10px; box-shadow: 0 0 10px {{ $p->progress == 100 ? 'rgba(74,222,128,0.4)' : 'rgba(255,107,0,0.4)' }};"></i>
              </div>
          </div>

          <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.02); padding:12px; border-radius:10px; border:1px solid var(--line); margin-top:auto;">
              <div>
                  <div style="font-size:11px; color:var(--muted); margin-bottom:2px;">Tugas Anda di proyek ini</div>
                  <div style="font-size:13px; font-weight:bold; color:var(--text);">
                      {{ $p->my_done_tasks }} / {{ $p->my_total_tasks }} Selesai
                  </div>
              </div>
              <div style="width:32px; height:32px; background:rgba(74,222,128,0.1); color:#4ade80; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                  <i data-lucide="check-circle" style="width:16px;"></i>
              </div>
          </div>
          
      </div>
      @empty
      <div class="card" style="grid-column: 1 / -1; padding: 40px; text-align: center; color: var(--muted);">
          <i data-lucide="folder-open" style="width:40px; height:40px; margin-bottom:10px; opacity:0.5;"></i>
          <div style="font-size:14px;">Anda belum ditugaskan ke proyek manapun saat ini.</div>
      </div>
      @endforelse
  </div>
</section>
@endsection