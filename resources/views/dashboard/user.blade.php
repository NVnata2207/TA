@extends('layouts.admin')

{{-- 1. PERBAIKAN: DEFINISI VARIABEL DI SINI --}}
@php
    // Cek status akhir
    $isLocked = ($user->hasil === 'Di Terima' || $user->hasil === 'Tidak Diterima');
    // Cek status khusus cetak bukti (hanya jika diterima)
    $bisaCetak = ($user->hasil === 'Di Terima');
@endphp

@section('page_title', 'Dashboard')
@section('content')

{{-- Alert Sukses/Error --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- MODAL LULUS --}}
@if($user->hasil === 'Di Terima')
    @php $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first(); @endphp
    <div id="modalLulus" class="modal show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5); padding-right: 17px;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Selamat, Anda Lulus Seleksi</h5>
                    <button type="button" class="close" onclick="document.getElementById('modalLulus').style.display='none'" aria-label="Close" style="color: white; opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda dinyatakan <b>LULUS</b> seleksi PPDB.</p>
                    <p>Silakan melakukan <b>Daftar Ulang</b> pada periode berikut:</p>
                    <ul>
                        <li><b>Mulai Daftar Ulang:</b> {{ $activeYear ? \Carbon\Carbon::parse($activeYear->mulai_daftar_ulang)->format('d M Y') : '-' }}</li>
                        <li><b>Selesai Daftar Ulang:</b> {{ $activeYear ? \Carbon\Carbon::parse($activeYear->selesai_daftar_ulang)->format('d M Y') : '-' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- MODAL TIDAK LULUS --}}
@if($user->hasil === 'Tidak Diterima')
    <div id="modalTidakLulus" class="modal show" tabindex="-1" role="dialog" style="display: block; background:rgba(0,0,0,0.5); padding-right: 17px;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Maaf, Anda Belum Lulus Seleksi</h5>
                    <button type="button" class="close" onclick="document.getElementById('modalTidakLulus').style.display='none'" aria-label="Close" style="color: white; opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Mohon maaf, Anda <b>TIDAK LULUS</b> seleksi PPDB tahun ini.</p>
                    <p>Terima kasih telah berpartisipasi.</p>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- INFO JADWAL --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info p-2">
            <b>E-PPDB Yayasan Insan Madani Mulia Madiun</b>
        </div>
        <div class="alert" style="background:#00bcd4;color:#fff;">
            @if(isset($activeYear) && $activeYear)
                Jadwal PPDB : {{ \Carbon\Carbon::parse($activeYear->mulai_pendaftaran)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activeYear->selesai_pendaftaran)->format('d M Y') }},
                Seleksi/Ujian : {{ \Carbon\Carbon::parse($activeYear->mulai_seleksi)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activeYear->selesai_seleksi)->format('d M Y') }},
                Pengumuman : {{ \Carbon\Carbon::parse($activeYear->tanggal_pengumuman)->format('d M Y') }},
                Daftar Ulang : {{ \Carbon\Carbon::parse($activeYear->mulai_daftar_ulang)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activeYear->selesai_daftar_ulang)->format('d M Y') }}
            @else
                Tidak ada tahun ajaran aktif atau data belum dimuat.
            @endif
        </div>
    </div>
</div>

{{-- NOTIFIKASI --}}
@php
    $notifications = \App\Models\Notification::where('user_id', $user->id)
                    ->where('type', 'user')
                    ->where('read', false)
                    ->latest()
                    ->get();
@endphp

@if($notifications->count())
    <div class="alert alert-warning mt-3">
        <b>Notifikasi:</b>
        <ul class="mb-0">
            @foreach($notifications as $notif)
                <li>{{ $notif->message }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- TOMBOL AKSI UTAMA (Formulir, Berkas, Cetak) --}}
