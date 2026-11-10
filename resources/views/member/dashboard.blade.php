@extends('layouts.app')
@section('title', 'Tugas Saya')

@section('content')
<section class="page active">
  <h1>Tugas Saya (Team Member)</h1>
  <p class="subtitle">Ini adalah tampilan jika Andi (Frontend Developer) login ke sistem.</p>
  
  <div class="notice" style="background:#e6f7f1; color:#168765; margin-bottom:20px; border:1px solid #bbf7d0;">
    <b>Info Sistem:</b> Anda tidak bisa mengerjakan tugas jika prasyaratnya belum selesai.
  </div>

  <div class="table-wrap">
      <!-- TOOLBAR: PENCARIAN & FILTER TANGGAL -->
      <div style="padding: 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; border-bottom:1px solid var(--line);">
          <h3 style="margin:0; font-size:16px;">Daftar Tugas Anda</h3>
          
          <div style="display:flex; gap:10px; align-items:center;">
              <div class="field" style="margin:0;">
                  <input type="date" id="filterDate" onchange="filterByDate()" style="padding:8px 12px; border:1px solid var(--line); background:var(--panel); color:var(--ink); border-radius:8px; font-size:12px;">
              </div>
              <input type="text" id="searchTask" onkeyup="filterTable()" placeholder="Cari tugas..." style="padding:8px 12px; border:1px solid var(--line); background:var(--panel); color:var(--ink); border-radius:8px; font-size:12px; width:200px;">
          </div>
      </div>

    <table style="margin-top:0;">
      <thead>
        <tr>
          <th style="padding: 16px 20px;">Nama Tugas</th>
          <th style="padding: 16px 20px;">Proyek</th>
          <th style="padding: 16px 20px;">Prasyarat</th>
          <th style="padding: 16px 20px;">Status</th>
          <th style="padding: 16px 20px;">Aksi (Update Status)</th>
        </tr>
      </thead>
      <tbody id="taskTable">
        @forelse($myTasks as $t)
        <tr data-date="{{ \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') }}" style="transition:0.3s;" onmouseover="this.style.background='rgba(98, 87, 232, 0.05)'" onmouseout="this.style.background='transparent'">
            <td style="padding: 16px 20px;">
                <b style="font-size:13px; color:var(--ink);">{{ $t->nama_task }}</b>
                
                @php
                    // LOGIKA OVERDUE (TELAT)
                    $isLate = \Carbon\Carbon::parse($t->deadline)->isPast() && $t->status != 'Done';
                @endphp

                <!-- Tampilkan deadline di bawah nama tugas -->
                <div style="font-size:11px; margin-top:4px; font-weight:bold; color: {{ $isLate ? '#df5d68' : '#e69a36' }};">
                    Deadline: {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}
                    
                    <!-- Kalau telat, munculin label merah -->
                    @if($isLate)
                        <span style="background:#fee2e2; color:#b91c1c; padding:2px 6px; border-radius:4px; font-size:10px; margin-left:5px; border:1px solid #fca5a5;">
                            ⚠️ TERLAMBAT!
                        </span>
                    @endif
                </div>
            </td>
            <td style="padding: 16px 20px;">
                <span style="color:var(--muted); font-size:12px;">{{ $t->project->nama_project ?? '-' }}</span>
            </td>
            <td style="padding: 16px 20px;">
                <span style="color:var(--muted); font-size:12px;">{{ $t->prasyarat ?? '-' }}</span>
            </td>
            <td style="padding: 16px 20px;">
                <span class="status {{ $t->status == 'Done' ? 'done' : ($t->status == 'Review' ? 'review' : '') }}" style="padding:6px 12px; font-weight:bold;">
                    {{ $t->status }}
                </span>
            </td>
            <td style="padding: 16px 20px;">
                @php
                    // LOGIKA PENGECEKAN PRASYARAT
                    $isLocked = false;
                    if ($t->prasyarat) {
                        $prasTask = \App\Models\Task::where('nama_task', $t->prasyarat)->first();
                        if ($prasTask && $prasTask->status !== 'Done') {
                            $isLocked = true;
                        }
                    }
                @endphp

                @if($isLocked)
                    <!-- TAMPILAN JIKA TERKUNCI -->
                    <span class="status locked" style="display:inline-block; padding:6px 10px;">🔒 Terkunci (Menunggu "{{ $t->prasyarat }}")</span>
                @else
                    <!-- TAMPILAN JIKA BISA DIUBAH (Form) -->
                    <form action="{{ route('member.tasks.update', $t->id_task) }}" method="POST" style="display:flex; gap:8px; align-items:center;">
                        @csrf
                        <select name="status" style="padding:6px; border-radius:6px; font-size:12px; border:1px solid var(--line); background:var(--panel); color:var(--ink);">
                            <option value="To Do" {{ $t->status == 'To Do' ? 'selected' : '' }}>To Do</option>
                            <option value="In Progress" {{ $t->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Review" {{ $t->status == 'Review' ? 'selected' : '' }}>Review</option>
                            <option value="Done" {{ $t->status == 'Done' ? 'selected' : '' }}>Done</option>
                        </select>
                        <button type="submit" class="btn" style="padding:6px 12px; font-size:12px;">Simpan</button>
                    </form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center; padding: 30px; color:var(--muted);">Belum ada tugas untuk Anda.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

<script>
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
                    if(tr[i].getAttribute('data-date') === document.getElementById("filterDate").value || document.getElementById("filterDate").value === "") {
                        tr[i].style.display = "";
                    }
                } else { tr[i].style.display = "none"; }
            }       
        }
    }
    function filterByDate() {
        let selectedDate = document.getElementById("filterDate").value;
        let table = document.getElementById("taskTable");
        let tr = table.getElementsByTagName("tr");
        for (let i = 0; i < tr.length; i++) {
            let taskDate = tr[i].getAttribute('data-date');
            if (taskDate) {
                if (taskDate === selectedDate || selectedDate === "") { tr[i].style.display = ""; } 
                else { tr[i].style.display = "none"; }
            }
        }
    }
</script>
@endsection