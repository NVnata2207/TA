@extends('layouts.admin')
@section('page_title', 'Edit Tahun Ajaran')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('academic_years.update', $academic_year) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nama Tahun Ajaran</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $academic_year->name) }}" required placeholder="Contoh: 2024/2025">
            </div>
            <div class="mb-3">
                <label for="kuota" class="form-label">Kuota</label>
                <input type="number" class="form-control" id="kuota" name="kuota" value="{{ old('kuota', $academic_year->kuota) }}">
            </div>
            <div class="mb-3">
                <label for="mulai_pendaftaran" class="form-label">Mulai Pendaftaran</label>
                <input type="date" class="form-control" id="mulai_pendaftaran" name="mulai_pendaftaran" value="{{ old('mulai_pendaftaran', $academic_year->mulai_pendaftaran) }}">
            </div>
            <div class="mb-3">
                <label for="selesai_pendaftaran" class="form-label">Selesai Pendaftaran</label>
                <input type="date" class="form-control" id="selesai_pendaftaran" name="selesai_pendaftaran" value="{{ old('selesai_pendaftaran', $academic_year->selesai_pendaftaran) }}">
            </div>
            <div class="mb-3">
                <label for="mulai_seleksi" class="form-label">Mulai Seleksi</label>
                <input type="date" class="form-control" id="mulai_seleksi" name="mulai_seleksi" value="{{ old('mulai_seleksi', $academic_year->mulai_seleksi) }}">
            </div>
            <div class="mb-3">
                <label for="selesai_seleksi" class="form-label">Selesai Seleksi</label>
                <input type="date" class="form-control" id="selesai_seleksi" name="selesai_seleksi" value="{{ old('selesai_seleksi', $academic_year->selesai_seleksi) }}">
            </div>
            <div class="mb-3">
                <label for="tanggal_pengumuman" class="form-label">Tanggal Pengumuman</label>
                <input type="date" class="form-control" id="tanggal_pengumuman" name="tanggal_pengumuman" value="{{ old('tanggal_pengumuman', $academic_year->tanggal_pengumuman) }}">
            </div>
            <div class="mb-3">
                <label for="mulai_daftar_ulang" class="form-label">Mulai Daftar Ulang</label>
                <input type="date" class="form-control" id="mulai_daftar_ulang" name="mulai_daftar_ulang" value="{{ old('mulai_daftar_ulang', $academic_year->mulai_daftar_ulang) }}">
            </div>
            <div class="mb-3">
                <label for="selesai_daftar_ulang" class="form-label">Selesai Daftar Ulang</label>
                <input type="date" class="form-control" id="selesai_daftar_ulang" name="selesai_daftar_ulang" value="{{ old('selesai_daftar_ulang', $academic_year->selesai_daftar_ulang) }}">
            </div>
            <div class="mb-3">
                <label for="is_active" class="form-label">Status Tahun</label>
                <select class="form-select" id="is_active" name="is_active">
                    <option value="0" {{ old('is_active', $academic_year->is_active) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="1" {{ old('is_active', $academic_year->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ route('academic_years.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection 