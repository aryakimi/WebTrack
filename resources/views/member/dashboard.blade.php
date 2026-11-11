@extends('layouts.app')
@section('title', 'Tugas Saya (Kanban)')

@section('content')
<!-- Panggil Library SortableJS untuk Drag & Drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<section class="page active">
  <h1>Papan Tugas (Kanban)</h1>
  <p class="subtitle">Geser kartu tugas ke kanan untuk mengubah status secara otomatis.</p>
  
  <div class="notice" style="background:rgba(26, 46, 40, 0.8); color:#4ade80; margin-bottom:20px; border:1px solid #166534; padding:15px; border-radius:10px;">
    <b>Info Sistem:</b> Anda tidak bisa memindahkan tugas jika prasyaratnya belum selesai atau waktu sudah habis.
  </div>

  <!-- KANBAN BOARD GRID -->
  <div class="kanban-board" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; align-items: start;">
      
      @php
          $columns = [
              'To Do' => 'To Do',
              'In Progress' => 'In Progress',
              'Review' => 'Review',
              'Done' => 'Done'
          ];
      @endphp

      @foreach($columns as $statusKey => $statusLabel)
      <div class="card" style="padding: 15px; background: rgba(26, 26, 26, 0.4);">
          <!-- Header Kolom -->
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:1px solid var(--line); padding-bottom:10px;">
              <h3 style="margin:0; font-size:14px; color:var(--text);">{{ $statusLabel }}</h3>
              <span class="pill" style="padding:2px 8px; font-size:10px;">{{ $myTasks->where('status', $statusKey)->count() }}</span>
          </div>

          <!-- Area Kartu (Tempat Drop) -->
          <div class="kanban-column" data-status="{{ $statusKey }}" style="min-height: 500px; display:flex; flex-direction:column; gap:10px;">
              
              @foreach($myTasks->where('status', $statusKey) as $t)
                  @php
                      $isOverdue = \Carbon\Carbon::parse($t->deadline)->endOfDay()->isPast() && $t->status != 'Done';
                      $isLocked = false;
                      if ($t->prasyarat) {
                          $prasTask = \App\Models\Task::where('nama_task', $t->prasyarat)->first();
                          if ($prasTask && $prasTask->status !== 'Done') {
                              $isLocked = true;
                          }
                      }
                      // Kalau terkunci, jangan kasih class 'draggable'
                      $dragClass = ($isLocked || $isOverdue) ? '' : 'draggable-card';
                  @endphp

                  <!-- Kartu Tugas -->
                  <div class="kanban-card {{ $dragClass }}" data-id="{{ $t->id_task }}" style="background: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--line); padding: 15px; border-radius: 10px; cursor: {{ ($isLocked || $isOverdue) ? 'not-allowed' : 'grab' }}; box-shadow: 0 4px 6px rgba(0,0,0,0.2); transition: transform 0.2s;">
                      
                      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                          <b style="font-size:13px; color:var(--text); line-height:1.4;">{{ $t->nama_task }}</b>
                      </div>
                      
                      <div style="font-size:11px; color:var(--muted); margin-top:8px;">
                          📁 {{ $t->project->nama_project ?? '-' }}
                      </div>

                      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px;">
                          <small style="color: {{ $isOverdue ? '#ff6b6b' : 'var(--muted)' }}; font-weight:bold;">
                              📅 {{ \Carbon\Carbon::parse($t->deadline)->format('d M') }}
                              @if($isOverdue) (Telat) @endif
                          </small>
                          
                          @if($isLocked)
                              <span class="status" style="background:rgba(255, 107, 107, 0.15); color:#ff6b6b; padding:2px 6px; font-size:9px; border:1px solid rgba(255, 107, 107, 0.3);">🔒 Prasyarat</span>
                          @elseif($isOverdue)
                              <span class="status" style="background:rgba(255, 107, 107, 0.15); color:#ff6b6b; padding:2px 6px; font-size:9px; border:1px solid rgba(255, 107, 107, 0.3);">⏰ Habis</span>
                          @endif
                      </div>
                  </div>
              @endforeach
          </div>
      </div>
      @endforeach

  </div>