<div class="row mb-3">

    {{-- CARD 1: Isi Formulir --}}
    <div class="col-md-3 col-6 mb-3">
        <div class="card text-white" style="background:#ff0000FF;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-file-alt fa-2x me-2"></i>
                    <div>
                        <div class="fw-bold" style="font-size:1.3rem;">Formulir</div>
                        <div>Biodata</div>
                    </div>
                </div>
                @if($isLocked)
                    <button class="btn btn-light btn-sm w-100 disabled" style="opacity: 0.7; cursor: not-allowed;">
                        <i class="fas fa-lock"></i> Akses Ditutup
                    </button>
                @else
                    <a href="{{ route('user.documents.index') }}" class="btn btn-light btn-sm w-100">Isi Formulir Disini
                        <i class="fa fa-upload"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- CARD 2: FORMULIR --}}
    <div class="col-md-3 col-6 mb-3">
        <div class="card text-white" style="background:#20cfcf;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-pen fa-2x me-2"></i>
                    <div>
                        <div class="fw-bold" style="font-size:1.3rem;">Formulir</div>
                        <div>Pendaftaran</div>
                    </div>
                </div>
                {{-- GANTI JADI INI SAJA (TANPA IF-ELSE) --}}
                <a href="{{ route('user.downloadRegistrationForm') }}" class="btn btn-light btn-sm w-100" target="_blank">
                    Download Formulir PDF <i class="fa fa-download"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- CARD 3: BERKAS --}}
    <div class="col-md-3 col-6 mb-3">
        <div class="card text-white" style="background:#ff9800;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-file-alt fa-2x me-2"></i>
                    <div>
                        <div class="fw-bold" style="font-size:1.3rem;">Berkas</div>
                        <div>Pendukung</div>
                    </div>
                </div>
                @if($isLocked)
                    <button class="btn btn-light btn-sm w-100 disabled" style="opacity: 0.7; cursor: not-allowed;">
                        <i class="fas fa-lock"></i> Akses Ditutup
                    </button>
                @else
                    <a href="{{ route('user.documents.index') }}" class="btn btn-light btn-sm w-100">
                        Upload Berkas <i class="fa fa-upload"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- CARD 3: CETAK BUKTI --}}
    <div class="col-md-3 col-6 mb-3">
        <div class="card text-white" style="background:#0288d1;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-print fa-2x me-2"></i>
                    <div>
                        <div class="fw-bold" style="font-size:1.3rem;">{{ $bisaCetak ? 'Cetak' : 'Belum bisa' }}</div>
                        <div>Bukti Pendaftaran</div>
                    </div>
                </div>
                
                {{-- PERBAIKAN DI SINI: MENGGUNAKAN ROUTE YANG BENAR --}}
                @if($bisaCetak)
                    <a href="{{ route('user.print_acceptance') }}" class="btn btn-light btn-sm w-100">
                        Cetak Bukti <i class="fa fa-print"></i>
                    </a>
                @else
                    <a href="#" class="btn btn-light btn-sm w-100 disabled" style="opacity: 0.7;">
                        Belum tersedia <i class="fa fa-print"></i>
                    </a>
                @endif
                
            </div>
        </div>
    </div>
</div>

{{-- LAYOUT BAWAH (Pengumuman & Info Akun) --}}
<div class="row mb-3">
    {{-- KOLOM KIRI: PENGUMUMAN --}}
    <div class="col-md-8 mb-3">
        <div class="card">
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

    {{-- KOLOM KANAN: INFO NOMOR & KONTAK & UJIAN --}}
    <div class="col-md-4 mb-3">
        {{-- Card Nomor Pendaftaran --}}
        <div class="card mb-3" style="background:#e91e63;color:#fff;">
            <div class="card-body text-center">
                <div class="fw-bold mb-2">Nomer Pendaftaran</div>
                <div style="font-size:1.5rem;letter-spacing:2px;">
                    {{ $user->kode_pendaftaran ?? 'Belum ada' }}
                </div>
            </div>
        </div>

        {{-- Card Kontak --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="fw-bold mb-2"><i class="fas fa-phone-alt"></i> Contact Panitia PPDB</div>
                <div class="mb-1"><i class="fas fa-user"></i> - Panitia PPDB</div>
                <div><i class="fas fa-envelope"></i> - info@ppdb.com</div>
            </div>
        </div>
    </div>
</div>


@endsection