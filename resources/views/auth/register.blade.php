@extends('layouts.auth')

@section('content')
<style>
    /* === 1. STYLE BACKGROUND & OVERLAY (SAMA DENGAN LOGIN) === */
    body {
        /* Pastikan nama file gambar sama dengan yang di Login */
        background-image: url("{{ asset('img/image2.jpg') }}"); 
        
        background-repeat: no-repeat;
        background-position: center center;
        background-attachment: fixed;
        background-size: cover;
        background-color: #4a4a4a;
    }

    /* Overlay Gelap Transparan */
    .bg-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 0;
    }

    /* === 2. STYLE KARTU FORM === */
    .register-card {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        width: 100%;
        max-width: 500px; /* Lebar sedikit lebih besar dari login agar muat banyak field */
        position: relative;
        z-index: 1;
    }
</style>

{{-- Overlay Background --}}
<div class="bg-overlay"></div>

{{-- Container Utama --}}
<div class="d-flex align-items-center justify-content-center min-vh-100 p-3">
    
    <div class="register-card my-4">
        
        {{-- Header --}}
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">Buat Akun Baru</h2>
        </div>

        {{-- Alert Error --}}
        @if(session('error'))
            <div class="alert alert-danger py-2 small mb-3">
                {{ session('error') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger py-2 small mb-3">
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- === FORMULIR PENDAFTARAN === --}}
        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- 1. NAMA LENGKAP (Ada ID untuk Javascript) --}}
            <div class="mb-3" id="nama-field">
                <label for="name" class="form-label fw-bold small">Nama Lengkap</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Ahmad Dahlan">
            </div>

            {{-- 2. NISN --}}
            <div class="mb-3">
                <label for="nisn" class="form-label fw-bold small">NISN</label>
                <input type="text" class="form-control" id="nisn" name="nisn" value="{{ old('nisn') }}" required placeholder="Masukkan NISN Anda">
            </div>

            {{-- 3. JENJANG (Code dari Anda) --}}
            <div class="mb-3">
                <label class="form-label fw-bold small">Jenjang / Kategori Asal</label>
                <select name="jenjang_tambahan" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Jenjang --</option>
                    <option value="SD" {{ old('jenjang_tambahan') == 'SD' ? 'selected' : '' }}>SD / MI (Sederajat)</option>
                    <option value="SMP" {{ old('jenjang_tambahan') == 'SMP' ? 'selected' : '' }}>SMP / MTs (Sederajat)</option>
                </select>
            </div>

            {{-- 4. EMAIL --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-bold small">Alamat Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com">
            </div>

            {{-- 5. PASSWORD --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-bold small">Kata Sandi</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Minimal 8 karakter">
            </div>

            {{-- 6. KONFIRMASI PASSWORD --}}
            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-bold small">Konfirmasi Kata Sandi</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi">
            </div>

            {{-- 7. PILIHAN ROLE (Code dari Anda) --}}
            <div class="mb-4">
                <label for="role" class="form-label fw-bold small">Daftar Sebagai</label>
                <select class="form-control" id="role" name="role" required>
                    {{-- PERBAIKAN DI SINI: value kosong & label yang benar --}}
                    <option value="" disabled selected>-- Pilih Daftar Sebagai --</option>
                    <option value="user">Calon Siswa (User)</option>
                    
                    @if(isset($bisaDaftarAdmin) && $bisaDaftarAdmin)
                        <option value="admin">Admin (Staff)</option>
                    @endif
                </select>
            </div>

            {{-- TOMBOL DAFTAR (Warna Hijau agar senada dengan Login) --}}
            <button type="submit" class="btn btn-success w-100 py-2 fw-bold mb-3" style="background-color: #2ecc71; border-color: #2ecc71;">
                Buat Pengguna
            </button>

            {{-- LINK KE LOGIN --}}
            <div class="text-center">
                <span class="text-muted small">Sudah punya akun?</span>
                <a href="{{ route('login') }}" class="text-decoration-none small text-primary fw-bold">
                    Login disini
                </a>
            </div>
        </form>
    </div>
</div>

{{-- === SCRIPT LOGIC (Code dari Anda - Tetap Dipertahankan) === --}}
<script>
    const roleSelect = document.getElementById('role');
    const namaField = document.getElementById('nama-field');
    const namaInput = document.getElementById('name');

    function toggleNamaField() {
        if (roleSelect.value === 'admin') {
            // Jika daftar sebagai Admin, sembunyikan Nama (mungkin nama diambil dari data staff nanti)
            namaField.style.display = 'none';
            namaInput.required = false;
        } else {
            // Jika User/Siswa, Wajib isi Nama
            namaField.style.display = 'block';
            namaInput.required = true;
        }
    }

    if(roleSelect){
        roleSelect.addEventListener('change', toggleNamaField);
        // Jalankan saat halaman pertama load
        window.addEventListener('DOMContentLoaded', toggleNamaField);
    }
</script>

@endsection