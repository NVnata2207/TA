<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamQuestion;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamQuestionController extends Controller
{
    public function index()
    {
        $questions = ExamQuestion::with('academicYear')->latest()->get();
        return view('admin.exam-questions.index', compact('questions'));
    }

    public function create()
    {
        $academicYears = AcademicYear::where('is_active', 1)->get();
        return view('admin.exam-questions.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240',
            'description' => 'nullable|string'
        ]);

        $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $file = $request->file('file');
        $path = $file->store('exam-questions', 'public');

        ExamQuestion::create([
            'academic_year_id' => $activeYear->id,
            'title' => $request->title,
            'file_path' => $path,
            'description' => $request->description
        ]);

        return redirect()->route('admin.exam-questions.index')
            ->with('success', 'Soal ujian berhasil ditambahkan');
    }

    public function destroy(ExamQuestion $question)
    {
        Storage::disk('public')->delete($question->file_path);
        $question->delete();

        return redirect()->route('admin.exam-questions.index')
            ->with('success', 'Soal ujian berhasil dihapus');
    }

    public function show(ExamQuestion $examQuestion)
    {
        $examQuestion->load(['academicYear', 'answers.user']);
        return view('admin.exam-questions.show', compact('examQuestion'));
    }

    public function updateAnswer(Request $request, $answerId)
    {
        $answer = \App\Models\ExamAnswer::findOrFail($answerId);
        $request->validate([
            'status' => 'required|in:pending,reviewed,perlu_diubah',
            'score' => 'nullable|integer|min:0|max:100',
            'admin_notes' => 'nullable|string',
        ]);
        $oldStatus = $answer->status;
        $answer->status = $request->status;
        $answer->score = $request->score;
        $answer->admin_notes = $request->admin_notes;
        $answer->save();
        // Notifikasi ke user jika status diubah ke perlu_diubah
        if ($oldStatus !== 'perlu_diubah' && $request->status === 'perlu_diubah') {
            \App\Models\Notification::create([
                'user_id' => $answer->user_id,
                'type' => 'user',
                'message' => 'Jawaban Anda perlu direvisi: ' . ($request->admin_notes ?: '-')
            ]);
        }
        return back()->with('success', 'Status dan penilaian jawaban berhasil diperbarui.');
    }
} 