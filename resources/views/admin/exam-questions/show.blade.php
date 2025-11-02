@extends('layouts.admin')
@section('page_title', 'Detail Soal Ujian')
@section('content')
<div class="container">
    <a href="{{ route('admin.exam-questions.index') }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Detail Soal Ujian</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Judul Soal</dt>
                <dd class="col-sm-9">{{ $examQuestion->title }}</dd>
                <dt class="col-sm-3">Tahun Ajaran</dt>
                <dd class="col-sm-9">{{ $examQuestion->academicYear->tahun_ajaran }}</dd>
                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">{{ $examQuestion->description }}</dd>
                <dt class="col-sm-3">File Soal</dt>
                <dd class="col-sm-9">
                    <a href="{{ asset('storage/'.$examQuestion->file_path) }}" class="btn btn-sm btn-info" target="_blank">
                        <i class="fas fa-download"></i> Download Soal
                    </a>
                </dd>
            </dl>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Jawaban Peserta</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>File Jawaban</th>
                            <th>Waktu Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examQuestion->answers as $answer)
                            <tr>
                                <td>{{ $answer->user->name }}</td>
                                <td>{{ $answer->user->email }}</td>
                                <td>
                                    <form action="{{ route('admin.exam-answers.update', $answer->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm me-2" style="width:auto">
                                            <option value="pending" {{ $answer->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="reviewed" {{ $answer->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                            <option value="perlu_diubah" {{ $answer->status == 'perlu_diubah' ? 'selected' : '' }}>Perlu Diubah</option>
                                        </select>
                                        <input type="number" name="score" class="form-control form-control-sm me-2" placeholder="Nilai" min="0" max="100" value="{{ $answer->score }}" style="width:70px">
                                        <input type="text" name="admin_notes" class="form-control form-control-sm me-2" placeholder="Komentar" value="{{ $answer->admin_notes }}" style="width:150px">
                                        <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ asset('storage/'.$answer->file_path) }}" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-download"></i> Download Jawaban
                                    </a>
                                </td>
                                <td>{{ $answer->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada jawaban yang diupload peserta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 