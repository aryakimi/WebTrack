@extends('layouts.app')
@section('title', 'Rekan Tim')

@section('content')
<section class="page active">
  <div style="margin-bottom:24px;">
      <h1>Rekan Tim</h1>
      <p class="subtitle" style="margin:0;">Kenali peran dan keahlian anggota tim lainnya di WebTrack.</p>
  </div>
  
  <div class="grid" style="grid-template-columns:repeat(2,minmax(0,1fr))">
      @forelse($members as $m)
      <div class="card">
          <div style="display:flex; align-items:center; gap:12px;">
              <div class="avatar" style="width:45px; height:45px; font-size:16px;">
                  {{ substr($m->nama_member, 0, 2) }}
              </div>
              <div>
                  <b style="font-size:15px; color:var(--text);">{{ $m->nama_member }}</b>
                  <div class="muted" style="margin-top:2px;">{{ $m->posisi }}</div>
              </div>
          </div>

          <div class="project-row" style="margin-top:20px;">
              <span class="muted">Keahlian Utama</span>
              <b style="font-size:12px; color:var(--text);">
                  @if($m->posisi == 'Frontend') Frontend Developer
                  @elseif($m->posisi == 'Backend') Backend Developer
                  @elseif($m->posisi == 'UI/UX') UI/UX Designer
                  @elseif($m->posisi == 'Testing') QA Tester
                  @else {{ $m->posisi }}
                  @endif
              </b> 
          </div>
          
          <div class="project-row" style="margin-top:10px;">
              <span class="muted">Beban Kerja (Workload)</span>
              <b style="font-size:12px; color:{{ $m->workload >= 75 ? '#ff6b00' : '#4ade80' }};">
                  {{ $m->workload }}%
              </b>
          </div>
          
          <div class="bar" style="height:8px; margin-top:8px; background:var(--line); border-radius:10px; overflow:hidden;">
              <i style="display:block; height:100%; width:{{ $m->workload }}%; background:{{ $m->workload >= 75 ? '#ff6b00' : '#4ade80' }}; border-radius:10px;"></i>
          </div>

          <div style="margin-top: 15px; display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--muted);">
              <span style="width: 8px; height: 8px; background: #4ade80; border-radius: 50%; box-shadow: 0 0 5px #4ade80;"></span>
              {{ $m->status }}
          </div>
      </div>
      @empty
      <div class="empty" style="grid-column:1/-1; padding:30px; text-align:center; color:var(--muted);">
          Belum ada data anggota tim lain.
      </div>
      @endforelse
  </div>
</section>
@endsection