</section>

<!-- TOAST NOTIFICATION UNTUK KANBAN -->
<div id="kanbanToast" style="display:none; position: fixed; top: 30px; right: 30px; background: rgba(26, 46, 40, 0.95); backdrop-filter: blur(15px); border: 1px solid #166534; border-left: 4px solid #4ade80; padding: 16px 24px; border-radius: 10px; align-items: center; gap: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); z-index: 9999; color: #f8fafc; font-size: 13px; font-weight: 500; transform: translateY(-150%); transition: transform 0.5s ease;">
    <div style="color: #4ade80; background: rgba(74, 222, 128, 0.1); padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
        <i data-lucide="check-circle" width="20"></i>
    </div>
    <span id="toastText">Status tugas berhasil disimpan!</span>
</div>

<!-- SCRIPT DRAG & DROP & TOAST LOGIC -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const columns = document.querySelectorAll('.kanban-column');

        columns.forEach(column => {
            new Sortable(column, {
                group: 'shared',
                animation: 200,
                ghostClass: 'sortable-ghost',
                draggable: '.draggable-card',
                onEnd: function (evt) {
                    const taskId = evt.item.getAttribute('data-id');
                    const newStatus = evt.to.getAttribute('data-status');
                    const oldStatus = evt.from.getAttribute('data-status');

                    if (newStatus === oldStatus) return;

                    // Efek Loading sementara di kartu
                    evt.item.style.opacity = '0.5';
                    evt.item.style.pointerEvents = 'none';

                    fetch(`/member/tasks/update-kanban/${taskId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            const toast = document.getElementById('kanbanToast');
                            const toastText = document.getElementById('toastText');
                            
                            // Tentukan pesan berdasarkan status
                            let message = "";
                            if (newStatus === 'To Do') message = "Tugas dikembalikan ke To Do.";
                            else if (newStatus === 'In Progress') message = "Tugas mulai dikerjakan (In Progress).";
                            else if (newStatus === 'Review') message = "Tugas dikirim untuk di-Review.";
                            else if (newStatus === 'Done') message = "Mantap! Tugas berhasil diselesaikan (Done).";
                            
                            toastText.innerText = message;

                            // Ubah warna border & background Toast sesuai status
                            if (newStatus === 'Done') {
                                toast.style.borderLeftColor = '#4ade80';
                                toast.style.background = 'rgba(26, 46, 40, 0.95)';
                            } else if (newStatus === 'In Progress') {
                                toast.style.borderLeftColor = '#ff6b00';
                                toast.style.background = 'rgba(30, 25, 20, 0.95)';
                            } else if (newStatus === 'Review') {
                                toast.style.borderLeftColor = '#60a5fa';
                                toast.style.background = 'rgba(20, 30, 40, 0.95)';
                            } else {
                                toast.style.borderLeftColor = '#8b8b8b';
                                toast.style.background = 'rgba(26, 26, 26, 0.95)';
                            }

                            // Tampilkan Toast (animasi turun)
                            toast.style.display = 'flex';
                            setTimeout(() => { toast.style.transform = 'translateY(0)'; }, 50);
                            
                            // Sembunyikan setelah 3 detik
                            setTimeout(() => {
                                toast.style.transform = 'translateY(-150%)';
                                setTimeout(() => { toast.style.display = 'none'; }, 300);
                            }, 3000);
                        }
                        
                        // Balikin efek loading di kartu
                        evt.item.style.opacity = '1';
                        evt.item.style.pointerEvents = 'auto';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mengubah status!');
                        window.location.reload();
                    });
                }
            });
        });
    });
</script>

<style>
    /* Style khusus saat kartu lagi digeser */
    .sortable-ghost {
        opacity: 0.4;
        background: rgba(255, 107, 0, 0.1) !important;
        border: 1px dashed var(--primary) !important;
    }
    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.3);
        border-color: rgba(255, 107, 0, 0.3);
    }
</style>
@endsection