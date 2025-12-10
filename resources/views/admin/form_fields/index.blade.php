@extends('layouts.admin')
@section('page_title', 'Pengaturan Formulir Pendaftaran')

@section('content')
<div class="container pb-5">
    <h3 class="mb-4 fw-bold"></h3>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- BAGIAN 1: KELOLA KATEGORI (Header) --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body bg-light">
            <h5 class="card-title mb-3">1. Kelola Kategori (Group)</h5>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                {{-- Form Tambah Kategori --}}
                <form method="POST" action="{{ route('admin.category.store') }}" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="category" class="form-control" placeholder="Nama Kategori Baru" required style="width: 200px;">
                    <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button> 
                </form>
            </div>
            
            <hr>

            {{-- List Kategori yang Ada --}}
            <div class="d-flex flex-wrap gap-3">
                @foreach($categories as $cat)
                    <div class="btn-group shadow-sm">
                        {{-- Form Edit Kategori --}}
                        <form method="POST" action="{{ route('admin.category.update', $cat) }}" class="d-flex">
                            @csrf @method('PUT')
                            <input type="text" name="category" value="{{ $cat }}" class="form-control form-control-sm border-end-0 rounded-0 rounded-start" style="width: 150px;">
                            <button type="submit" class="btn btn-primary btn-sm rounded-0"><i class="fas fa-save"></i></button>
                        </form>
                        {{-- Form Hapus Kategori --}}
                        <form method="POST" action="{{ route('admin.category.destroy', $cat) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded-0 rounded-end" onclick="return confirm('Hapus kategori ini?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: LIST FIELD PER KATEGORI --}}
    @foreach($categories as $category)
        <div class="card mb-4 shadow-sm border-top-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">{{ $category }}</h5>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="50%">Label (Pertanyaan)</th>
                                {{-- Kolom Name SUDAH DIHAPUS --}}
                                <th width="15%">Urutan</th>
                                <th width="10%" class="text-center">Aktif</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- A. BARIS KHUSUS UNTUK TAMBAH FIELD BARU --}}
                            <tr class="table-success">
                                <form method="POST" action="{{ route('admin.form.storeField') }}">
                                    @csrf
                                    <input type="hidden" name="category" value="{{ $category }}">
                                    
                                    <td class="text-center fw-bold">+</td>
                                    <td>
                                        <input type="text" name="label" class="form-control form-control-sm" placeholder="Input Pertanyaan Baru..." required>
                                        {{-- Input name dihapus karena otomatis dibuat controller --}}
                                    </td>
                                    <td>
                                        <input type="number" name="order" class="form-control form-control-sm" value="0">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="is_active" checked>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                            <i class="fas fa-plus-circle"></i> TAMBAH
                                        </button>
                                    </td>
                                </form>
                            </tr>

                            {{-- B. LOOPING FIELD YANG SUDAH ADA --}}
                            @if(isset($fieldsByCategory[$category]))
                                @foreach($fieldsByCategory[$category] as $index => $field)
                                <tr>
                                    {{-- Form Update --}}
                                    <form method="POST" action="{{ route('admin.form.updateField', $field->id) }}">
                                        @csrf @method('PUT')
                                        
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <input type="text" name="label" value="{{ $field->label }}" class="form-control form-control-sm">
                                        </td>
                                        {{-- Kolom Display Name SUDAH DIHAPUS --}}
                                        <td>
                                            <input type="number" name="order" value="{{ $field->order }}" class="form-control form-control-sm">
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" name="is_active" {{ $field->is_active ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="submit" class="btn btn-primary btn-sm" title="Simpan Perubahan">
                                                    <i class="fas fa-save"></i> Update
                                                </button>
                                    </form> 
                                                {{-- Tombol Hapus --}}
                                                <form method="POST" action="{{ route('admin.form.destroyField', $field->id) }}" onsubmit="return confirm('Yakin hapus field ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection