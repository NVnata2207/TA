<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamQuestion;
use App\Models\ExamAnswer;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // Library untuk pengolahan tanggal dan waktu

class ExamController extends Controller
{
    /**
     * Fungsi index menampilkan halaman ujian.
     * Halaman ini berisi daftar soal dan status jawaban siswa.
     */
    public function index()
    {
        // Ambil data user yang sedang login.
        $user = auth()->user();
        
        // Cari tahun ajaran yang sedang aktif.
        $activeYear = AcademicYear::where('is_active', 1)->first();
        
        // Jika tidak ada tahun ajaran aktif, tolak akses dan kembalikan ke dashboard.
        if (!$activeYear) {
            return redirect()->route('dashboard.user')
                ->with('error', 'Tidak ada tahun ajaran aktif');
        }

        // Siapkan variabel waktu.
        $now = Carbon::now(); // Waktu sekarang.
        $startDate = Carbon::parse($activeYear->mulai_seleksi); // Tanggal mulai ujian.
        $endDate = Carbon::parse($activeYear->selesai_seleksi); // Tanggal selesai ujian.

        // Cek Validasi Peserta: Apakah user statusnya sudah diverifikasi admin?
        if ($user->status_pendaftaran !== 'Sudah Diverifikasi') {
            return redirect()->route('dashboard.user')
                ->with('error', 'Anda belum diverifikasi untuk mengikuti ujian');
        }

        // Cek Waktu Ujian: Apakah sekarang berada di LUAR jadwal ujian? (Belum mulai ATAU Sudah lewat).
        if ($now < $startDate || $now > $endDate) {
            return redirect()->route('dashboard.user')
                ->with('error', 'Ujian belum dimulai atau sudah berakhir');
        }

        // Ambil semua soal ujian untuk tahun ajaran ini.
        $questions = ExamQuestion::where('academic_year_id', $activeYear->id)->get();
        
        // Ambil jawaban yang SUDAH pernah diupload user ini.
        // 'whereIn' -> Hanya ambil jawaban untuk soal-soal yang ada di list $questions.
        // 'keyBy' -> Mengubah index array jadi ID soal. Ini memudahkan di View untuk cek: "Soal ID 5 sudah dijawab belum?"
        $answers = ExamAnswer::where('user_id', $user->id)
            ->whereIn('exam_question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('exam_question_id');

        // Tampilkan view ujian.
        return view('user.exam.index', compact('questions', 'answers', 'activeYear'));
    }

    /**
     * Fungsi download untuk mengunduh file SOAL ujian (PDF).
     */
    public function download(ExamQuestion $question) // $question didapat dari ID di URL
    {
        $user = auth()->user();
        $activeYear = AcademicYear::where('is_active', 1)->first();
        
        // Validasi Soal: Pastikan soal tersebut milik tahun ajaran yang sedang aktif.
        if (!$activeYear || $question->academic_year_id !== $activeYear->id) {
            return redirect()->route('user.exam.index')
                ->with('error', 'Soal tidak ditemukan');
        }

        // Validasi User: Harus sudah diverifikasi.
        if ($user->status_pendaftaran !== 'Sudah Diverifikasi') {
            return redirect()->route('user.exam.index')
                ->with('error', 'Anda belum diverifikasi untuk mengikuti ujian');
        }

        // Validasi Waktu: Tidak boleh download jika ujian belum mulai/sudah selesai.
        $now = Carbon::now();
        $startDate = Carbon::parse($activeYear->mulai_seleksi);
        $endDate = Carbon::parse($activeYear->selesai_seleksi);

        if ($now < $startDate || $now > $endDate) {
            return redirect()->route('user.exam.index')
                ->with('error', 'Ujian belum dimulai atau sudah berakhir');
        }

        // Proses download file dari storage public.
        return Storage::disk('public')->download($question->file_path);
    }

    /**
     * Fungsi submit untuk mengupload JAWABAN ujian.
     */
    public function submit(Request $request, ExamQuestion $question)
    {
        $user = auth()->user();
        $activeYear = AcademicYear::where('is_active', 1)->first();
        
        // --- BLOK VALIDASI (Sama seperti fungsi download) ---
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
        // --- END BLOK VALIDASI ---

        // Cek apakah user sudah pernah upload jawaban untuk soal ini?
        $answer = ExamAnswer::where('user_id', $user->id)
            ->where('exam_question_id', $question->id)
            ->first();
        
        $isReupload = false; // Penanda apakah ini upload baru atau revisi.

        // Logika Revisi Jawaban:
        if ($answer) {
            // Jika jawaban sudah ada, TAPI statusnya BUKAN 'perlu_diubah' (misal: pending atau reviewed),
            // maka tolak upload ulang. User hanya boleh upload ulang jika diminta admin.
            if ($answer->status !== 'perlu_diubah') {
                return redirect()->route('user.exam.index')->with('error', 'Anda tidak bisa upload ulang kecuali status Perlu Diubah oleh admin.');
            }
            $isReupload = true; // Tandai ini sebagai revisi.
        }

        // Validasi file jawaban (PDF, maks 10MB).
        $request->validate([
            'answer_file' => 'required|file|mimes:pdf|max:10240'
        ]);

        // Simpan file jawaban.
        $file = $request->file('answer_file');
        $path = $file->store('exam-answers', 'public');

        // Update atau Buat Data Jawaban.
        ExamAnswer::updateOrCreate(
            [
                // Kriteria pencarian (WHERE):
                'user_id' => $user->id,
                'exam_question_id' => $question->id
            ],
            [
                // Data yang diupdate/dibuat:
                'file_path' => $path,
                'status' => 'pending', // Reset status jadi 'pending' (menunggu review admin).
                'score' => null,       // Reset nilai (karena jawaban baru).
                'admin_notes' => null  // Reset catatan admin.
            ]
        );

        // Notifikasi Khusus: Jika ini adalah upload ulang (revisi), beri tahu semua admin.
        if ($isReupload) {
            // Ambil semua ID admin.
            $adminIds = \App\Models\User::where('role', 'admin')->pluck('id');
            
            foreach ($adminIds as $adminId) {
                \App\Models\Notification::create([
                    'user_id' => $adminId,
                    'type' => 'admin',
                    'message' => 'User ' . $user->name . ' telah mengupload ulang jawaban untuk soal "' . $question->title . '".'
                ]);
            }
        }

        // Redirect dengan pesan sukses.
        return redirect()->route('user.exam.index')
            ->with('success', 'Jawaban berhasil diupload');
    }
}