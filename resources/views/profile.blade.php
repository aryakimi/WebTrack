@extends('layouts.app')
@section('title', 'Pengaturan Profil')

@section('content')
<section class="page active">
  <h1>Pengaturan Profil</h1>
  <p class="subtitle">Perbarui informasi akun dan keamanan Anda di sini.</p>
  
  <div class="card form-card" style="margin-top:20px; padding: 30px;">
    
    <!-- FORM UTAMA -->
    <form action="{{ $role == 'admin' ? route('admin.profile.update') : route('member.profile.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
      
      <div style="display: flex; gap: 30px; align-items: center; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid var(--line);">
          
          <!-- Kotak Foto (Preview) -->
          <div style="width: 120px; height: 120px; background: var(--soft); border-radius: 50%; display: grid; place-items: center; border: 3px solid var(--primary); box-shadow: 0 0 20px var(--accent-glow); overflow: hidden; flex-shrink: 0;">
              <!-- ID 'previewFoto' ditambahkan biar bisa diubah pakai JS -->
              <img id="previewFoto" src="{{ $user->foto ? asset('storage/' . $user->foto) : '' }}" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover; display: {{ $user->foto ? 'block' : 'none' }};">
              
              <!-- Inisial huruf (kalau belum ada foto) -->
              <span id="inisialFoto" style="font-size: 40px; font-weight: 800; color: var(--primary); display: {{ $user->foto ? 'none' : 'block' }};">
                  {{ substr($role == 'admin' ? $user->nama : $user->nama_member, 0, 2) }}
              </span>
          </div>

          <!-- Tombol Pilih Foto -->
          <div>
              <h3 style="margin: 0 0 10px 0; font-size: 16px; color: var(--text);">Foto Profil</h3>
              <label class="btn secondary" style="font-size: 12px; padding: 10px 15px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                  <i data-lucide="camera" width="16"></i> Pilih Foto Baru
                  <!-- ID 'inputFoto' dan onchange buat ngejalanin JS -->
                  <input type="file" id="inputFoto" name="foto" accept="image/*" style="display: none;" onchange="previewImage(event)">
              </label>
              <div class="hint" style="margin-top: 8px;">Foto akan tersimpan saat Anda klik "Simpan Perubahan".</div>
          </div>
      </div>

      <!-- Data Diri -->
      <div class="form-grid">
          <div class="field full">
              <label>Nama Lengkap</label>
              <input type="text" name="nama" required value="{{ $role == 'admin' ? $user->nama : $user->nama_member }}">
          </div>

          <div class="field full">
              <label>Email Address</label>
              <input type="email" name="email" required value="{{ $user->email }}">
          </div>

          <div class="field full">
              <label>Password Baru (Opsional)</label>
              <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password">
              <div class="hint">Minimal 6 karakter.</div>
          </div>

          <div class="field full" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
              <a href="{{ $role == 'admin' ? route('admin.dashboard') : route('member.dashboard') }}" class="btn light" style="text-decoration:none;">Batal</a>
              <button type="submit" class="btn">Simpan Perubahan</button>
          </div>
      </div>
    </form>
  </div>
</section>

<!-- SCRIPT UNTUK PREVIEW FOTO -->
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('previewFoto');
            var inisial = document.getElementById('inisialFoto');
            
            // Set gambar preview
            output.src = reader.result;
            output.style.display = 'block';
            
            // Sembunyikan inisial huruf
            inisial.style.display = 'none';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection