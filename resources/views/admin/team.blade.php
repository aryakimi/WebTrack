@extends('layouts.app')
@section('title', 'Anggota Tim')

@section('content')
<section class="page active">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
      <div>
          <h1>Anggota Tim</h1>
          <p class="subtitle" style="margin:0;">Kenali peran, keahlian, dan kapasitas setiap anggota.</p>
      </div>
      <button class="btn" onclick="document.getElementById('formTambah').style.display='block'">+ Tambah Anggota</button>
  </div>
  
  <div class="grid" style="grid-template-columns:repeat(2,minmax(0,1fr))">
      @forelse($members as $m)
      
      <!-- Simulasi Workload -->
      @php
       // Hitung berapa tugas aktif (selain Done) yang di-assign ke anggota ini
          $activeTasks = \App\Models\Task::where('id_member', $m->id_member)
                            ->where('status', '!=', 'Done')
                            ->count();
          
          // Rumus: Anggap 1 tugas itu bobotnya 25%. 
          // Kalau 4 tugas aktif, berarti 100%. Maksimal mentok di 100%.
          $dummyWorkload = min(100, $activeTasks * 25); 
          
          // Warna: Oren kalau di atas 70%, Hijau kalau aman
          $warnaBar = $dummyWorkload >= 75 ? '#e69a36' : '#16a477';

      @endphp

      <div class="card">
          <div style="display:flex; align-items:center; gap:12px;">
              <!-- Avatar Inisial -->
              <div class="avatar" style="width:45px; height:45px; font-size:16px; background:var(--soft); color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                  {{ substr($m->nama_member, 0, 2) }}
              </div>
              <div>
                  <b style="font-size:15px; color:var(--ink);">{{ $m->nama_member }}</b>
                  <div class="muted" style="margin-top:2px;">{{ $m->posisi }}</div>
              </div>
          </div>

          <div class="project-row" style="margin-top:20px;">
              <span class="muted">Keahlian</span>
              <!-- Menampilkan teks yang lebih rapi -->
              <b style="font-size:12px; color:var(--ink);">
                  @if($m->posisi == 'Frontend') Frontend Developer
                  @elseif($m->posisi == 'Backend') Backend Developer
                  @elseif($m->posisi == 'UI/UX') UI/UX Designer
                  @elseif($m->posisi == 'Testing') QA Tester
                  @else {{ $m->posisi }}
                  @endif
              </b> 
          </div>
          
          <div class="project-row" style="margin-top:10px;">
              <span class="muted">Workload</span>
              <b style="font-size:12px; color:{{ $warnaBar }};">{{ $dummyWorkload }}%</b>
          </div>
          
          <div class="bar" style="height:8px; margin-top:8px; background:var(--line); border-radius:10px; overflow:hidden;">
              <i style="display:block; height:100%; width:{{ $dummyWorkload }}%; background:{{ $warnaBar }}; border-radius:10px;"></i>
          </div>
      </div>
      @empty
      <div class="empty" style="grid-column:1/-1; padding:30px; text-align:center; color:var(--muted);">
          Belum ada data anggota tim. Silakan tambah anggota baru.
      </div>
      @endforelse
  </div>

  <!-- FORM TAMBAH ANGGOTA (POP-UP MODAL) -->
  <div id="formTambah" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); padding:20px; z-index:999;">
      <div class="card" style="max-width:500px; margin:50px auto; padding:30px; background:var(--panel); border-radius:14px;">
          <h3 style="margin-top:0; margin-bottom:20px; color:var(--ink);">Tambah Anggota Tim Baru</h3>
          
          <form action="{{ route('admin.team.store') }}" method="POST" class="form-grid">
              @csrf
              <div class="field full">
                  <label>Nama Lengkap</label>
                  <input type="text" name="nama_member" required placeholder="Contoh: Raka Saputra">
              </div>
              
              <div class="field full">
                  <label>Email</label>
                  <input type="email" name="email" required placeholder="Contoh: raka@webtrack.com">
              </div>
              
              <!-- KOLOM POSISI JADI DROPDOWN -->
              <div class="field full">
                  <label>Posisi / Keahlian</label>
                  <select name="posisi" required style="width:100%; border:1px solid var(--line); border-radius:8px; padding:11px 12px; background:var(--panel); color:var(--ink); font-size:12px; outline:none;">
                      <option value="">-- Pilih Keahlian --</option>
                      <option value="Frontend">Frontend Developer</option>
                      <option value="Backend">Backend Developer</option>
                      <option value="UI/UX">UI/UX Designer</option>
                      <option value="Testing">QA Tester</option>
                  </select>
              </div>
              
              <div class="field full">
                  <label>Password</label>
                  <input type="password" name="password" required placeholder="Minimal 6 karakter">
              </div>
              
              <div class="field full" style="display:flex; gap:10px; margin-top:10px;">
                  <button type="submit" class="btn" style="flex:1;">Simpan Anggota</button>
                  <button type="button" class="btn light" style="flex:1;" onclick="document.getElementById('formTambah').style.display='none'">Batal</button>
              </div>
          </form>
      </div>
  </div>
</section>
@endsection