@extends('layouts.app')
@section('title', 'Pembagian Tugas')

@section('content')
<section class="page active">
  <h1>Pembagian Tugas (Admin)</h1>
  <p class="subtitle">Tetapkan pekerjaan dan atur ketergantungan tugas (Task Dependency).</p>
  
  <div class="card form-card">
    <div class="section-head">
        <h3>Form Pembagian Tugas</h3>
        <span class="pill">Task assignment</span>
    </div>
    
    <form action="{{ route('admin.tasks.store') }}" method="POST" class="form-grid">
      @csrf
      
      <!-- 1. PROYEK (Dikasih ID untuk JavaScript) -->
      <div class="field">
        <label>Proyek</label>
        <select name="id_project" id="projectDropdown" required>
          <option value="">-- Pilih Proyek --</option>
          @foreach($projects as $p)
            <option value="{{ $p->id_project }}">{{ $p->nama_project }}</option>
          @endforeach
        </select>
      </div>

      <!-- 2. NAMA TUGAS -->
      <div class="field">
        <label>Nama Tugas</label>
        <input type="text" name="nama_task" required placeholder="Contoh: Membuat halaman login">
      </div>

      <!-- 3. KATEGORI SKILL (Filter Anggota) -->
      <div class="field">
        <label>Kategori Skill</label>
        <select id="skillDropdown">
            <option value="">Pilih kategori</option>
            <option value="Frontend">Frontend</option>
            <option value="Backend">Backend</option>
            <option value="UI/UX">UI/UX</option>
            <option value="Testing">Testing</option>
        </select>
      </div>

      <!-- 4. ANGGOTA TIM -->
      <div class="field">
        <label>Anggota Tim</label>
        <select name="id_member" id="memberDropdown" required>
          <option value="">Pilih skill terlebih dahulu</option>
          @foreach($members as $m)
            <option value="{{ $m->id_member }}">{{ $m->nama_member }} ({{ $m->posisi }})</option>
          @endforeach
        </select>
      </div>

      <!-- 5. PRASYARAT (Dikosongkan dulu, diisi JS) -->
      <div class="field">
        <label>Prasyarat (Task Dependency)</label>
        <select name="prasyarat" id="prasyaratDropdown">
            <option value="">Tidak ada prasyarat</option>
        </select>
      </div>

      <!-- 6. DEADLINE -->
      <div class="field">
        <label>Deadline</label>
        <input type="date" name="deadline" required>
      </div>

      <!-- TOMBOL SUBMIT -->
      <div class="field full">
        <div class="hint">Jika diisi prasyarat, tugas ini tidak akan bisa dikerjakan oleh Member sebelum tugas prasyaratnya "Done".</div>
        <button class="btn" type="submit">+ Tetapkan Tugas</button>
      </div>
    </form>
  </div>

  <!-- KOTAK SMART RECOMMENDATION -->
  <div class="card" style="margin-top:18px">
    <div class="section-head">
        <h3>Skill-Based Task Assignment</h3>
        <span class="pill">Smart recommendation</span>
    </div>
    <div id="skillRecommendation" class="notice">
        Pilih kategori skill pada form di atas untuk melihat rekomendasi anggota.
    </div>
  </div>
</section>

<!-- SCRIPT UNTUK FITUR INTERAKTIF -->
<script>
    // --- 1. LOGIKA SKILL-BASED ASSIGNMENT ---
    const membersData = @json($members);

    document.getElementById('skillDropdown').addEventListener('change', function() {
        let selectedSkill = this.value;
        let memberDropdown = document.getElementById('memberDropdown');
        let recommendationBox = document.getElementById('skillRecommendation');
        
        if(selectedSkill === "") {
            memberDropdown.innerHTML = '<option value="">Pilih skill terlebih dahulu</option>';
            recommendationBox.innerHTML = 'Pilih kategori skill pada form di atas untuk melihat rekomendasi anggota.';
            return;
        }

        let filteredMembers = membersData.filter(m => 
            m.posisi.toLowerCase().includes(selectedSkill.toLowerCase()) || 
            (m.skill && m.skill.toLowerCase().includes(selectedSkill.toLowerCase()))
        );
        
        memberDropdown.innerHTML = '<option value="">-- Pilih Anggota --</option>';
        
        if(filteredMembers.length > 0) {
            filteredMembers.forEach(m => {
                memberDropdown.innerHTML += `<option value="${m.id_member}">${m.nama_member} (${m.posisi})</option>`;
            });

            // Otomatis pilih anggota pertama
            memberDropdown.value = filteredMembers[0].id_member;

            // Tampilkan Rekomendasi
            let bestMatch = filteredMembers[0];
            let dummyWorkload = Math.floor(Math.random() * 60) + 30; 
            
            recommendationBox.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <b style="font-size:13px; color:#172033;">Rekomendasi: ${bestMatch.nama_member}</b>
                        <div class="muted" style="margin-top:4px;">${bestMatch.posisi}</div>
                    </div>
                    <span class="pill">100% Skill Match</span>
                </div>
                <div class="project-row" style="margin-top:12px;">
                    <span class="muted">Workload Saat Ini</span>
                    <b style="font-size:12px;">${dummyWorkload}%</b>
                </div>
                <div class="bar">
                    <i style="width:${dummyWorkload}%; background:${dummyWorkload > 80 ? 'var(--orange)' : 'var(--green)'}"></i>
                </div>
            `;
        } else {
            memberDropdown.innerHTML = '<option value="">Tidak ada anggota dengan skill ini</option>';
            recommendationBox.innerHTML = `Belum ada anggota tim yang memiliki keahlian <b>${selectedSkill}</b>.`;
        }
    });

    // --- 2. LOGIKA FILTER PRASYARAT BERDASARKAN PROYEK ---
    const allTasks = @json($tasks);

    document.getElementById('projectDropdown').addEventListener('change', function() {
        const selectedProjectId = this.value;
        const prasyaratDropdown = document.getElementById('prasyaratDropdown');

        // Reset isi dropdown prasyarat
        prasyaratDropdown.innerHTML = '<option value="">Tidak ada prasyarat</option>';

        if (selectedProjectId !== "") {
            // Filter tugas: cuma ambil tugas yang id_project-nya sama
            const filteredTasks = allTasks.filter(t => t.id_project == selectedProjectId);

            filteredTasks.forEach(t => {
                prasyaratDropdown.innerHTML += `<option value="${t.nama_task}">${t.nama_task} (${t.status})</option>`;
            });
        }
    });
</script>
@endsection