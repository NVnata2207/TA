<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamQuestion;
use App\Models\ExamAnswer;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeYear = AcademicYear::where('is_active', 1)->first();
        
        if (!$activeYear) {
            return redirect()->route('dashboard.user')
                ->with('error', 'Tidak ada tahun ajaran aktif');
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($activeYear->mulai_seleksi);
        $endDate = Carbon::parse($activeYear->selesai_seleksi);

        if ($user->status_pendaftaran !== 'Sudah Diverifikasi') {
            return redirect()->route('dashboard.user')
                ->with('error', 'Anda belum diverifikasi untuk mengikuti ujian');
        }

        if ($now < $startDate || $now > $endDate) {
            return redirect()->route('dashboard.user')
                ->with('error', 'Ujian belum dimulai atau sudah berakhir');
        }

        $questions = ExamQuestion::where('academic_year_id', $activeYear->id)->get();
        $answers = ExamAnswer::where('user_id', $user->id)
            ->whereIn('exam_question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('exam_question_id');

        return view('user.exam.index', compact('questions', 'answers', 'activeYear'));
    }

    public function download(ExamQuestion $question)
    {
        $user = auth()->user();
        $activeYear = AcademicYear::where('is_active', 1)->first();
        
        if (!$activeYear || $question->academic_year_id !== $activeYear->id) {
            return redirect()->route('user.exam.index')
                ->with('error', 'Soal tidak ditemukan');
        }

        if ($user->status_pendaftaran !== 'Sudah Diverifikasi') {
            return redirect()->route('user.exam.index')
                ->with('error', 'Anda belum diverifikasi untuk mengikuti ujian');
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($activeYear->mulai_seleksi);
        $endDate = Carbon::parse($activeYear->selesai_seleksi);

        if ($now < $startDate || $now > $endDate) {
            return redirect()->route('user.exam.index')
                ->with('error', 'Ujian belum dimulai atau sudah berakhir');
        }

        return Storage::disk('public')->download($question->file_path);
    }

    public function submit(Request $request, ExamQuestion $question)
    {
        $user = auth()->user();
        $activeYear = AcademicYear::where('is_active', 1)->first();
        
        if (!$activeYear || $question->academic_year_id !== $activeYear->id) {
            return redirect()->route('user.exam.index')
                ->with('error', 'Soal tidak ditemukan');
        }

        if ($user->status_pendaftaran !== 'Sudah Diverifikasi') {
            return redirect()->route('user.exam.index')
                ->with('error', 'Anda belum diverifikasi untuk mengikuti ujian');
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($activeYear->mulai_seleksi);
        $endDate = Carbon::parse($activeYear->selesai_seleksi);

        if ($now < $startDate || $now > $endDate) {
            return redirect()->route('user.exam.index')
                ->with('error', 'Ujian belum dimulai atau sudah berakhir');
        }

        $answer = ExamAnswer::where('user_id', $user->id)
            ->where('exam_question_id', $question->id)
            ->first();
        $isReupload = false;
        if ($answer) {
            if ($answer->status !== 'perlu_diubah') {
                return redirect()->route('user.exam.index')->with('error', 'Anda tidak bisa upload ulang kecuali status Perlu Diubah oleh admin.');
            }
            $isReupload = true;
        }
        $request->validate([
            'answer_file' => 'required|file|mimes:pdf|max:10240'
        ]);
        $file = $request->file('answer_file');
        $path = $file->store('exam-answers', 'public');
        ExamAnswer::updateOrCreate(
            [
                'user_id' => $user->id,
                'exam_question_id' => $question->id
            ],
            [
                'file_path' => $path,
                'status' => 'pending',
                'score' => null,
                'admin_notes' => null
            ]
        );
        // Notifikasi ke admin jika user upload ulang
        if ($isReupload) {
            $adminIds = \App\Models\User::where('role', 'admin')->pluck('id');
            foreach ($adminIds as $adminId) {
                \App\Models\Notification::create([
                    'user_id' => $adminId,
                    'type' => 'admin',
                    'message' => 'User ' . $user->name . ' telah mengupload ulang jawaban untuk soal "' . $question->title . '".'
                ]);
            }
        }
        return redirect()->route('user.exam.index')
            ->with('success', 'Jawaban berhasil diupload');
    }
} 