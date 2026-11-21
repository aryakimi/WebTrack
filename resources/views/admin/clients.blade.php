@extends('layouts.app')
@section('title', 'Daftar Client')

@section('content')
<!-- Panggil Library SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<section class="page active">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
      <div>
          <h1>Daftar Client</h1>
          <p class="subtitle" style="margin:0;">Lihat informasi kontak dan perusahaan yang bekerja sama.</p>
      </div>
  </div>
  
  <!-- KOTAK STATISTIK -->
  <div class="grid stats" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 24px; max-width: 600px;">
      <div class="card" style="padding:16px; border-left: 4px solid var(--primary);">
          <div class="stat-top">Total Client</div>
          <div class="stat-num" style="font-size:22px;">{{ $totalClients }}</div>
      </div>
      <div class="card" style="padding:16px; border-left: 4px solid #4ade80;">
          <div class="stat-top">Client Aktif</div>
          <div class="stat-num" style="font-size:22px; color:#4ade80;">
              {{ $clients->filter(fn($c) => $c->total_projects > 0)->count() }}
          </div>
      </div>
  </div>

  <!-- TABEL DAFTAR CLIENT -->
  <div class="table-wrap">
      <div style="padding: 16px 16px 0 16px;">
          <h3 style="margin:0; font-size:15px;">Semua Client</h3>
      </div>
      
      <table style="margin-top:10px;">
          <thead>
              <tr>
                  <th style="padding: 10px 16px; font-size: 12px;">Nama Client</th>
                  <th style="padding: 10px 16px; font-size: 12px;">Kontak</th>
                  <th style="padding: 10px 16px; font-size: 12px;">Alamat</th>
                  <th style="padding: 10px 16px; font-size: 12px; text-align:center;">Tanggal Mesen</th>
                  <th style="padding: 10px 16px; font-size: 12px; text-align:center;">Proyek</th>
                  <th style="padding: 10px 16px; font-size: 12px; text-align:right;">Aksi</th>
              </tr>
          </thead>
          <tbody>
              @forelse($clients as $c)
              <tr style="transition:0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                  <!-- Nama & Avatar -->
                  <td style="padding: 10px 16px;">
                      <div style="display:flex; align-items:center; gap:10px;">
                          <div style="width:30px; height:30px; background:rgba(255,107,0,0.1); color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:bold;">
                              {{ strtoupper(substr($c->nama_client, 0, 2)) }}
                          </div>
                          <div>
                              <b style="font-size:12px; color:var(--text); display:block;">{{ $c->nama_client }}</b>
                              <span style="color:var(--muted); font-size:10px;">ID: #{{ $c->id_client }}</span>
                          </div>
                      </div>
                  </td>
                  <!-- Kontak (Email & Telp) -->
                  <td style="padding: 10px 16px;">
                      <div style="font-size:11px; color:var(--text);">✉️ {{ $c->email ?? '-' }}</div>
                      <div style="font-size:10px; color:var(--muted); margin-top:2px;">📞 {{ $c->no_telepon ?? '-' }}</div>
                  </td>
                  <!-- Alamat -->
                  <td style="padding: 10px 16px;">
                      <span style="color:var(--muted); font-size:11px; display:block; max-width:200px; line-height:1.3;">
                          {{ $c->alamat ?? '-' }}
                      </span>
                  </td>
                  <!-- Tanggal Mesen -->
                  <td style="padding: 10px 16px; text-align:center;">
                      <span style="color:var(--text); font-size:11px;">
                          @if($c->tanggal_mesen)
                              📅 {{ \Carbon\Carbon::parse($c->tanggal_mesen)->format('d M Y') }}
                          @else
                              -
                          @endif
                      </span>
                  </td>
                  <!-- Total Proyek -->
                  <td style="padding: 10px 16px; text-align:center;">
                      <span class="pill" style="background:rgba(255,255,255,0.05); color:var(--text); padding:4px 10px; font-size:11px; border-radius:20px; font-weight:bold; border:1px solid var(--line);">
                          {{ $c->total_projects }} Proyek
                      </span>
                  </td>
                  <!-- Tombol Hapus -->
                  <td style="padding: 10px 16px; text-align:right;">
                      <form id="formHapusClient-{{ $c->id_client }}" action="{{ route('admin.clients.destroy', $c->id_client) }}" method="POST" style="display:inline-block;">
                          @csrf
                          @method('DELETE')
                          <button type="button" onclick="konfirmasiHapusClient({{ $c->id_client }}, '{{ $c->nama_client }}')" style="background:transparent; color:#ff6b6b; padding:4px 8px; font-size:10px; font-weight:bold; border:1px solid #ff6b6b; cursor:pointer; border-radius:4px; transition:0.3s;" onmouseover="this.style.background='#ff6b6b'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='#ff6b6b';">
                              Hapus
                          </button>
                      </form>
                  </td>
              </tr>
              @empty
              <tr>
                  <td colspan="6" style="text-align:center; padding: 40px; color:var(--muted);">
                      Belum ada data client. Silakan buat proyek baru untuk menambahkan client.
                  </td>
              </tr>
              @endforelse
          </tbody>
      </table>
  </div>
</section>

<!-- SCRIPT SWEETALERT UNTUK HAPUS & NOTIFIKASI ERROR -->
<script>
    function konfirmasiHapusClient(id, nama) {
        Swal.fire({
            title: 'Hapus Client ' + nama + '?',
            text: "Data client akan dihapus permanen. Pastikan client ini tidak memiliki proyek aktif!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff6b00',
            cancelButtonColor: '#333333',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#1a1a1a',
            color: '#f8fafc',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formHapusClient-' + id).submit();
            }
        })
    }

    // Notifikasi Error kalau Client gagal dihapus (karena masih ada proyek)
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            background: '#1a1a1a',
            color: '#f8fafc',
            confirmButtonColor: '#ff6b00',
        });
    @endif
</script>
@endsection