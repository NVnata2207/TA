<?php

// Menentukan namespace (lokasi file) controller ini
namespace App\Http\Controllers;

// Mengimpor kelas-kelas yang dibutuhkan
use App\Models\AcademicYear; // Mengimpor Model Tahun Ajaran
use Illuminate\Http\Request; // Mengimpor kelas Request untuk mengelola data form/URL

// Mendefinisikan kelas controller yang mewarisi (extends) Controller dasar
class AcademicYearController extends Controller
{
    /**
     * Fungsi 'index' ini menampilkan halaman utama Tahun Ajaran.
     * Halaman ini biasanya menampilkan DAFTAR tahun ajaran, dan
     * berdasarkan kodingan ini, juga menampilkan FORM EDIT jika ada parameter 'edit' di URL.
     */
    public function index(Request $request)
    {
        //  Mengambil data tahun ajaran, diurutkan berdasarkan 'name' secara descending (Z ke A, atau 2024 ke 2023).
        // 'paginate(10)' -> Mengambil data 10 per halaman (untuk penomoran halaman/pagination).
        $academic_years = AcademicYear::orderByDesc('name')->paginate(10);

        // Menyiapkan variabel $academicYearEdit dengan nilai awal null.
        $academicYearEdit = null;

        // Mengecek apakah di URL ada parameter 'edit' (contoh: .../academic_years?edit=5)
        if ($request->has('edit')) {
            // Jika ada, cari data AcademicYear berdasarkan ID yang ada di parameter 'edit'.
            $academicYearEdit = AcademicYear::find($request->edit);
        }

        // Menampilkan view 'index.blade.php' di folder 'admin/academic_years/'.
        // 'compact(...)' -> Mengirim variabel $academic_years (daftar) dan $academicYearEdit (data u/ diedit) ke view.
        return view('admin.academic_years.index', compact('academic_years', 'academicYearEdit'));
    }

    /**
     * Fungsi 'create' menampilkan halaman form untuk membuat tahun ajaran BARU.
     * (Catatan: Berdasarkan fungsi index, sepertinya form 'create' juga ada di halaman index).
     */
    public function create()
    {
        // Menampilkan view 'create.blade.php'
        return view('admin.academic_years.create');
    }

    /**
     * Fungsi 'store' bertugas untuk MENYIMPAN data dari form 'create' ke database.
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk dari form.
        $request->validate([
            // 'name' -> Wajib diisi, harus teks, dan harus UNIK (tidak boleh sama) di tabel 'academic_years'.
            'name' => 'required|string|unique:academic_years,name',
            'kuota' => 'nullable|integer', // Kuota -> Boleh kosong, jika diisi harus angka.
            'mulai_pendaftaran' => 'nullable|date', // Boleh kosong, jika diisi harus format tanggal.
            'selesai_pendaftaran' => 'nullable|date',
            'mulai_seleksi' => 'nullable|date',
            'selesai_seleksi' => 'nullable|date',
            'tanggal_pengumuman' => 'nullable|date',
            'mulai_daftar_ulang' => 'nullable|date',
            'selesai_daftar_ulang' => 'nullable|date',
            'is_active' => 'nullable|boolean', // Boleh kosong, jika diisi harus boolean (true/false, 1/0).
        ]);

        // Membuat data baru di tabel 'academic_years'.
        AcademicYear::create([
            'name' => $request->name,
            'kuota' => $request->kuota,
            'mulai_pendaftaran' => $request->mulai_pendaftaran,
            'selesai_pendaftaran' => $request->selesai_pendaftaran,
            'mulai_seleksi' => $request->mulai_seleksi,
            'selesai_seleksi' => $request->selesai_seleksi,
            'tanggal_pengumuman' => $request->tanggal_pengumuman,
            'mulai_daftar_ulang' => $request->mulai_daftar_ulang,
            'selesai_daftar_ulang' => $request->selesai_daftar_ulang,
            // Cek 'is_active': Jika ada nilainya (cth: 'on' atau 1 dari checkbox), simpan sbg 'true'. Jika tidak (unchecked), simpan sbg 'false'.
            'is_active' => $request->is_active ? true : false,
        ]);

        // Redirect kembali ke halaman index (daftar) dengan pesan sukses.
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Fungsi 'edit' menampilkan halaman form untuk MENGEDIT data tahun ajaran.
     * Laravel otomatis mencari 'AcademicYear' berdasarkan ID di URL (Route Model Binding).
     */
    public function edit(AcademicYear $academic_year) // $academic_year berisi data yg mau diedit
    {
        // Menampilkan view 'edit.blade.php' dan mengirim data $academic_year ke view.
        return view('admin.academic_years.edit', compact('academic_year'));
    }

    /**
     * Fungsi 'update' bertugas untuk MEMPERBARUI data di database dari form 'edit'.
     */
    public function update(Request $request, AcademicYear $academic_year) // $academic_year adalah data LAMA
    {
        // Validasi data yang masuk.
        $request->validate([
            // 'name' -> Wajib, teks, UNIK, TAPI mengabaikan ID data itu sendiri ($academic_year->id).
            // Ini agar kita bisa save tanpa error "nama sudah ada" jika kita tidak mengubah nama.
            'name' => 'required|string|unique:academic_years,name,' . $academic_year->id,
            'kuota' => 'nullable|integer',
            'mulai_pendaftaran' => 'nullable|date',
            'selesai_pendaftaran' => 'nullable|date',
            'mulai_seleksi' => 'nullable|date',
            'selesai_seleksi' => 'nullable|date',
            'tanggal_pengumuman' => 'nullable|date',
            'mulai_daftar_ulang' => 'nullable|date',
            'selesai_daftar_ulang' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        // Update data $academic_year (data lama) dengan data dari $request (data baru).
        $academic_year->update([
            'name' => $request->name,
            'kuota' => $request->kuota,
            'mulai_pendaftaran' => $request->mulai_pendaftaran,
            'selesai_pendaftaran' => $request->selesai_pendaftaran,
            'mulai_seleksi' => $request->mulai_seleksi,
            'selesai_seleksi' => $request->selesai_seleksi,
            'tanggal_pengumuman' => $request->tanggal_pengumuman,
            'mulai_daftar_ulang' => $request->mulai_daftar_ulang,
            'selesai_daftar_ulang' => $request->selesai_daftar_ulang,
            'is_active' => $request->is_active ? true : false, // Logika checkbox yg sama dgn 'store'
        ]);

        // Redirect kembali ke halaman index dengan pesan sukses.
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil diupdate.');
    }

    /**
     * Fungsi 'destroy' bertugas untuk MENGHAPUS satu data tahun ajaran.
     */
    public function destroy(AcademicYear $academic_year) // $academic_year adalah data yg mau dihapus
    {
        // Hapus data dari database.
        $academic_year->delete();

        // Redirect kembali ke halaman index dengan pesan sukses.
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Fungsi 'setActive' (BUKAN CRUD) untuk MENETAPKAN satu tahun ajaran sebagai AKTIF.
     * Logika bisnisnya: Hanya boleh ada 1 tahun ajaran yang aktif.
     */
    public function setActive(AcademicYear $academic_year) // $academic_year adalah data yg mau di-set aktif
    {
        // Update SEMUA data di tabel 'academic_years', set 'is_active' menjadi 'false' (nonaktifkan semua).
        AcademicYear::query()->update(['is_active' => false]);
        
        // Update HANYA data $academic_year yang dipilih, set 'is_active' menjadi 'true'.
        $academic_year->update(['is_active' => true]);

        // Redirect kembali ke halaman index dengan pesan sukses.
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran aktif berhasil diubah.');
    }

    /**
     * Fungsi 'unsetActive' (BUKAN CRUD) untuk MENONAKTIFKAN satu tahun ajaran.
     * Ini berbeda dari 'setActive' karena tidak menonaktifkan yang lain.
     */
    public function unsetActive(AcademicYear $academic_year) // $academic_year adalah data yg mau di-nonaktifkan
    {
        // Update HANYA data $academic_year yang dipilih, set 'is_active' menjadi 'false'.
        $academic_year->update(['is_active' => false]);
        
        // Redirect kembali ke halaman index dengan pesan sukses.
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil dinonaktifkan.');
    }

    /**
     * Fungsi 'bulkDelete' (BUKAN CRUD) untuk MENGHAPUS BANYAK data sekaligus.
     * Biasanya digunakan untuk fitur "checkbox" di tabel.
     */
    public function bulkDelete(Request $request)
    {
        // Ambil data 'ids' dari form. 'ids' diharapkan berupa array [1, 2, 5, ...].
        // Jika 'ids' tidak ada, gunakan array kosong [].
        $ids = $request->input('ids', []);

        // Cek apakah array $ids tidak kosong.
        if (!empty($ids)) {
            // 3. Hapus semua data di tabel 'academic_years' YANG ID-nya ADA DI DALAM array $ids.
            AcademicYear::whereIn('id', $ids)->delete();
        }

        // Redirect kembali ke halaman index dengan pesan sukses.
        return redirect()->route('academic_years.index')->with('success', 'Data terpilih berhasil dihapus.');
    }
}