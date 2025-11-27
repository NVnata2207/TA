@extends('layouts.admin')
@section('page_title', 'Cek Berkas Formulir Pendaftaran')
@section('content')
<style>
    .category-label {
        display: inline-block;
        padding: 4px 16px;
        border-radius: 8px 8px 0 0;
        font-weight: bold;
        color: #000000;
        margin-bottom: 0;
        font-size: 1rem;
    }
    .cat-Data\ Registrasi { background: #f59e42; }
    .cat-Data\ Pribadi { background: #22c55e; }
    .cat-Data\ Priodik { background: #06b6d4; }
    .cat-Data\ Ayah { background: #a78bfa; }
    .cat-Data\ Ibu { background: #f472b6; }
    .cat-Data\ Kontak { background: #ef4444; }
    .cat-Data\ Nilai { background: #0ea5e9; }
    .checklist-grid { display: flex; flex-wrap: wrap; gap: 12px; }
    .checklist-item { min-width: 220px; }
</style>
<div class="container">
    <h3 class="mb-3">Pengaturan Formulir Pendaftaran</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="mb-4">
        <h5>Kelola Kategori (Group)</h5>
        <form method="POST" action="{{ url('form_fields/store-category') }}" class="d-inline-block me-3">
    @csrf
            <input type="text" name="category" class="form-control d-inline-block w-auto" placeholder="Tambah Kategori Baru" required>
            <button type="submit" class="btn btn-success btn-sm">Tambah</button> 
        </form>
            <div class="d-inline-block">
            @foreach($categories as $cat)
                <form method="POST" action="{{ url('form_fields/update-category/'.$cat) }}" class="d-inline-block me-2">
                    @csrf
                    <input type="text" name="category" value="{{ $cat }}" class="form-control d-inline-block w-auto" style="width:160px;">
                    <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                </form>
                <form method="POST" action="{{ url('form_fields/destroy-category/'.$cat) }}" class="d-inline-block">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kategori {{ $cat }} beserta seluruh field di dalamnya?')">Hapus</button>
                </form>
            @endforeach
        </div>
    </div>
    <form method="POST" action="{{ route('form_fields.update_all') }}">
    @csrf
        @foreach($categories as $category)
            <div class="card mb-3">
                <div class="card-header p-0 border-0">
                    <span class="category-label cat-{{ str_replace(' ', '\\ ', $category) }}">{{ $category }}</span>
                </div>
                <div class="card-body checklist-grid">
                    @foreach($fieldsByCategory[$category] as $field)
                        @if($field && is_object($field))
                        <div class="form-check checklist-item d-flex align-items-center justify-content-between">
                            <div>
                                <input class="form-check-input" type="checkbox" name="fields[]" value="{{ $field->id }}" id="field_{{ $field->id }}" {{ $field->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="field_{{ $field->id }}">
                                    {{ $field->label }}
                                </label>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
    </form>
    @foreach($categories as $category)
        <div class="card-footer bg-light">
            <form method="POST" action="{{ route('form_fields.storeField') }}" class="row g-2 align-items-center">
                @csrf
                <input type="hidden" name="category" value="{{ $category }}">
                <div class="col-md-3">
                    <input type="text" name="label" class="form-control form-control-sm" placeholder="Label Field" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Name (unik)" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="order" class="form-control form-control-sm" placeholder="Urutan" min="0">
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="active_{{ $category }}" checked>
                        <label class="form-check-label" for="active_{{ $category }}">Aktif</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-sm">Tambah Field</button>
                </div>
            </form>
            <div class="d-flex flex-wrap mt-2">
                @foreach($fieldsByCategory[$category] as $field)
                    @if($field && is_object($field))
                    <form method="POST" action="{{ url('form_fields/'.$field->id.'/delete') }}" onsubmit="return confirm('Hapus field ini?')" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger ms-2 mb-1" title="Hapus"><i class="fa fa-trash"></i> {{ $field->label }}</button>
                    </form>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection 