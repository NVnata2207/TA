@extends('layouts.admin')
@section('page_title', 'Halaman Ujian')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Halaman Ujian</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="alert alert-info">
                        <h5 class="alert-heading">Informasi Ujian</h5>
                        <p class="mb-0">
                            Periode Ujian: {{ \Carbon\Carbon::parse($activeYear->mulai_seleksi)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($activeYear->selesai_seleksi)->format('d M Y') }}
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Judul Soal</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $question)
                                    <tr>
                                        <td>{{ $question->title }}</td>
                                        <td>{{ $question->description }}</td>
                                        <td>
                                            @if(isset($answers[$question->id]))
                                                @if($answers[$question->id]->status == 'pending')
                                                    <span class="badge bg-warning">Menunggu Penilaian</span>
                                                @elseif($answers[$question->id]->status == 'reviewed')
                                                    <span class="badge bg-success">Sudah Dinilai</span>
                                                @elseif($answers[$question->id]->status == 'perlu_diubah')
                                                    <span class="badge bg-danger">Perlu Diubah</span>
                                                @endif
                                                @if($answers[$question->id]->admin_notes)
                                                    <div class="text-danger small mt-1"><b>Komentar Admin:</b> {{ $answers[$question->id]->admin_notes }}</div>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Belum Dikerjakan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('user.exam.download', $question) }}" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-download"></i> Download Soal
                                                </a>
                                                @if(isset($answers[$question->id]))
                                                    <a href="{{ asset('storage/'.$answers[$question->id]->file_path) }}" class="btn btn-sm btn-success" target="_blank">
                                                        <i class="fas fa-eye"></i> Lihat Jawaban
                                                    </a>
                                                @endif
                                            </div>
                                            @if(!isset($answers[$question->id]) || (isset($answers[$question->id]) && $answers[$question->id]->status == 'perlu_diubah'))
                                                <form action="{{ route('user.exam.submit', $question) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                                    @csrf
                                                    <div class="input-group">
                                                        <input type="file" class="form-control form-control-sm" name="answer_file" accept="application/pdf" required>
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-upload"></i> Upload Jawaban
                                                        </button>
                                                    </div>
                                                </form>
                                            @elseif(isset($answers[$question->id]) && $answers[$question->id]->status != 'perlu_diubah')
                                                <div class="text-muted small mt-2">Upload ulang hanya bisa jika status <b>Perlu Diubah</b> oleh admin.</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada soal ujian</td>
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