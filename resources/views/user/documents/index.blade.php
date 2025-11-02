@extends('layouts.admin')
@section('page_title', 'Upload Berkas Pendukung')
@section('content')
@php
$user = auth()->user();
$activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
@endphp
@if($user->status_pendaftaran === 'Sudah Diverifikasi')
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.2);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Upload Berkas Ditutup</h5>
                </div>
                <div class="modal-body">
                    <p>Berkas Anda sudah diverifikasi. Silakan menunggu jadwal Seleksi/Ujian:</p>
                    <ul>
                        <li><b>Mulai Seleksi/Ujian:</b> {{ $activeYear ? \Carbon\Carbon::parse($activeYear->mulai_seleksi)->format('d M Y') : '-' }}</li>
                        <li><b>Selesai Seleksi/Ujian:</b> {{ $activeYear ? \Carbon\Carbon::parse($activeYear->selesai_seleksi)->format('d M Y') : '-' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@else
<div class="container">
    <h3 class="mb-3">Upload Berkas Pendukung</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Nama Dokumen</th>
                <th>Status</th>
                <th>Upload/Ganti</th>
                <th>Hapus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $type => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>
                    @if(isset($documents[$type]))
                        <a href="{{ asset('storage/'.$documents[$type]->file_path) }}" target="_blank" class="badge bg-success">Sudah diupload</a>
                    @else
                        <span class="badge bg-secondary">Belum ada</span>
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('user.documents.store') }}" enctype="multipart/form-data" style="display:inline-block">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="file" name="file" accept="application/pdf" required style="display:inline-block;width:180px">
                        <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                    </form>
                </td>
                <td>
                    @if(isset($documents[$type]))
                        <form method="POST" action="{{ route('user.documents.destroy', $type) }}" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus dokumen ini?')">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection 