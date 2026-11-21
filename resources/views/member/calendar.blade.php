@extends('layouts.app')
@section('title', 'Kalender Deadline')

@section('content')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<section class="page active">
  <div style="margin-bottom:24px;">
      <h1>Kalender Deadline</h1>
      <p class="subtitle" style="margin:0;">Lihat jadwal tenggat waktu tugas Anda dalam tampilan kalender.</p>
  </div>
  
  <div class="card" style="padding: 20px;">
      <div id='calendar'></div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var eventsData = @json($events);

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
      events: eventsData,
      height: 'auto',
      contentHeight: 'auto',
      buttonText: { today: 'Hari Ini', month: 'Bulan', week: 'Minggu' },
      eventClick: function(info) {
        alert('Tugas: ' + info.event.title + '\nDeadline: ' + info.event.start.toLocaleDateString());
      }
    });
    calendar.render();
  });
</script>
@endsection

@push('styles')
<style>
    /* FullCalendar Base Styling */
    .fc-theme-standard td, .fc-theme-standard th { border-color: var(--line) !important; }
    .fc-theme-standard .fc-scrollgrid { border-color: var(--line) !important; }
    
    /* Header Hari (Sun, Mon, Tue) - ORANGE */
    .fc-col-header-cell {
        background-color: var(--primary) !important;
    }
    .fc-col-header-cell-cushion {
        color: #000 !important;
        font-weight: 800 !important;
        text-transform: uppercase;
        font-size: 12px;
    }

    /* Day Numbers & Buttons */
    .fc-daygrid-day-number { color: var(--text) !important; font-weight: bold; }
    .fc-button-primary { background-color: var(--bg-card) !important; border-color: var(--line) !important; color: var(--text) !important; }
    .fc-button-primary:hover { background-color: var(--soft) !important; }
    .fc-button-active { background-color: var(--primary) !important; border-color: var(--primary) !important; color: #000 !important; font-weight: bold; }
    
    /* Highlight Today */
    .fc-day-today { background-color: rgba(255, 107, 0, 0.08) !important; }
</style>
@endpush