@extends('layouts.admin')
@section('page_title', 'Kelola Pengumuman')
@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header">{{ isset($editAnnouncement) && $editAnnouncement ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}</div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ isset($editAnnouncement) && $editAnnouncement ? route('announcements.update', $editAnnouncement) : route('announcements.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if(isset($editAnnouncement) && $editAnnouncement)
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $editAnnouncement->title ?? '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Isi Pengumuman</label>
                        <textarea class="form-control" id="content" name="content" rows="6">{{ old('content', $editAnnouncement->content ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe Pengumuman</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="pembukaan" {{ old('type', $editAnnouncement->type ?? '')=='pembukaan'?'selected':'' }}>Pembukaan Pendaftaran</option>
                            <option value="masa_pendaftaran" {{ old('type', $editAnnouncement->type ?? '')=='masa_pendaftaran'?'selected':'' }}>Masa Pendaftaran</option>
                            <option value="ditutup" {{ old('type', $editAnnouncement->type ?? '')=='ditutup'?'selected':'' }}>Pendaftaran Ditutup</option>
                            <option value="biasa" {{ old('type', $editAnnouncement->type ?? '')=='biasa'?'selected':'' }}>Pengumuman Biasa</option>
                        </select>
                    </div>
                    <div class="mb-3" id="tanggal_pembukaan_group" style="display:none;">
                        <label for="tanggal_pembukaan" class="form-label">Tanggal Pembukaan</label>
                        <input type="date" class="form-control" id="tanggal_pembukaan" name="tanggal_pembukaan" value="{{ old('tanggal_pembukaan', $editAnnouncement->tanggal_pembukaan ?? '') }}">
                    </div>
                    <div class="mb-3" id="masa_pendaftaran_group" style="display:none;">
                        <label class="form-label">Tanggal Masa Pendaftaran</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="date" class="form-control" id="tanggal_mulai_pendaftaran" name="tanggal_mulai_pendaftaran" value="{{ old('tanggal_mulai_pendaftaran', $editAnnouncement->tanggal_mulai_pendaftaran ?? '') }}" placeholder="Mulai">
                            </div>
                            <div class="col-auto align-self-center">s.d.</div>
                            <div class="col">
                                <input type="date" class="form-control" id="tanggal_selesai_pendaftaran" name="tanggal_selesai_pendaftaran" value="{{ old('tanggal_selesai_pendaftaran', $editAnnouncement->tanggal_selesai_pendaftaran ?? '') }}" placeholder="Selesai">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="ditutup_group" style="display:none;">
                        <label for="tanggal_pengumuman" class="form-label">Tanggal Pengumuman</label>
                        <input type="date" class="form-control mb-2" id="tanggal_pengumuman" name="tanggal_pengumuman" value="{{ old('tanggal_pengumuman', $editAnnouncement->tanggal_pengumuman ?? '') }}">
                        <label class="form-label">Tanggal Daftar Ulang</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="date" class="form-control" id="tanggal_mulai_daftar_ulang" name="tanggal_mulai_daftar_ulang" value="{{ old('tanggal_mulai_daftar_ulang', $editAnnouncement->tanggal_mulai_daftar_ulang ?? '') }}" placeholder="Mulai">
                            </div>
                            <div class="col-auto align-self-center">s.d.</div>
                            <div class="col">
                                <input type="date" class="form-control" id="tanggal_selesai_daftar_ulang" name="tanggal_selesai_daftar_ulang" value="{{ old('tanggal_selesai_daftar_ulang', $editAnnouncement->tanggal_selesai_daftar_ulang ?? '') }}" placeholder="Selesai">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar (opsional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        @if(isset($editAnnouncement) && $editAnnouncement && $editAnnouncement->image)
                            <div class="mt-2"><img src="{{ asset('storage/'.$editAnnouncement->image) }}" alt="img" style="max-width:80px;"></div>
                        @endif
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="show_on_login" name="show_on_login" value="1" {{ old('show_on_login', $editAnnouncement->show_on_login ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_on_login">Tampilkan di pop up login</label>
                    </div>
                    <button type="submit" class="btn btn-{{ isset($editAnnouncement) && $editAnnouncement ? 'warning' : 'success' }}">{{ isset($editAnnouncement) && $editAnnouncement ? 'Update' : 'Simpan' }}</button>
                    @if(isset($editAnnouncement) && $editAnnouncement)
                        <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Batal</a>
                    @endif
                </form>
                <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
                <script>
                    ClassicEditor.create(document.querySelector('#content'));
                    function toggleTanggalFields() {
                        var type = document.getElementById('type').value;
                        document.getElementById('tanggal_pembukaan_group').style.display = (type === 'pembukaan') ? '' : 'none';
                        document.getElementById('masa_pendaftaran_group').style.display = (type === 'masa_pendaftaran') ? '' : 'none';
                        document.getElementById('ditutup_group').style.display = (type === 'ditutup') ? '' : 'none';
                    }
                    document.getElementById('type').addEventListener('change', toggleTanggalFields);
                    window.addEventListener('DOMContentLoaded', toggleTanggalFields);
                </script>
                <style>
                    .ck-editor__editable[role="textbox"] { min-height: 300px; }
                </style>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-header">Daftar Pengumuman</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th>Tampil di Login</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($announcements as $a)
                        <tr>
                            <td>{{ $a->title }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $a->type)) }}</td>
                            <td>{!! $a->show_on_login ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' !!}</td>
                            <td>@if($a->image)<img src="{{ asset('storage/'.$a->image) }}" alt="img" style="max-width:60px;">@endif</td>
                            <td>
                                <a href="{{ route('announcements.edit', $a) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('announcements.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                                <form action="{{ route('announcements.toggleShowOnLogin', $a) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-{{ $a->show_on_login ? 'secondary' : 'success' }} btn-sm" title="Tampilkan/Sembunyikan di Login">
                                        {{ $a->show_on_login ? 'Sembunyikan di Login' : 'Tampilkan di Login' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-2">{{ $announcements->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection 