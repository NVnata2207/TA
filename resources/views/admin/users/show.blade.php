@extends('layouts.admin')
@section('page_title', 'Detail Peserta')
@section('content')
<div class="container">
    <h3 class="mb-3">Detail Peserta</h3>
    
    {{-- Biodata Peserta --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Biodata Peserta</div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Kode Pendaftaran</th><td>{{ $user->kode_pendaftaran }}</td></tr>
                <tr><th>Nama</th><td>{{ $user->name }}</td></tr>
                <tr><th>Email</th><td>{{ $user->email }}</td></tr>
                <tr><th>Status</th><td>{{ $user->status_pendaftaran }}</td></tr>
                <tr><th>Hasil</th><td>{{ $user->hasil }}</td></tr>
                <tr><th>Daftar Ulang</th><td>{{ $user->daftar_ulang }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Tabel Berkas Pendukung (SUDAH DINAMIS) --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Berkas Pendukung</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Dokumen</th>
                        <th>Status</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 🟢 INI BAGIAN YANG BARU (DINAMIS DARI DATABASE) 🟢 --}}
                    @foreach($requirements as $req)
                        @php
                            // Cari dokumen user berdasarkan requirement_id
                            $doc = $user->documents->where('requirement_id', $req->id)->first();
                        @endphp

                        <tr>
                            {{-- 1. Nama Dokumen --}}
                            <td>{{ $req->name }}</td>
                            
                            {{-- 2. Status --}}
                            <td>
                                @if($doc)
                                    <span class="badge bg-success">Ada</span>
                                @else
                                    <span class="badge bg-secondary">Belum ada</span>
                                @endif
                            </td>

                            {{-- 3. Download --}}
                            <td>
                                @if($doc)
                                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download / Lihat
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    {{-- 🔴 AKHIR BAGIAN DINAMIS 🔴 --}}
                </tbody>
            </table>
        </div>
    </div>
    
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali ke List Peserta</a>
</div>
@endsection