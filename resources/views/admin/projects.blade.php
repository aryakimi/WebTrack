@extends('layouts.app')
@section('title', 'Daftar Proyek')

@section('content')
<section class="page active">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
      <div>
          <h1>Daftar Proyek</h1>
          <p class="subtitle" style="margin:0;">Kelola seluruh proyek pembuatan website dalam satu halaman.</p>
      </div>
      <button class="btn" onclick="document.getElementById('formTambah').style.display='block'">+ Tambah Proyek</button>
  </div>

  <!-- KOTAK STATISTIK KECIL -->
  <div class="grid stats" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
      <div class="card" style="padding:16px; border-left: 4px solid var(--primary);">
          <div class="stat-top">Total Proyek</div>
          <div class="stat-num" style="font-size:22px;">{{ $totalProjects }}</div>
      </div>
      <div class="card" style="padding:16px; border-left: 4px solid #16a477;">
          <div class="stat-top">Selesai</div>
          <div class="stat-num" style="font-size:22px; color:#4ade80;">{{ $doneProjects }}</div>
      </div>
      <div class="card" style="padding:16px; border-left: 4px solid var(--primary);">
          <div class="stat-top">Berjalan / Pending</div>
          <div class="stat-num" style="font-size:22px; color:var(--primary);">{{ $activeProjects }}</div>
      </div>
  </div>

  <!-- TABEL DAFTAR PROYEK -->
  <div class="table-wrap">
      <div style="padding: 20px 20px 0 20px; display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:16px;">Semua Proyek</h3>
          <!-- Fitur Search -->
          <input type="text" id="searchProject" onkeyup="filterTable()" placeholder="Cari nama proyek..." style="padding:8px 12px; border:1px solid var(--line); border-radius:8px; font-size:12px; width:250px; background:var(--main); color:var(--text); outline:none;">
      </div>
      
      <table style="margin-top:10px;">
          <thead>
              <tr>
                  <th style="padding: 16px 20px;">Nama Proyek</th>
                  <th style="padding: 16px 20px;">Deskripsi</th>
                  <th style="padding: 16px 20px;">Deadline</th>
                  <th style="padding: 16px 20px; width: 20%;">Progres</th>
                  <th style="padding: 16px 20px;">Status</th>
                  <th style="padding: 16px 20px;">Aksi</th>
              </tr>
          </thead>
          <tbody id="projectTable">
              @forelse($projects as $p)
              <tr style="transition:0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                  <td style="padding: 16px 20px;">
                      <div style="display:flex; align-items:center; gap:12px;">
                          <div style="width:36px; height:36px; background:var(--soft); color:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px;">
                              📁
                          </div>
                          <b style="font-size:13px; color:var(--text);">{{ $p->nama_project }}</b>
                      </div>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span style="color:var(--muted); font-size:12px;">
                          {{ strlen($p->deskripsi ?? '') > 5 ? $p->deskripsi : '-' }}
                      </span>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span style="color:var(--muted); font-size:12px;">
                          📅 {{ \Carbon\Carbon::parse($p->deadline)->format('d M Y') }}
                      </span>
                  </td>
                  <td style="padding: 16px 20px;">
                      <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--muted); margin-bottom:6px;">
                          <span>{{ $p->progress }}%</span>
                      </div>
                      <div class="bar" style="height:8px; background:var(--line); border-radius:10px; overflow:hidden;">
                          <i style="display:block; height:100%; width:{{ $p->progress }}%; background:{{ $p->progress == 100 ? '#16a477' : 'var(--primary)' }}; border-radius:10px; box-shadow: 0 0 10px {{ $p->progress == 100 ? 'rgba(22, 164, 119, 0.4)' : 'rgba(255, 107, 0, 0.4)' }};"></i>
                      </div>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span class="pill" style="background:{{ $p->status == 'Done' ? 'rgba(22, 164, 119, 0.15)' : 'rgba(255, 107, 0, 0.1)' }}; color:{{ $p->status == 'Done' ? '#4ade80' : 'var(--primary)' }}; padding:6px 12px; font-size:11px; border-radius:20px; font-weight:bold; border:1px solid {{ $p->status == 'Done' ? 'rgba(22, 164, 119, 0.3)' : 'rgba(255, 107, 0, 0.2)' }};">
                          {{ $p->status }}
                      </span>
                  </td>
                  <td style="padding: 16px 20px;">
                      <form action="{{ route('admin.projects.destroy', $p->id_project) }}" method="POST" onsubmit="return confirm('Yakin mau hapus proyek ini beserta semua tugasnya?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" style="background:transparent; color:#ff6b6b; padding:6px 10px; font-size:11px; font-weight:bold; border:1px solid #ff6b6b; cursor:pointer; border-radius:6px; transition:0.3s;" onmouseover="this.style.background='#ff6b6b'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='#ff6b6b';">
                              Hapus
                          </button>
                      </form>
                  </td>
              </tr>
              @empty
              <tr>
                  <td colspan="6" style="text-align:center; padding: 40px; color:var(--muted);">
                      Belum ada proyek. Silakan tambah proyek baru.
                  </td>
              </tr>
              @endforelse
          </tbody>
      </table>
  </div>

  <!-- Form Tambah Proyek (Pop-up) -->
  <div id="formTambah" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); padding:20px; z-index:999; backdrop-filter: blur(5px);">
      <div class="card" style="max-width:500px; margin:50px auto; padding:30px;">
          <h3 style="margin-top:0; margin-bottom:20px; color:var(--text);">Tambah Proyek Baru</h3>
          <form action="{{ route('admin.projects.store') }}" method="POST" class="form-grid">
              @csrf
              <div class="field full"><label>Nama Proyek</label><input type="text" name="nama_project" required placeholder="Contoh: Website Toko Online"></div>
              <div class="field full"><label>Deskripsi Singkat</label><textarea name="deskripsi" placeholder="Jelaskan tujuan proyek ini..."></textarea></div>
              <div class="field full"><label>Deadline</label><input type="date" name="deadline" required></div>
              
              <div class="field full" style="display:flex; gap:10px; margin-top:10px;">
                  <button type="submit" class="btn" style="flex:1;">Simpan Proyek</button>
                  <button type="button" class="btn light" style="flex:1;" onclick="document.getElementById('formTambah').style.display='none'">Batal</button>
              </div>
          </form>
      </div>
  </div>
</section>

<!-- SCRIPT UNTUK FITUR PENCARIAN -->
<script>
    function filterTable() {
        let input = document.getElementById("searchProject");
        let filter = input.value.toLowerCase();
        let table = document.getElementById("projectTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                let txtValue = td.textContent || td.innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
@endsection