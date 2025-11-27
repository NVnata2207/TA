@extends('layouts.admin')

@section('page_title', 'Halaman Ujian')

@section('content')
<div class="container">
    {{-- Header & Informasi Waktu --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body p-4">
                    <h3 class="fw-bold"><i class="fas fa-file-signature me-2"></i> Ujian Seleksi PPDB</h3>
                    <p class="mb-0">
                        Silakan unduh soal yang tersedia, kerjakan, lalu scan/foto jawaban Anda menjadi format <b>PDF</b> dan unggah kembali di sini.
                    </p>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <div class="d-md-flex justify-content-between align-items-center">
                        <div>
                            <i class="far fa-calendar-alt"></i> <b>Jadwal Ujian:</b> <br>
                            {{ \Carbon\Carbon::parse($activeYear->mulai_seleksi)->translatedFormat('d F Y') }} s.d. 
                            {{ \Carbon\Carbon::parse($activeYear->selesai_seleksi)->translatedFormat('d F Y') }}
                        </div>
                        <div class="mt-3 mt-md-0 text-md-end">
                            <small>Waktu Server:</small><br>
                            <span class="fw-bold" style="font-size: 1.2rem;">{{ now()->translatedFormat('d F Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Daftar Soal --}}
    <div class="row">
        @forelse($questions as $question)
            @php
                // --- 1. TAMBAHAN LOGIKA PENGAMAN (LOCK) ---
                $user = auth()->user();
                // Cek apakah user sudah dapat hasil akhir
                $isLocked = ($user->hasil === 'Di Terima' || $user->hasil === 'Tidak Diterima');

                // Cek jawaban user
                $myAnswer = $answers[$question->id] ?? null;
                
                // Status Badge Logic
                $statusBadge = '<span class="badge bg-secondary">Belum Dikerjakan</span>';
                if($myAnswer) {
                    if($myAnswer->status == 'pending') {
                        $statusBadge = '<span class="badge bg-warning text-dark">Menunggu Review</span>';
                    } elseif($myAnswer->status == 'reviewed') {
                        $statusBadge = '<span class="badge bg-success">Sudah Dinilai (Skor: '.$myAnswer->score.')</span>';
                    } elseif($myAnswer->status == 'perlu_diubah') {
                        $statusBadge = '<span class="badge bg-danger">Perlu Revisi</span>';
                    }
                }
                
                // Cek Waktu Ujian
                $now = now();
                $start = \Carbon\Carbon::parse($activeYear->mulai_seleksi);
                $end = \Carbon\Carbon::parse($activeYear->selesai_seleksi)->endOfDay();
                $isTimeOpen = $now->between($start, $end);
                
                // --- 2. UPDATE LOGIKA BOLEH UPLOAD ---
                // Tambahkan syarat: !$isLocked (User TIDAK boleh terkunci)
                $canUpload = !$isLocked && ($isTimeOpen || ($myAnswer && $myAnswer->status == 'perlu_diubah')) && (!($myAnswer && $myAnswer->status == 'reviewed'));
            @endphp

            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                        <div class="d-flex justify-content-between">
                            <h5 class="fw-bold text-primary mb-0">{{ $question->title }}</h5>
                            <div>{!! $statusBadge !!}</div>
                        </div>
                    </div>
                    <div class="card-body px-3">
                        <p class="text-muted small mb-3">{{ $question->description ?? 'Tidak ada deskripsi tambahan.' }}</p>
                        
                        {{-- Tombol Download Soal --}}
                        <div class="mb-3">
                            <label class="small fw-bold text-secondary">File Soal:</label> <br>
                            <a href="{{ route('user.exam.download', $question->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-download me-1"></i> Download Soal (PDF)
                            </a>
                        </div>

                        {{-- Area Upload Jawaban --}}
                        <div class="p-3 bg-light rounded border">
                            <label class="small fw-bold text-secondary mb-2">Upload Jawaban Anda:</label>
                            
                            @if($myAnswer)
                                <div class="mb-2 small">
                                    <i class="fas fa-file-pdf text-danger"></i> 
                                    File terupload: <a href="{{ asset('storage/'.$myAnswer->file_path) }}" target="_blank">Lihat Jawaban Saya</a>
                                    <div class="text-muted" style="font-size: 0.8rem;">
                                        Diupload: {{ $myAnswer->updated_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                                
                                @if($myAnswer->admin_notes)
                                    <div class="alert alert-warning py-2 px-2 small mb-2">
                                        <b>Catatan Admin:</b> {{ $myAnswer->admin_notes }}
                                    </div>
                                @endif
                            @endif

                            {{-- Form Upload --}}
                            @if($canUpload)
                                <form action="{{ route('user.exam.submit', $question->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <input type="file" name="answer_file" class="form-control" accept="application/pdf" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-upload"></i> {{ $myAnswer ? 'Ganti File' : 'Kirim' }}
                                        </button>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">*Format PDF, Max 10MB</small>
                                </form>
                            @else
                                {{-- --- 3. PESAN JIKA TERKUNCI --- --}}
                                <div class="alert alert-secondary py-1 px-2 small mb-0 text-center">
                                    <i class="fas fa-lock"></i> 
                                    @if($isLocked)
                                        Seleksi telah berakhir (Hasil Final).
                                    @elseif($myAnswer && $myAnswer->status == 'reviewed')
                                        Jawaban sudah dinilai final.
                                    @else
                                        Waktu ujian telah berakhir.
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-2"></i> <br>
                    Belum ada soal ujian yang tersedia untuk saat ini.
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-3">
        <a href="{{ route('dashboard.user') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection