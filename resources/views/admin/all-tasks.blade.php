@extends('layouts.app')
@section('title', 'Daftar Tugas')

@section('content')
<section class="page active">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
      <div>
          <h1>Daftar Semua Tugas</h1>
          <p class="subtitle" style="margin:0;">Pantau semua tugas yang telah ditetapkan ke anggota tim.</p>
      </div>
  </div>

  <!-- TABEL DAFTAR TUGAS -->
  <div class="table-wrap">
      <!-- TOOLBAR: PENCARIAN & FILTER TANGGAL -->
      <div style="padding: 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; border-bottom:1px solid var(--line);">
          <h3 style="margin:0; font-size:16px;">Semua Tugas</h3>
          
          <div style="display:flex; gap:10px; align-items:center;">
              <!-- INPUT FILTER TANGGAL -->
              <div class="field" style="margin:0;">
                  <input type="date" id="filterDate" onchange="filterByDate()" style="padding:8px 12px; border:1px solid var(--line); background:var(--panel); color:var(--ink); border-radius:8px; font-size:12px;">
              </div>
              <!-- INPUT PENCARIAN -->
              <input type="text" id="searchTask" onkeyup="filterTable()" placeholder="Cari nama tugas..." style="padding:8px 12px; border:1px solid var(--line); background:var(--panel); color:var(--ink); border-radius:8px; font-size:12px; width:200px;">
          </div>
      </div>

      <table style="margin-top:0;">
          <thead>
              <tr>
                  <th style="padding: 16px 20px;">Nama Tugas</th>
                  <th style="padding: 16px 20px;">Proyek</th>
                  <th style="padding: 16px 20px;">Penanggung Jawab</th>
                  <th style="padding: 16px 20px;">Deadline</th> 
                  <th style="padding: 16px 20px;">Prasyarat</th>
                  <th style="padding: 16px 20px;">Status</th>
              </tr>
          </thead>
          <tbody id="taskTable">
              @forelse($tasks as $t)
              <!-- Tambahkan atribut data-date untuk filter JavaScript -->
              <tr data-date="{{ \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') }}" style="transition:0.3s;" onmouseover="this.style.background='rgba(98, 87, 232, 0.05)'" onmouseout="this.style.background='transparent'">
                  <td style="padding: 16px 20px;">
                      <div style="display:flex; align-items:center; gap:10px;">
                          <div style="width:28px; height:28px; background:var(--soft); color:var(--primary); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px;">
                              ☑
                          </div>
                          <b style="font-size:13px; color:var(--ink);">{{ $t->nama_task }}</b>
                      </div>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span style="color:var(--muted); font-size:12px;">{{ $t->project->nama_project ?? '-' }}</span>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span style="font-weight:bold; font-size:12px; color:var(--primary);">{{ $t->member->nama_member ?? '-' }}</span>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span style="color:#505a70; font-size:12px;">
                          📅 {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}
                      </span>
                  </td>
                  <td style="padding: 16px 20px;">
                      <span style="color:var(--muted); font-size:12px;">
                          {{ $t->prasyarat ?? '-' }}
                      </span>
                  </td>
                  <td style="padding: 16px 20px;">
                      @if($t->status == 'Done')
                          <span class="status done" style="padding:6px 12px; font-weight:bold;">Done</span>
                      @elseif($t->status == 'Review')
                          <span class="status review" style="padding:6px 12px; font-weight:bold;">Review</span>
                      @elseif($t->status == 'In Progress')
                          <span class="status" style="background:#e0f2fe; color:#0369a1; padding:6px 12px; font-weight:bold;">In Progress</span>
                      @else
                          <span class="status" style="padding:6px 12px; font-weight:bold;">To Do</span>
                      @endif
                  </td>
              </tr>
              @empty
              <tr>
                  <td colspan="6" style="text-align:center; padding: 40px; color:var(--muted);">
                      Belum ada tugas yang ditetapkan.
                  </td>
              </tr>
              @endforelse
          </tbody>
      </table>
  </div>
</section>

<!-- SCRIPT UNTUK PENCARIAN & FILTER TANGGAL -->
<script>
    // 1. Fungsi Pencarian (Search)
    function filterTable() {
        let input = document.getElementById("searchTask");
        let filter = input.value.toLowerCase();
        let table = document.getElementById("taskTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                let txtValue = td.textContent || td.innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    // Jangan tampilkan jika sedang difilter tanggal (biar nggak bentrok)
                    if(tr[i].getAttribute('data-date') === document.getElementById("filterDate").value || document.getElementById("filterDate").value === "") {
                        tr[i].style.display = "";
                    }
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }

    // 2. Fungsi Filter Berdasarkan Tanggal
    function filterByDate() {
        let selectedDate = document.getElementById("filterDate").value;
        let table = document.getElementById("taskTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            let taskDate = tr[i].getAttribute('data-date');
            
            if (taskDate) {
                // Jika tanggal di tabel sama dengan tanggal yang dipilih, ATAU filter tanggal kosong (tampilkan semua)
                if (taskDate === selectedDate || selectedDate === "") {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>
@endsection