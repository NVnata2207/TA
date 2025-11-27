<?php

// Menentukan namespace (lokasi file) controller ini, yaitu di dalam folder 'App/Http/Controllers/Admin'
namespace App\Http\Controllers\Admin;

// Mengimpor kelas-kelas yang dibutuhkan
use App\Http\Controllers\Controller; // Mengimpor Controller dasar Laravel
use App\Models\ExamQuestion;         // Mengimpor Model Soal Ujian
use App\Models\AcademicYear;        // Mengimpor Model Tahun Ajaran
use Illuminate\Http\Request;        // Mengimpor kelas Request untuk mengelola data form
use Illuminate\Support\Facades\Storage; // Mengimpor kelas Storage untuk mengelola file

// Mendefinisikan kelas controller yang mewarisi (extends) Controller dasar
class ExamQuestionController extends Controller
{
    /**
     * Fungsi 'index' ini bertugas untuk menampilkan halaman daftar semua soal ujian.
     */
    public function index()
    {
        // 1. Mengambil semua data 'ExamQuestion' dari database.
        // 'with('academicYear')' -> Eager loading, mengambil data relasi 'academicYear' dalam satu query (lebih efisien).
        // 'latest()' -> Mengurutkan data berdasarkan waktu pembuatan (terbaru dulu).
        // 'get()' -> Menjalankan query dan mengambil hasilnya.
        $questions = ExamQuestion::with('academicYear')->latest()->get();

        // 2. Menampilkan file view 'index.blade.php' yang ada di 'resources/views/admin/exam-questions/'.
        // 'compact('questions')' -> Mengirim variabel $questions ke dalam view.
        return view('admin.exam-questions.index', compact('questions'));
    }

    /**
     * Fungsi 'create' bertugas untuk menampilkan halaman form tambah soal ujian baru.
     */
    public function create()
    {
        // 1. Mengambil data tahun ajaran yang statusnya aktif ('is_active' = 1).
        // Ini biasanya digunakan untuk mengisi <select> atau dropdown di form.
        $academicYears = AcademicYear::where('is_active', 1)->get();

        // 2. Menampilkan view 'create.blade.php' dan mengirim data $academicYears ke view.
        return view('admin.exam-questions.create', compact('academicYears'));
    }

    /**
     * Fungsi 'store' bertugas untuk menyimpan data dari form tambah soal baru ke database.
     */
    public function store(Request $request) // $request berisi semua data yang dikirim dari form
    {
        // 1. Validasi data yang masuk.
        $request->validate([
            'title' => 'required|string|max:255', // Judul: wajib diisi, harus teks, maks 255 karakter.
            'file' => 'required|file|mimes:pdf|max:10240', // File: wajib diisi, harus file, format PDF, maks 10MB (10240 KB).
            'description' => 'nullable|string' // Deskripsi: boleh kosong, jika diisi harus teks.
        ]);

        // 2. Mencari data tahun ajaran yang sedang aktif.
        $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();

        // 3. Jika tidak ada tahun ajaran aktif, kembalikan ke halaman sebelumnya dengan pesan error.
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // 4. Mengambil file yang di-upload dari request.
        $file = $request->file('file');
        
        // 5. Menyimpan file ke 'storage/app/public/exam-questions'.
        // 'public' adalah nama disk. $path akan berisi path relatif (cth: 'exam-questions/namafileunik.pdf').
        $path = $file->store('exam-questions', 'public');

        // 6. Membuat (menyimpan) data baru ke tabel 'exam_questions'.
        ExamQuestion::create([
            'academic_year_id' => $activeYear->id, // Mengisi ID tahun ajaran aktif.
            'title' => $request->title, // Mengisi judul dari form.
            'file_path' => $path, // Mengisi path file yang sudah disimpan.
            'description' => $request->description // Mengisi deskripsi dari form.
        ]);

        // 7. Mengalihkan (redirect) pengguna kembali ke halaman index (daftar soal).
        // 'with('success', ...)' -> Mengirim pesan flash (notifikasi) sukses.
        return redirect()->route('admin.exam-questions.index')
            ->with('success', 'Soal ujian berhasil ditambahkan');
    }

    /**
     * Fungsi 'destroy' bertugas untuk menghapus data soal ujian.
     * Laravel otomatis mencari 'ExamQuestion' berdasarkan ID yang ada di URL.
     */
    public function destroy(ExamQuestion $question) // $question adalah data soal yang ingin dihapus
    {
        // 1. Menghapus file fisik (PDF) dari storage 'public' berdasarkan path yang tersimpan di database.
        Storage::disk('public')->delete($question->file_path);
        
        // 2. Menghapus data soal ujian dari tabel di database.
        $question->delete();

        // 3. Mengalihkan kembali ke halaman index dengan pesan sukses.
        return redirect()->route('admin.exam-questions.index')
            ->with('success', 'Soal ujian berhasil dihapus');
    }

    /**
     * Fungsi 'show' bertugas untuk menampilkan detail satu soal ujian beserta jawaban-jawabannya.
     */
    public function show(ExamQuestion $examQuestion) // $examQuestion adalah data soal yang ingin dilihat
    {
        // 1. Memuat relasi tambahan (Lazy Eager Loading) untuk $examQuestion yang sudah didapat.
        // 'academicYear' -> data tahun ajaran soal ini.
        // 'answers.user' -> data semua jawaban ('answers') DAN data pengguna ('user') yang mengirim jawaban tsb.
        $examQuestion->load(['academicYear', 'answers.user']);

        // 2. Menampilkan view 'show.blade.php' dan mengirim data $examQuestion (yg sudah lengkap dgn relasi) ke view.
        return view('admin.exam-questions.show', compact('examQuestion'));
    }

    /**
     * Fungsi 'updateAnswer' bertugas untuk memperbarui status/nilai dari SEBUAH jawaban siswa.
     * Fungsi ini BUKAN untuk mengedit soal, tapi untuk menilai jawaban.
     */
    public function updateAnswer(Request $request, $answerId) // $answerId adalah ID jawaban yang mau dinilai
    {
        // 1. Cari data jawaban ('ExamAnswer') berdasarkan $answerId.
        // 'findOrFail' -> Jika data tidak ketemu, otomatis tampilkan halaman 404 Not Found.
        $answer = \App\Models\ExamAnswer::findOrFail($answerId);

        // 2. Validasi data yang dikirim oleh admin (dari form penilaian).
        $request->validate([
            'status' => 'required|in:pending,reviewed,perlu_diubah', // Status: wajib, harus salah satu dari 3 pilihan.
            'score' => 'nullable|integer|min:0|max:100', // Nilai: boleh kosong, harus angka, minimal 0, maksimal 100.
            'admin_notes' => 'nullable|string', // Catatan admin: boleh kosong, harus teks.
        ]);

        // 3. Simpan status lama jawaban (sebelum diubah) untuk perbandingan.
        $oldStatus = $answer->status;

        // 4. Update data jawaban dengan data baru dari form.
        $answer->status = $request->status;
        $answer->score = $request->score;
        $answer->admin_notes = $request->admin_notes;

        // 5. Simpan perubahan ke database.
        $answer->save();

        // 6. Logika Notifikasi:
        // Jika status SEBELUMNYA BUKAN 'perlu_diubah' DAN status BARU ADALAH 'perlu_diubah'
        if ($oldStatus !== 'perlu_diubah' && $request->status === 'perlu_diubah') {
            
            // 7. Buat notifikasi baru di database untuk user yang bersangkutan.
            \App\Models\Notification::create([
                'user_id' => $answer->user_id, // ID user pemilik jawaban.
                'type' => 'user', // Tipe notifikasi (untuk user).
                'message' => 'Jawaban Anda perlu direvisi: ' . ($request->admin_notes ?: '-') // Pesan notifikasi.
            ]);
        }

        // 8. Kembalikan pengguna ke halaman sebelumnya (kemungkinan halaman 'show' soal) dengan pesan sukses.
        return back()->with('success', 'Status dan penilaian jawaban berhasil diperbarui.');
    }
}