@extends('layouts.admin')
@section('page_title', 'Detail Peserta')
@section('content')
<div class="container">
    <h3 class="mb-3">Detail Peserta</h3>
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
                <!-- Tambahkan field lain sesuai kebutuhan -->
            </table>
        </div>
    </div>
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
                    @foreach($types as $type => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>
                            @if(isset($documents[$type]))
                                <span class="badge bg-success">Sudah diupload</span>
                            @else
                                <span class="badge bg-secondary">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($documents[$type]))
                                <a href="{{ asset('storage/'.$documents[$type]->file_path) }}" target="_blank" class="btn btn-sm btn-primary">Download</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali ke List Peserta</a>
</div>
@endsection 