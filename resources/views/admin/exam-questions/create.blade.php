@extends('layouts.admin')
@section('page_title', 'Tambah Soal Ujian')
@section('content')
@php
    $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
@endphp
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Tambah Soal Ujian</h4>
                </div>
                <div class="card-body">
                    @if(!$activeYear)
                        <div class="alert alert-danger">Tidak ada tahun ajaran aktif. Silakan atur tahun ajaran aktif terlebih dahulu.</div>
                    @else
                    <form action="{{ route('admin.exam-questions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran Aktif</label>
                            <input type="text" class="form-control" value="{{ $activeYear->tahun_ajaran }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Soal</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">File Soal (PDF)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept="application/pdf" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.exam-questions.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 