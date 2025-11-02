@extends('layouts.admin')
@section('page_title', 'Tahun Pelajaran')
@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Tambah Tahun Pelajaran</span>
                <button type="button" class="btn-close" aria-label="Close" onclick="window.location='{{ route('academic_years.index') }}'"></button>
            </div>
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
                <form method="POST" action="{{ isset($academicYearEdit) && $academicYearEdit ? route('academic_years.update', $academicYearEdit) : route('academic_years.store') }}">
                    @csrf
                    @if(isset($academicYearEdit) && $academicYearEdit)
                        @method('PUT')
                    @endif
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Tahun Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $academicYearEdit->name ?? '') }}" required placeholder="Contoh : 2020">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kuota" class="form-label">Kuota <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="kuota" name="kuota" value="{{ old('kuota', $academicYearEdit->kuota ?? '') }}" required placeholder="Kuota">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="mulai_pendaftaran" class="form-label">Mulai Pendaftaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="mulai_pendaftaran" name="mulai_pendaftaran" value="{{ old('mulai_pendaftaran', $academicYearEdit->mulai_pendaftaran ?? '') }}" required placeholder="Tanggal Mulai Penda">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selesai_pendaftaran" class="form-label">Selesai Pendaftaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="selesai_pendaftaran" name="selesai_pendaftaran" value="{{ old('selesai_pendaftaran', $academicYearEdit->selesai_pendaftaran ?? '') }}" required placeholder="Tanggal Selesai Penc">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="mulai_seleksi" class="form-label">Mulai Seleksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="mulai_seleksi" name="mulai_seleksi" value="{{ old('mulai_seleksi', $academicYearEdit->mulai_seleksi ?? '') }}" required placeholder="Tanggal Mulai Seleks">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selesai_seleksi" class="form-label">Selesai Seleksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="selesai_seleksi" name="selesai_seleksi" value="{{ old('selesai_seleksi', $academicYearEdit->selesai_seleksi ?? '') }}" required placeholder="Tanggal Selesai Selel">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pengumuman" class="form-label">Tanggal Pengumuman <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_pengumuman" name="tanggal_pengumuman" value="{{ old('tanggal_pengumuman', $academicYearEdit->tanggal_pengumuman ?? '') }}" required placeholder="Tanggal Pengumuman">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="mulai_daftar_ulang" class="form-label">Mulai Daftar Ulang <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="mulai_daftar_ulang" name="mulai_daftar_ulang" value="{{ old('mulai_daftar_ulang', $academicYearEdit->mulai_daftar_ulang ?? '') }}" required placeholder="Tanggal Mulai Daftar">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selesai_daftar_ulang" class="form-label">Selesai Daftar Ulang <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="selesai_daftar_ulang" name="selesai_daftar_ulang" value="{{ old('selesai_daftar_ulang', $academicYearEdit->selesai_daftar_ulang ?? '') }}" required placeholder="Tanggal Selesai Daft">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status Tahun <span class="text-danger">*</span></label>
                        <select class="form-select" id="is_active" name="is_active" required>
                            <option value="" disabled {{ old('is_active', $academicYearEdit->is_active ?? '') === '' ? 'selected' : '' }}>Pilih Status Tahun</option>
                            <option value="0" {{ old('is_active', $academicYearEdit->is_active ?? '') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="1" {{ old('is_active', $academicYearEdit->is_active ?? '') == '1' ? 'selected' : '' }}>Aktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">{{ isset($academicYearEdit) && $academicYearEdit ? 'Update' : 'Tambah' }}</button>
                    <a href="{{ route('academic_years.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>List Tahun Pelajaran</span>
                <div>
                    <button class="btn btn-success btn-sm">Excel</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="p-2 d-flex justify-content-between align-items-center">
                    <div>
                        Show <select id="perPage" class="form-select d-inline-block w-auto" style="min-width:60px;">
                            <option>10</option><option>25</option><option>50</option>
                        </select> entries
                    </div>
                    <div>
                        <input type="text" class="form-control" id="searchBox" placeholder="Search:" style="width:200px;display:inline-block;">
                    </div>
                </div>
                <table class="table table-bordered table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun Pelajaran</th>
                            <th>Kuota</th>
                            <th>Status Tahun</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($academic_years as $i => $year)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $year->name }}</td>
                            <td>{{ $year->kuota }}</td>
                            <td>
                                @if($year->is_active)
                                    <span class="badge bg-success">AKTIF</span>
                                @else
                                    <span class="badge bg-secondary">TIDAK AKTIF</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $year->id }}"><i class="fa fa-search"></i></button>
                                <a href="{{ route('academic_years.index', ['edit' => $year->id]) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('academic_years.destroy', $year) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <!-- Modal Detail -->
                        <div class="modal fade" id="detailModal{{ $year->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $year->id }}" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="detailModalLabel{{ $year->id }}">Detail Tahun Pelajaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <ul class="list-group">
                                  <li class="list-group-item"><b>Tahun Pelajaran:</b> {{ $year->name }}</li>
                                  <li class="list-group-item"><b>Kuota:</b> {{ $year->kuota }}</li>
                                  <li class="list-group-item"><b>Mulai Pendaftaran:</b> {{ $year->mulai_pendaftaran }}</li>
                                  <li class="list-group-item"><b>Selesai Pendaftaran:</b> {{ $year->selesai_pendaftaran }}</li>
                                  <li class="list-group-item"><b>Mulai Seleksi:</b> {{ $year->mulai_seleksi }}</li>
                                  <li class="list-group-item"><b>Selesai Seleksi:</b> {{ $year->selesai_seleksi }}</li>
                                  <li class="list-group-item"><b>Tanggal Pengumuman:</b> {{ $year->tanggal_pengumuman }}</li>
                                  <li class="list-group-item"><b>Mulai Daftar Ulang:</b> {{ $year->mulai_daftar_ulang }}</li>
                                  <li class="list-group-item"><b>Selesai Daftar Ulang:</b> {{ $year->selesai_daftar_ulang }}</li>
                                  <li class="list-group-item"><b>Status Tahun:</b> {{ $year->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}</li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-2">
                    {{ $academic_years->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Checkbox select all
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.rowCheckbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            bulkDeleteBtn.disabled = !Array.from(checkboxes).some(cb => cb.checked);
        });
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                bulkDeleteBtn.disabled = !Array.from(checkboxes).some(cb => cb.checked);
            });
        });
        bulkDeleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if(confirm('Yakin hapus data terpilih?')) {
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    });
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
@endsection 