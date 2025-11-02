@extends('layouts.admin')
@section('page_title', 'Tambah Pengumuman')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Judul</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Isi Pengumuman</label>
                <textarea class="form-control" id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe Pengumuman</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="pembukaan" {{ old('type')=='pembukaan'?'selected':'' }}>Pembukaan Pendaftaran</option>
                    <option value="masa_pendaftaran" {{ old('type')=='masa_pendaftaran'?'selected':'' }}>Masa Pendaftaran</option>
                    <option value="ditutup" {{ old('type')=='ditutup'?'selected':'' }}>Pendaftaran Ditutup</option>
                    <option value="biasa" {{ old('type')=='biasa'?'selected':'' }}>Pengumuman Biasa</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Gambar (opsional)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="show_on_login" name="show_on_login" value="1" {{ old('show_on_login') ? 'checked' : '' }}>
                <label class="form-check-label" for="show_on_login">Tampilkan di pop up login</label>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
        <script>
            ClassicEditor.create(document.querySelector('#content'));
        </script>
        <style>
            .ck-editor__editable[role="textbox"] {
                min-height: 300px;
            }
        </style>
    </div>
</div>
@endsection 