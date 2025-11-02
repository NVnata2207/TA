@extends('layouts.admin')
@section('page_title', 'Manajemen Soal Ujian')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Manajemen Soal Ujian</h3>
        <a href="{{ route('admin.exam-questions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Soal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tahun Ajaran</th>
                            <th>Deskripsi</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td>{{ $question->title }}</td>
                                <td>{{ $question->academicYear->tahun_ajaran }}</td>
                                <td>{{ $question->description }}</td>
                                <td>
                                    <a href="{{ asset('storage/'.$question->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.exam-questions.show', $question) }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-search"></i> Detail
                                    </a>
                                    <form action="{{ route('admin.exam-questions.destroy', $question) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus soal ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada soal ujian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 