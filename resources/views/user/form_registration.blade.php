@extends('layouts.admin')
@section('page_title', 'Formulir Pendaftaran')

@section('content')
<div class="container-fluid">
    
    {{-- 1. NOTIFIKASI SUKSES (ALERT BAR) --}}
    @if(session('success'))
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-left: 5px solid #155724; background-color: #d4edda; color: #155724;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x mr-3"></i>
                    <div>
                        <strong>Berhasil Disimpan!</strong> {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 2. NOTIFIKASI ERROR (JIKA ADA) --}}
    @if($errors->any())
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 5px solid #dc3545;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                    <div>
                        <strong>Gagal!</strong> Mohon periksa kembali inputan Anda.
                        <ul class="mb-0 mt-1 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-10">
            
            {{-- KARTU UTAMA --}}
            <div class="card shadow-lg border-0 rounded-lg">
                
                {{-- HEADER --}}
                <div class="card-header bg-gradient-success text-white py-3 text-center">
                    <h4 class="card-title font-weight-bold mb-2">
                        <i class="fas fa-user-edit mr-2"></i> Lengkapi / Edit Data Diri
                    </h4>
                    <p class="mb-0 small text-white-50">
                        Data yang tersimpan akan otomatis tampil di sini. Silakan ubah jika ada kesalahan.
                    </p>
                </div>
                
                <form action="{{ route('user.update_profile') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-4">
                        
                        {{-- ==================================================== --}}
                        {{-- BAGIAN 1: IDENTITAS DATA SISWA --}}
                        {{-- ==================================================== --}}
                        <div class="d-flex align-items-center mb-3">
                            <span class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center mr-2" style="width: 30px; height: 30px; font-weight: bold;">1</span>
                            <h6 class="text-success font-weight-bold mb-0">IDENTITAS DATA SISWA</h6>
                        </div>
                        
                        <div class="row">
                            {{-- NISN --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">NISN</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-hashtag text-muted"></i></span>
                                    </div>
                                    {{-- Value: Mengambil data lama (old) atau data dari database ($user->studentDetail->nisn) --}}
                                    <input type="number" name="nisn" class="form-control border-left-0" value="{{ old('nisn', $user->studentDetail->nisn ?? '') }}" placeholder="NISN" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>

                            {{-- NAMA LENGKAP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Nama Lengkap</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-user text-muted"></i></span>
                                    </div>
                                    <input type="text" name="name" class="form-control border-left-0" value="{{ old('name', $user->name) }}" placeholder="Nama Sesuai Ijazah" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>

                            {{-- NIK --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">NIK</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-id-badge text-muted"></i></span>
                                    </div>
                                    <input type="number" name="nik" class="form-control border-left-0" value="{{ old('nik', $user->studentDetail->nik ?? '') }}" placeholder="NIK" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>

                            {{-- JENJANG SEKOLAH --}}
                            <div class="col-md-6 mb-3"> 
                                <label class="form-label text-secondary small font-weight-bold">Jenjang Sekolah</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-school text-muted"></i></span>
                                    </div>
                                    <select class="form-control custom-select border-left-0" name="jenjang" style="border: 2px solid #6c757d; border-left: none;">
                                        <option value="">-- Pilih --</option>
                                        <option value="SD" {{ (old('jenjang', $user->studentDetail->jenjang ?? '') == 'SD') ? 'selected' : '' }}>SD / MI</option>
                                        <option value="SMP" {{ (old('jenjang', $user->studentDetail->jenjang ?? '') == 'SMP') ? 'selected' : '' }}>SMP / MTs</option>
                                    </select>
                                </div>
                            </div>

                            {{-- ASAL SEKOLAH --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Asal Sekolah</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-graduation-cap text-muted"></i></span>
                                    </div>
                                    <input type="text" name="asal_sekolah" class="form-control border-left-0" value="{{ old('asal_sekolah', $user->studentDetail->asal_sekolah ?? '') }}" placeholder="Contoh: SDN 1 Jakarta" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- TEMPAT LAHIR --}}
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Tempat Lahir</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;">
                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="tempat_lahir" class="form-control border-left-0" value="{{ old('tempat_lahir', $user->studentDetail->tempat_lahir ?? '') }}" placeholder="Kota Lahir" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>

                            {{-- TANGGAL LAHIR --}}
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold small text-secondary">Tanggal Lahir</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        {{-- Style: Border tebal, kanan dimatikan --}}
                                        <span class="input-group-text bg-white" style="border: 2px solid #6c757d; border-right: none;">
                                            <i class="fas fa-calendar-alt text-muted"></i>
                                        </span>
                                    </div>
                                    {{-- Style: Border tebal, kiri dimatikan --}}
                                    <input type="date" name="tanggal_lahir" class="form-control" 
                                           value="{{ old('tanggal_lahir', $user->studentDetail->tanggal_lahir ?? '') }}" 
                                           max="{{ date('Y-m-d') }}"
                                           style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>

                            {{-- JENIS KELAMIN --}}
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Jenis Kelamin</label>
                                <select class="form-control custom-select" name="gender" style="border: 2px solid #6c757d;">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ (old('gender', $user->studentDetail->gender ?? '') == 'L') ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ (old('gender', $user->studentDetail->gender ?? '') == 'P') ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            {{-- AGAMA --}}
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Agama</label>
                                <select class="form-control custom-select" name="agama" style="border: 2px solid #6c757d;">
                                    <option value="Islam" {{ (old('agama', $user->studentDetail->agama ?? '') == 'Islam') ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen" {{ (old('agama', $user->studentDetail->agama ?? '') == 'Kristen') ? 'selected' : '' }}>Kristen</option>
                                    <option value="Katolik" {{ (old('agama', $user->studentDetail->agama ?? '') == 'Katolik') ? 'selected' : '' }}>Katolik</option>
                                    <option value="Hindu" {{ (old('agama', $user->studentDetail->agama ?? '') == 'Hindu') ? 'selected' : '' }}>Hindu</option>
                                    <option value="Buddha" {{ (old('agama', $user->studentDetail->agama ?? '') == 'Buddha') ? 'selected' : '' }}>Buddha</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            {{-- GOLONGAN DARAH --}}
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Gol. Darah</label>
                                <select class="form-control custom-select" name="golongan_darah" style="border: 2px solid #6c757d;">
                                    <option value="">-</option>
                                    <option value="A" {{ (old('golongan_darah', $user->studentDetail->golongan_darah ?? '') == 'A') ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ (old('golongan_darah', $user->studentDetail->golongan_darah ?? '') == 'B') ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ (old('golongan_darah', $user->studentDetail->golongan_darah ?? '') == 'AB') ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ (old('golongan_darah', $user->studentDetail->golongan_darah ?? '') == 'O') ? 'selected' : '' }}>O</option>
                                </select>
                            </div>

                            {{-- KEWARGANEGARAAN --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Kewarganegaraan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-flag text-muted"></i></span>
                                    </div>
                                    <input type="text" name="kewarganegaraan" class="form-control border-left-0" value="{{ old('kewarganegaraan', $user->studentDetail->kewarganegaraan ?? 'WNI') }}" placeholder="WNI" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>

                            {{-- TEMPAT TINGGAL --}}
                            <div class="col-md-5 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Tempat Tinggal</label>
                                <select class="form-control custom-select" name="tempat_tinggal" style="border: 2px solid #6c757d;">
                                    <option value="">-- Pilih --</option>
                                    <option value="Bersama Orang Tua" {{ (old('tempat_tinggal', $user->studentDetail->tempat_tinggal ?? '') == 'Bersama Orang Tua') ? 'selected' : '' }}>Bersama Orang Tua</option>
                                    <option value="Wali" {{ (old('tempat_tinggal', $user->studentDetail->tempat_tinggal ?? '') == 'Wali') ? 'selected' : '' }}>Wali</option>
                                    <option value="Kos" {{ (old('tempat_tinggal', $user->studentDetail->tempat_tinggal ?? '') == 'Kos') ? 'selected' : '' }}>Kos</option>
                                    <option value="Asrama" {{ (old('tempat_tinggal', $user->studentDetail->tempat_tinggal ?? '') == 'Asrama') ? 'selected' : '' }}>Asrama</option>
                                </select>
                            </div>
                        </div>

                        {{-- ALAMAT --}}
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Alamat Lengkap</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-home text-muted"></i></span>
                                    </div>
                                    <textarea name="alamat" class="form-control border-left-0" rows="2" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan" style="border: 2px solid #6c757d; border-left: none;">{{ old('alamat', $user->studentDetail->alamat ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 dashed-border">

                        {{-- ==================================================== --}}
                        {{-- BAGIAN 2: KONTAK & KELUARGA --}}
                        {{-- ==================================================== --}}
                        <div class="d-flex align-items-center mb-3">
                            <span class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center mr-2" style="width: 30px; height: 30px; font-weight: bold;">2</span>
                            <h6 class="text-success font-weight-bold mb-0">DATA ORANG TUA / WALI</h6>
                        </div>

                        {{-- DATA AYAH --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Nama Ayah Kandung</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-male text-muted"></i></span>
                                    </div>
                                    <input type="text" name="nama_ayah" class="form-control border-left-0" value="{{ old('nama_ayah', $user->studentDetail->nama_ayah ?? '') }}" placeholder="Nama Ayah" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Pendidikan Ayah</label>
                                <select name="pendidikan_ayah" class="form-control custom-select" style="border: 2px solid #6c757d;">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD" {{ (old('pendidikan_ayah', $user->studentDetail->pendidikan_ayah ?? '') == 'SD') ? 'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ (old('pendidikan_ayah', $user->studentDetail->pendidikan_ayah ?? '') == 'SMP') ? 'selected' : '' }}>SMP</option>
                                    <option value="SMA" {{ (old('pendidikan_ayah', $user->studentDetail->pendidikan_ayah ?? '') == 'SMA') ? 'selected' : '' }}>SMA/SMK</option>
                                    <option value="S1" {{ (old('pendidikan_ayah', $user->studentDetail->pendidikan_ayah ?? '') == 'S1') ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ (old('pendidikan_ayah', $user->studentDetail->pendidikan_ayah ?? '') == 'S2') ? 'selected' : '' }}>S2</option>
                                    <option value="Lainnya" {{ (old('pendidikan_ayah', $user->studentDetail->pendidikan_ayah ?? '') == 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah', $user->studentDetail->pekerjaan_ayah ?? '') }}" placeholder="Pekerjaan Ayah" style="border: 2px solid #6c757d;">
                            </div>
                        </div>

                        {{-- DATA IBU --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Nama Ibu Kandung</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-female text-muted"></i></span>
                                    </div>
                                    <input type="text" name="nama_ibu" class="form-control border-left-0" value="{{ old('nama_ibu', $user->studentDetail->nama_ibu ?? '') }}" placeholder="Nama Ibu" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Pendidikan Ibu</label>
                                <select name="pendidikan_ibu" class="form-control custom-select" style="border: 2px solid #6c757d;">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD" {{ (old('pendidikan_ibu', $user->studentDetail->pendidikan_ibu ?? '') == 'SD') ? 'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ (old('pendidikan_ibu', $user->studentDetail->pendidikan_ibu ?? '') == 'SMP') ? 'selected' : '' }}>SMP</option>
                                    <option value="SMA" {{ (old('pendidikan_ibu', $user->studentDetail->pendidikan_ibu ?? '') == 'SMA') ? 'selected' : '' }}>SMA/SMK</option>
                                    <option value="S1" {{ (old('pendidikan_ibu', $user->studentDetail->pendidikan_ibu ?? '') == 'S1') ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ (old('pendidikan_ibu', $user->studentDetail->pendidikan_ibu ?? '') == 'S2') ? 'selected' : '' }}>S2</option>
                                    <option value="Lainnya" {{ (old('pendidikan_ibu', $user->studentDetail->pendidikan_ibu ?? '') == 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $user->studentDetail->pekerjaan_ibu ?? '') }}" placeholder="Pekerjaan Ibu" style="border: 2px solid #6c757d;">
                            </div>
                        </div>

                        {{-- PENGHASILAN & KONTAK --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Penghasilan Orang Tua / Wali</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;">Rp</span>
                                    </div>
                                    <select name="penghasilan_ortu" class="form-control custom-select border-left-0" style="border: 2px solid #6c757d; border-left: none;">
                                        <option value="">-- Pilih Kisaran --</option>
                                        <option value="< 1 Juta" {{ (old('penghasilan_ortu', $user->studentDetail->penghasilan_ortu ?? '') == '< 1 Juta') ? 'selected' : '' }}>Kurang dari 1 Juta</option>
                                        <option value="1 - 3 Juta" {{ (old('penghasilan_ortu', $user->studentDetail->penghasilan_ortu ?? '') == '1 - 3 Juta') ? 'selected' : '' }}>1 - 3 Juta</option>
                                        <option value="3 - 5 Juta" {{ (old('penghasilan_ortu', $user->studentDetail->penghasilan_ortu ?? '') == '3 - 5 Juta') ? 'selected' : '' }}>3 - 5 Juta</option>
                                        <option value="> 5 Juta" {{ (old('penghasilan_ortu', $user->studentDetail->penghasilan_ortu ?? '') == '> 5 Juta') ? 'selected' : '' }}>Lebih dari 5 Juta</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Nomor WhatsApp / HP Aktif</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fab fa-whatsapp text-muted"></i></span>
                                    </div>
                                    <input type="text" name="no_hp" class="form-control border-left-0" value="{{ old('no_hp', $user->studentDetail->no_hp ?? '') }}" placeholder="08xxxxxxxxxx" style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-secondary small font-weight-bold">Email Akun (Tidak dapat diubah)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border: 2px solid #6c757d; border-right: none;"><i class="fas fa-envelope text-muted"></i></span>
                                    </div>
                                    <input type="email" name="email" class="form-control border-left-0 bg-light" value="{{ $user->email }}" readonly style="border: 2px solid #6c757d; border-left: none;">
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    {{-- FOOTER TOMBOL --}}
                    <div class="card-footer bg-light text-right py-3">
                        <button type="submit" class="btn btn-success px-5 font-weight-bold shadow-sm rounded-pill">
                            <i class="fas fa-save mr-2"></i> SIMPAN PERUBAHAN
                        </button>
                    </div>

                </form>
            </div>
            
            <div class="text-center mt-3 text-muted small">
                <i class="fas fa-lock mr-1"></i> Data Anda tersimpan aman dalam sistem E-PPDB.
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling agar input group teks lebih rapi dengan border tebal */
    .input-group-text {
        background-color: #f8f9fa;
    }
    .dashed-border {
        border-top: 2px dashed #e9ecef;
    }
    .card {
        border-radius: 15px;
        overflow: hidden; 
    }
    
    /* Efek Focus Hijau saat diklik */
    .form-control:focus, .custom-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        border-color: #28a745 !important;
    }
    /* Pastikan border tetap menyatu saat fokus */
    .form-control:focus + .input-group-prepend .input-group-text, 
    .form-control:focus {
        border-left: 1px solid #28a745 !important; 
    }
</style>
@endpush