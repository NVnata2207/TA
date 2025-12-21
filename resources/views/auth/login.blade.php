@extends('layouts.auth')

@section('content')
<style>
    /* Mengatur Background Gambar Penuh */
    body {
        /* Pastikan nama file di dalam tanda kutip SAMA PERSIS dengan file di folder public/img */
        background-image: url("{{ asset('img/image2.jpg') }}");
        
        background-repeat: no-repeat;
        background-position: center center;
        background-attachment: fixed;
        background-size: cover;
        
        /* Warna cadangan jika gambar gagal dimuat */
        background-color: #d4d3d3ff; 
    }

    /* Overlay Gelap Transparan */
    .bg-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Semakin tinggi angkanya (0.8), semakin gelap */
        z-index: 0;
    }

    /* Kartu Login Tengah */
    .login-card {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        width: 100%;
        max-width: 400px;
        position: relative;
        z-index: 1; /* Agar di atas overlay */
    }
</style>

{{-- Overlay Gelap --}}
<div class="bg-overlay"></div>

{{-- Kontainer Utama (Tengah Layar) --}}
<div class="d-flex align-items-center justify-content-center min-vh-100 p-3">
    
    <div class="login-card">
        {{-- Header Form --}}
        <div class="text-center mb-4">
            <p class="fs-2">Selamat Datang Di Portal PPDB</p>
        </div>

        {{-- Alert Error --}}
        @if(session('error'))
            <div class="alert alert-danger py-2 small mb-3">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form Input --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label for="nisn" class="form-label fw-bold small">NISN</label>
                <input type="text" class="form-control" id="nisn" name="nisn" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-bold small">Kata Sandi</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            {{-- Tombol Login --}}
            <button type="submit" class="btn btn-success w-100 py-2 fw-bold mb-3" style="background-color: #2ecc71; border-color: #2ecc71;">
                Login
            </button>

            {{-- Link Buat Akun --}}
            <div class="text-center">
                <a href="{{ route('register') }}" class="text-decoration-none small text-primary">
                    Buat Akun Baru
                </a>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT & MODAL PENGUMUMAN (TETAP ADA) --}}
@php
    $announcements = \App\Models\Announcement::where('show_on_login', true)->latest()->get();
@endphp

@if($announcements->count() > 0)
<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="fas fa-bullhorn"></i> Informasi PPDB</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @foreach($announcements as $idx => $a)
          <div class="mb-4 border-bottom pb-3">
             <h5 class="fw-bold text-center">{{ $a->title }}</h5>
             @if($a->image)
                <div class="text-center mb-3"><img src="{{ asset('storage/'.$a->image) }}" class="img-fluid rounded" style="max-height:300px;"></div>
             @endif
             <div class="text-center">
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $idx }}">Lihat Selengkapnya</button>
             </div>
             <div class="collapse mt-2" id="collapse{{ $idx }}">
                <div class="card card-body border-0">{!! $a->content !!}</div>
             </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
        infoModal.show();
        
        document.querySelectorAll('oembed').forEach(el => {
            const url = el.getAttribute('url');
            if (url && url.includes('youtube.com')) {
                let u = url.replace('watch?v=', 'embed/');
                if(u.includes('&')) u = u.split('&')[0];
                const i = document.createElement('iframe');
                i.src = u; i.width = "100%"; i.height = "315"; i.allowFullscreen = true;
                el.parentNode.replaceChild(i, el);
            }
        });
    });
</script>
@endif

@endsection