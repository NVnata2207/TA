@extends('layouts.auth')
@section('content')
<div class="row min-vh-100 g-0">
    <div class="col-md-7 d-none d-md-flex align-items-center justify-content-center bg-light p-0" style="overflow:hidden;">
        <img src="/img/banner.jpg" alt="PPDB" class="w-100 h-100" style="object-fit:cover; min-height:100vh; min-width:100%;">
    </div>
    <div class="col-md-5 d-flex align-items-center justify-content-center bg-white">
        <div class="w-100" style="max-width: 350px;">
            <div class="text-center mb-4">
                <h3 class="fw-bold">e-PPDB</h3>
                <div class="text-muted small">version 1.5.3</div>
            </div>
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="nisn" class="form-label">NISN</label>
                    <input type="text" class="form-control" id="nisn" name="nisn" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Login</button>
            </form>
            <div class="mt-3 text-center">
                <a href="{{ route('register') }}">Buat Akun Baru</a>
            </div>
        </div>
    </div>
</div>

@php
    $announcements = \App\Models\Announcement::where('show_on_login', true)->latest()->get();
@endphp
<!-- Modal Pengumuman -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="infoModalLabel"><i class="fas fa-bullhorn"></i> Informasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @forelse($announcements as $idx => $a)
          <div class="mb-4 announcement-item">
            {{-- Tanggal di atas --}}
            @if($a->type=='pembukaan' && $a->tanggal_pembukaan)
              <div class="alert alert-info py-1 text-center mb-2">Pendaftaran dibuka: <b>{{ $a->tanggal_pembukaan }}</b></div>
            @endif
            @if($a->type=='masa_pendaftaran' && $a->tanggal_mulai_pendaftaran && $a->tanggal_selesai_pendaftaran)
              <div class="alert alert-info py-1 text-center mb-2">Masa Pendaftaran: <b>{{ $a->tanggal_mulai_pendaftaran }} s.d. {{ $a->tanggal_selesai_pendaftaran }}</b></div>
            @endif
            @if($a->type=='ditutup')
              @if($a->tanggal_pengumuman)
                <div class="alert alert-info py-1 text-center mb-2">Pengumuman: <b>{{ $a->tanggal_pengumuman }}</b></div>
              @endif
              @if($a->tanggal_mulai_daftar_ulang && $a->tanggal_selesai_daftar_ulang)
                <div class="alert alert-info py-1 text-center mb-2">Daftar Ulang: <b>{{ $a->tanggal_mulai_daftar_ulang }} s.d. {{ $a->tanggal_selesai_daftar_ulang }}</b></div>
              @endif
            @endif
            {{-- Judul dan gambar di tengah --}}
            <div class="text-center mb-2">
              <h5 class="fw-bold">{{ $a->title }}</h5>
              @if($a->image)
                <div class="mb-2"><img src="{{ asset('storage/'.$a->image) }}" alt="img" class="img-fluid d-block mx-auto" style="max-width:400px;"></div>
              @endif
            </div>
            {{-- Isi content dengan tombol selengkapnya --}}
            <div class="text-center">
              <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContent{{ $idx }}" aria-expanded="false" aria-controls="collapseContent{{ $idx }}">
                <span class="fw-bold">Selengkapnya</span>
              </button>
            </div>
            <div class="collapse mt-2" id="collapseContent{{ $idx }}">
              <div class="card card-body border-0">{!! $a->content !!}</div>
            </div>
          </div>
        @empty
          <div class="text-center text-muted">Belum ada pengumuman.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
        infoModal.show();
    });
</script>
@endsection 