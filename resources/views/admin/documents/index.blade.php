@extends('layouts.admin')
@section('page_title', 'Master Dokumen Upload')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Pengaturan Dokumen Upload</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Dokumen Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.documents.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Nama Dokumen</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: KTP Orang Tua" required>
                            <small class="text-muted">Nama ini akan muncul di halaman upload siswa.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Dokumen Aktif</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="10%">No</th>
                                    <th>Nama Dokumen</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $index => $doc)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $doc->name }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus dokumen ini? Siswa tidak akan diminta mengupload ini lagi.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada pengaturan dokumen. Silakan tambah di samping kiri.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection