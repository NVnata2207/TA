@extends('layouts.admin')
@section('page_title', 'Upload Berkas')

@section('content')

{{-- 1. LOGIKA PENGUNCI (LOCK) --}}
@php
    $user = auth()->user();
    // Cek apakah status sudah final
    $isLocked = ($user->hasil === 'Di Terima' || $user->hasil === 'Tidak Diterima');
@endphp

    {{-- Alert Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. PESAN PERINGATAN JIKA TERKUNCI --}}
    @if($isLocked)
        <div class="alert alert-warning shadow-sm border-left-warning">
            <i class="fas fa-lock fa-lg me-2"></i> 
            <b>Akses Ditutup:</b> Masa seleksi telah berakhir (Status: <b>{{ $user->hasil }}</b>). Anda tidak dapat mengubah atau menghapus berkas lagi.
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload Berkas Pendukung</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Dokumen</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 40%;">Upload/Ganti</th>
                            <th style="width: 10%;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $typeKey => $typeName)
                        <tr>
                            <td style="vertical-align: middle;">{{ $typeName }}</td>
                            
                            {{-- Status Upload --}}
                            <td style="vertical-align: middle;">
                                @if(isset($documents[$typeKey]))
                                    <span class="badge bg-success">Sudah diupload</span>
                                    <br>
                                    <small><a href="{{ asset('storage/'.$documents[$typeKey]->file_path) }}" target="_blank">Lihat File</a></small>
                                @else
                                    <span class="badge bg-secondary">Belum ada</span>
                                @endif
                            </td>

                            {{-- Form Upload --}}
                            <td style="vertical-align: middle;">
                                <form action="{{ route('user.documents.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $typeKey }}">
                                    
                                    {{-- INPUT FILE --}}
                                    {{-- Jika Locked, tambahkan atribut 'disabled' --}}
                                    <input type="file" name="file" class="form-control form-control-sm me-2" accept=".pdf" required 
                                        {{ $isLocked ? 'disabled' : '' }}>
                                    
                                    {{-- TOMBOL UPLOAD --}}
                                    <button type="submit" class="btn btn-primary btn-sm" {{ $isLocked ? 'disabled' : '' }}>
                                        @if($isLocked) 
                                            <i class="fas fa-lock"></i> 
                                        @else 
                                            Upload 
                                        @endif
                                    </button>
                                </form>
                            </td>

                            {{-- Tombol Hapus --}}
                            <td style="vertical-align: middle; text-align: center;">
                                @if(isset($documents[$typeKey]))
                                    <form action="{{ route('user.documents.destroy', $typeKey) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus berkas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" {{ $isLocked ? 'disabled' : '' }}>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <small class="text-muted">* Format file wajib <b>PDF</b>. Maksimal ukuran file <b>2MB</b>.</small>
            </div>
        </div>
    </div>
    
    <a href="{{ route('dashboard.user') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>

</div>
@endsection