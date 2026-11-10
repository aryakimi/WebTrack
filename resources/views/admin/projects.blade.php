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

  <!-- KOTAK STATISTIK KECIL (Biar ga kosong) -->
  <div class="grid stats" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
      <div class="card" style="padding:16px; border-left: 4px solid #6257e8;">
          <div class="stat-top">Total Proyek</div>
          <div class="stat-num" style="font-size:22px;">{{ $totalProjects }}</div>
      </div>
      <div class="card" style="padding:16px; border-left: 4px solid #16a477;">
          <div class="stat-top">Selesai</div>
          <div class="stat-num" style="font-size:22px; color:#16a477;">{{ $doneProjects }}</div>
      </div>
      <div class="card" style="padding:16px; border-left: 4px solid #e69a36;">
          <div class="stat-top">Berjalan / Pending</div>
          <div class="stat-num" style="font-size:22px; color:#e69a36;">{{ $activeProjects }}</div>
      </div>
  </div>

  <!-- TABEL DAFTAR PROYEK -->
  <div class="table-wrap">
      <div style="padding: 20px 20px 0 20px; display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:16px;">Semua Proyek</h3>
          <!-- Fitur Search (Visual saja, fungsi JS nanti) -->
          <input type="text" id="searchProject" onkeyup="filterTable()" placeholder="Cari nama proyek..." style="padding:8px 12px; border:1px solid #e1e5ee; border-radius:8px; font-size:12px; width:250px;">
      </div>
      
      <table style="margin-top:10px;">
          <thead>
              <tr>
                  <th style="padding: 16px 20px;">Nama Proyek</th>
                  <th style="padding: 16px 20px;">Deskripsi</th>
                  <th style="padding: 16px 20px;">Deadline</th>
                  <th style="padding: 16px 20px; width: 20%;">Progres</th>
                  <th style="padding: 16px 20px;">Status</th>
              </tr>
          </thead>
          <tbody id="projectTable">
              @forelse($projects as $p)
              <tr style="transition:0.3s;" onmouseover="this.style.background='#fafbfe'" onmouseout="this.style.background='white'">
                  <td style="padding: 16px 20px;">
                      <div style="display:flex; align-items:center; gap:12px;">
                          <div style="width:36px; height:36px; background:#eeedff; color:#6257e8; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px;">
                              📁
                          </div>
                          <b style="font-size:13px; color:#172033;">{{ $p->nama_project }}</b>
                      </div>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span style="color:#7b8497; font-size:12px;">
                          {{ strlen($p->deskripsi ?? '') > 5 ? $p->deskripsi : '-' }}
                      </span>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span style="color:#505a70; font-size:12px;">
                          📅 {{ \Carbon\Carbon::parse($p->deadline)->format('d M Y') }}
                      </span>
                  </td>
                  <td style="padding: 16px 20px;">
                      <div style="display:flex; justify-content:space-between; font-size:11px; color:#7b8497; margin-bottom:6px;">
                          <span>{{ $p->progress }}%</span>
                      </div>
                      <div class="bar" style="height:8px; background:#eff1f6; border-radius:10px; overflow:hidden;">
                          <i style="display:block; height:100%; width:{{ $p->progress }}%; background:{{ $p->progress == 100 ? '#16a477' : '#6257e8' }}; border-radius:10px;"></i>
                      </div>
                  </td>
                  <td style="padding: 16px 20px;">
                      <!-- Badge Status Dinamis (Hijau jika Done) -->
                      <span class="pill" style="background:{{ $p->status == 'Done' ? '#e6f7f1' : '#f0efff' }}; color:{{ $p->status == 'Done' ? '#168765' : '#6257e8' }}; padding:6px 12px; font-size:11px; border-radius:20px; font-weight:bold;">
                          {{ $p->status }}
                      </span>
                  </td>
              </tr>
              @empty
              <tr>
                  <td colspan="5" style="text-align:center; padding: 40px; color:#7b8497;">
                      Belum ada proyek. Silakan tambah proyek baru.
                  </td>
              </tr>
              @endforelse
          </tbody>
      </table>
  </div>

  <!-- Form Tambah Proyek (Pop-up) -->
  <div id="formTambah" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); padding:20px; z-index:999;">
      <div class="card" style="max-width:500px; margin:50px auto; padding:30px;">
          <h3 style="margin-top:0; margin-bottom:20px;">Tambah Proyek Baru</h3>
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

<!-- SCRIPT UNTUK FITUR PENCARIAN (SEARCH) -->
<script>
    function filterTable() {
        let input = document.getElementById("searchProject");
        let filter = input.value.toLowerCase();
        let table = document.getElementById("projectTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName("td")[0]; // Cari di kolom pertama (Nama Proyek)
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