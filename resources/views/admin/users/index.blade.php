@extends('layouts.admin')
@section('page_title', 'Peserta')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
        <span class="fw-bold">List Peserta</span>
        <div>
            <a href="#" class="btn btn-success btn-sm me-1"><i class="fa fa-plus"></i> Tambah</a>
            <button class="btn btn-danger btn-sm me-1"><i class="fa fa-trash"></i> Hapus Data Terpilih</button>
            <button class="btn btn-warning btn-sm text-white me-1"><i class="fa fa-undo"></i> Reset Hasil</button>
            <button class="btn btn-success btn-sm me-1"><i class="fa fa-check"></i> Terima semua</button>
            <button class="btn btn-warning btn-sm text-white me-1"><i class="fa fa-download"></i> Unduh Rekap Nilai</button>
            <button class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Unduh Rekap Peserta Excel</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="p-2 d-flex flex-wrap justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                Show <select class="form-select d-inline-block w-auto">
                    <option>10</option><option>25</option><option>50</option>
                </select> entries
            </div>
            <div>
                <input type="text" class="form-control" placeholder="Search:" style="width:200px;display:inline-block;">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 30px;">
                            <input type="checkbox" class="form-check-input" id="select-all">
                        </th>
                        <th>Kode Pendaftaran</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Hasil</th>
                        <th>Daftar Ulang</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}">
                        </td>
                        <td><span class="badge bg-info">{{ $user->kode_pendaftaran }}</span></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form method="POST" action="{{ route('users.update', $user) }}" class="d-inline user-status-form">
                                @csrf @method('PUT')
                                <select name="status_pendaftaran" class="form-select form-select-sm d-inline w-auto user-status-select">
                                    <option value="Pendaftar Baru" {{ $user->status_pendaftaran == 'Pendaftar Baru' ? 'selected' : '' }}>Pendaftar Baru</option>
                                    <option value="Sudah Diverifikasi" {{ $user->status_pendaftaran == 'Sudah Diverifikasi' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                                    <option value="Berkas Kurang" {{ $user->status_pendaftaran == 'Berkas Kurang' ? 'selected' : '' }}>Berkas Kurang</option>
                                    <option value="Berkas Tidak Sesuai" {{ $user->status_pendaftaran == 'Berkas Tidak Sesuai' ? 'selected' : '' }}>Berkas Tidak Sesuai</option>
                                </select>
                                <input type="text" name="status_comment" class="form-control form-control-sm d-inline w-auto user-status-comment" placeholder="Komentar admin" style="display:none;max-width:200px;" required>
                                <button class="btn btn-primary btn-sm">Ubah</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('users.update', $user) }}" class="d-inline">
                                @csrf @method('PUT')
                                <select name="hasil" class="form-select form-select-sm d-inline w-auto">
                                    <option value="Di Terima" {{ $user->hasil == 'Di Terima' ? 'selected' : '' }}>Di Terima</option>
                                    <option value="Tidak Diterima" {{ $user->hasil == 'Tidak Diterima' ? 'selected' : '' }}>Tidak Diterima</option>
                                </select>
                                <button class="btn btn-success btn-sm">Ubah</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('users.update', $user) }}" class="d-inline">
                                @csrf @method('PUT')
                                <select name="daftar_ulang" class="form-select form-select-sm d-inline w-auto">
                                    <option value="Belum Daftar Ulang" {{ $user->daftar_ulang == 'Belum Daftar Ulang' ? 'selected' : '' }}>Belum Daftar Ulang</option>
                                    <option value="Sudah Daftar Ulang" {{ $user->daftar_ulang == 'Sudah Daftar Ulang' ? 'selected' : '' }}>Sudah Daftar Ulang</option>
                                </select>
                                <button class="btn btn-info btn-sm">Ubah</button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-primary" title="Lihat Detail"><i class="fa fa-search"></i></a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Hapus"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2">
            {{ $users->links() }}
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<script>
document.querySelectorAll('.user-status-form').forEach(function(form) {
    var select = form.querySelector('.user-status-select');
    var comment = form.querySelector('.user-status-comment');
    function toggleComment() {
        if(select.value === 'Berkas Kurang' || select.value === 'Berkas Tidak Sesuai') {
            comment.style.display = 'inline-block';
            comment.required = true;
        } else {
            comment.style.display = 'none';
            comment.required = false;
            comment.value = '';
        }
    }
    select.addEventListener('change', toggleComment);
    toggleComment();
});
</script>
@endsection 