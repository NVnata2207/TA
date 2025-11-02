@extends('layouts.admin')
@section('page_title', 'Dashboard')
@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info p-2">
            <b>e-PPDB SMPN 1 TUMIJAJAR</b><br>
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
<div class="row">
    <div class="col-md-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>1</h3>
                <p>ZONASI</p>
                <span>Kuota 173</span>
            </div>
            <div class="icon"><i class="fas fa-map-marked-alt"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>0</h3>
                <p>PRESTASI</p>
                <span>Kuota 58</span>
            </div>
            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>0</h3>
                <p>AFIRMASI</p>
                <span>Kuota 43</span>
            </div>
            <div class="icon"><i class="fas fa-id-card"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>0</h3>
                <p>PERPINDAHAN</p>
                <span>Kuota 14</span>
            </div>
            <div class="icon"><i class="fas fa-exchange-alt"></i></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 col-6">
        <div class="small-box bg-cyan">
            <div class="inner">
                <h3>1</h3>
                <p>Total Pendaftar</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>0</h3>
                <p>Pendaftar Baru</p>
            </div>
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>1</h3>
                <p>Pendaftar sudah diverifikasi</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3>0</h3>
                <p>Berkas kurang/tidak sesuai</p>
            </div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
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
@php
$notifications = \App\Models\Notification::where('user_id', auth()->id())->where('type', 'admin')->where('read', false)->latest()->get();
@endphp
@if($notifications->count())
    <div class="alert alert-info">
        <b>Notifikasi:</b>
        <ul class="mb-0">
            @foreach($notifications as $notif)
                <li>{{ $notif->message }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection 