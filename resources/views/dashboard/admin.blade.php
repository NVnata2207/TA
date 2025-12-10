@extends('layouts.admin')
@section('page_title', 'Dashboard')
@section('content')

{{-- 1. INFORMASI JADWAL --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info p-2">
            <b>E-PPDB Yayasan Insan Madani Mulia Madiun</b><br>
            @if($activeYear)
                Jadwal PPDB : {{ \Carbon\Carbon::parse($activeYear->mulai_pendaftaran)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activeYear->selesai_pendaftaran)->format('d M Y') }},
                Seleksi/Ujian : {{ \Carbon\Carbon::parse($activeYear->mulai_seleksi)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activeYear->selesai_seleksi)->format('d M Y') }},
                Pengumuman : {{ \Carbon\Carbon::parse($activeYear->tanggal_pengumuman)->format('d M Y') }},
                Daftar Ulang : {{ \Carbon\Carbon::parse($activeYear->mulai_daftar_ulang)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activeYear->selesai_daftar_ulang)->format('d M Y') }}
            @else
                Tidak ada tahun ajaran aktif.
            @endif
        </div>
    </div>
</div>
{{-- 2. STATISTIK STATUS PENDAFTAR --}}
<div class="row">
    {{-- TOTAL PENDAFTAR --}}
    <div class="col-md-3 col-6">
        <div class="small-box bg-cyan">
            <div class="inner">
                <h3>{{ $totalPendaftar ?? 0 }}</h3> {{-- <-- SUDAH DIGANTI --}}
                <p>Total Pendaftar</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    {{-- PENDAFTAR BARU --}}
    <div class="col-md-3 col-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>{{ $pendaftarBaru ?? 0 }}</h3> {{-- <-- SUDAH DIGANTI --}}
                <p>Pendaftar Baru</p>
            </div>
            <div class="icon"><i class="fas fa-user-plus"></i></div>
        </div>
    </div>
    {{-- SUDAH DIVERIFIKASI --}}
    <div class="col-md-3 col-6">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>{{ $sudahVerifikasi ?? 0 }}</h3> {{-- <-- SUDAH DIGANTI --}}
                <p>Pendaftar sudah diverifikasi</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
        </div>
    </div>
    {{-- BERKAS KURANG --}}
    <div class="col-md-3 col-6">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3>{{ $berkasKurang ?? 0 }}</h3> {{-- <-- SUDAH DIGANTI --}}
                <p>Berkas kurang/tidak sesuai</p>
            </div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
        </div>
    </div>
</div>

{{-- 3. PENGUMUMAN --}}
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-white border-bottom d-flex align-items-center">
                <i class="fas fa-bullhorn fa-lg me-2"></i>
                <span class="fw-bold">Pengumuman</span>
            </div>
            <div class="card-body bg-light">
                @php $announcements = \App\Models\Announcement::latest()->get(); @endphp
                @forelse($announcements as $a)
                    <div class="d-flex align-items-start mb-4 p-3 bg-white rounded shadow-sm">
                        <div class="me-3 text-center">
                            <span class="badge bg-success mb-2" style="font-size:1rem;">{{ $a->created_at->format('d M Y') }}</span>
                            <div><i class="fas fa-envelope fa-2x text-info"></i></div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold" style="font-size:1.1rem;">{{ $a->title }}</span>
                                <span class="text-muted small"><i class="far fa-clock"></i> {{ $a->created_at->format('H:i:s') }}</span>
                            </div>
                            <div>{!! $a->content !!}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">Belum ada pengumuman.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- 4. NOTIFIKASI ADMIN (DIPERBAIKI) --}}
@php
    $notifications = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('type', 'admin')
                    ->where('read', false)
                    ->latest()->get();
@endphp

@if($notifications->count())
    <div class="alert alert-info shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <b><i class="fas fa-bell"></i> Notifikasi Baru ({{ $notifications->count() }})</b>
        </div>
        
        <div class="list-group">
            @foreach($notifications as $notif)
                {{-- JADIKAN LINK (<a>) AGAR BISA DIKLIK --}}
                <a href="{{ $notif->link ?? '#' }}" class="list-group-item list-group-item-action flex-column align-items-start p-2">
                    <div class="d-flex w-100 justify-content-between">
                        {{-- Nama Pengirim (Opsional, ambil dari pesan) --}}
                        <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        <small><i class="fas fa-external-link-alt"></i></small>
                    </div>
                    
                    {{-- CSS PENTING: word-wrap & white-space AGAR TIDAK KELUAR KOTAK --}}
                    <p class="mb-1 text-dark" style="font-size: 0.9rem; white-space: normal; word-wrap: break-word;">
                        {!! $notif->message !!}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
@endif

@endsection