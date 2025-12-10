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
                    <thead class="bg-light">
                        <tr>
                            <th>Nama Dokumen</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 40%;">Upload/Ganti</th>
                            <th style="width: 10%;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- LOOPING DARI DATABASE (Requirements) --}}
                        @foreach($requirements as $req)
                        
                        {{-- Logika: Cari apakah User sudah upload dokumen untuk requirement ini? --}}
                        @php
                            // Mencari dokumen milik user yang requirement_id-nya cocok dengan ID saat ini
                            // Asumsi: User model punya relasi 'documents'
                            $myDoc = $user->documents->where('requirement_id', $req->id)->first();
                        @endphp

                        <tr>
                            {{-- 1. Nama Dokumen (Dinamis dari Admin) --}}
                            <td style="vertical-align: middle;">
                                <strong>{{ $req->name }}</strong>
                            </td>
                            
                            {{-- 2. Status Upload --}}
                            <td style="vertical-align: middle; text-align: center;">
                                @if($myDoc)
                                    <span class="badge badge-success px-2 py-1">Sudah diupload</span>
                                    <div class="mt-1">
                                        {{-- Link Lihat File --}}
                                        <a href="{{ asset('storage/'.$myDoc->file_path) }}" target="_blank" class="text-xs font-weight-bold text-primary">
                                            <i class="fas fa-eye"></i> Lihat File
                                        </a>
                                    </div>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Belum ada</span>
                                @endif
                            </td>

                            {{-- 3. Form Upload --}}
                            <td style="vertical-align: middle;">
                                <form action="{{ route('user.documents.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                                    @csrf
                                    {{-- Penting: Kirim ID Requirement --}}
                                    <input type="hidden" name="requirement_id" value="{{ $req->id }}">
                                    <input type="hidden" name="document_type" value="{{ $req->name }}">
                                    
                                    <div class="input-group input-group-sm">
                                        <input type="file" name="file" class="form-control" accept=".pdf" required {{ $isLocked ? 'disabled' : '' }}>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" {{ $isLocked ? 'disabled' : '' }}>
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </td>

                            {{-- 4. Tombol Hapus --}}
                            <td style="vertical-align: middle; text-align: center;">
                                @if($myDoc)
                                    <form action="{{ route('user.documents.destroy', $myDoc->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus berkas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-circle btn-sm" title="Hapus" {{ $isLocked ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
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
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Format file wajib <b>PDF</b>. Maksimal ukuran file <b>2MB</b>.
                </small>
            </div>
        </div>
    </div>
    
    <a href="{{ route('dashboard.user') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>

</div>
@endsection