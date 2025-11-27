<?php

namespace App\Http\Controllers;

// Mengimpor Model Announcement (Pengumuman)
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Fungsi index menampilkan daftar pengumuman.
     * Fungsi ini juga menangani logika untuk menampilkan form edit di halaman yang sama (modal/inline).
     */
    public function index(Request $request)
    {
        // Mengambil data pengumuman terbaru, 10 data per halaman.
        $announcements = Announcement::latest()->paginate(10);
        
        // Menyiapkan variabel edit. Awalnya null (kosong).
        $editAnnouncement = null;

        // Cek apakah ada parameter '?edit=ID' di URL.
        // Ini trik agar kita bisa memuat data yang mau diedit tanpa pindah ke halaman lain sepenuhnya.
        if ($request->has('edit')) {
            $editAnnouncement = Announcement::find($request->edit);
        }

        // Kirim data ke view.
        return view('admin.announcements.index', compact('announcements', 'editAnnouncement'));
    }

    // Menampilkan halaman form create (biasanya jika tidak pakai modal).
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Fungsi store menyimpan pengumuman baru dengan LOGIKA VALIDASI DINAMIS.
     */
    public function store(Request $request)
    {
        // Aturan validasi dasar (berlaku untuk semua tipe pengumuman).
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:pembukaan,masa_pendaftaran,ditutup,biasa', // Hanya boleh pilih salah satu tipe ini.
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar (maks 2MB).
            'show_on_login' => 'nullable|boolean',
        ];

        // LOGIKA DINAMIS: Tambahkan aturan validasi tergantung 'type' yang dipilih.
        
        // Jika tipe 'pembukaan', tanggal pembukaan WAJIB diisi.
        if ($request->type == 'pembukaan') {
            $rules['tanggal_pembukaan'] = 'required|date';
        }
        
        // Jika tipe 'masa_pendaftaran', tanggal mulai & selesai pendaftaran WAJIB diisi.
        if ($request->type == 'masa_pendaftaran') {
            $rules['tanggal_mulai_pendaftaran'] = 'required|date';
            // 'after_or_equal' memastikan tanggal selesai TIDAK BOLEH sebelum tanggal mulai.
            $rules['tanggal_selesai_pendaftaran'] = 'required|date|after_or_equal:tanggal_mulai_pendaftaran';
        }
        
        // Jika tipe 'ditutup', tanggal pengumuman & daftar ulang WAJIB diisi.
        if ($request->type == 'ditutup') {
            $rules['tanggal_pengumuman'] = 'required|date';
            $rules['tanggal_mulai_daftar_ulang'] = 'required|date';
            $rules['tanggal_selesai_daftar_ulang'] = 'required|date|after_or_equal:tanggal_mulai_daftar_ulang';
        }

        // Jalankan validasi dengan aturan yang sudah disusun di atas.
        $data = $request->validate($rules);

        // Proses Upload Gambar jika user mengunggah gambar.
        if ($request->hasFile('image')) {
            // Simpan gambar di folder 'storage/app/public/announcements'
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }

        // Ubah checkbox 'show_on_login' menjadi boolean (true/false).
        // $request->has(...) mengembalikan true jika checkbox dicentang, false jika tidak.
        $data['show_on_login'] = $request->has('show_on_login');

        // Otomatis mengisi 'academic_year_id' (Tahun Ajaran) jika belum ada di data.
        if (!isset($data['academic_year_id'])) {
            // Cari tahun ajaran yang sedang aktif.
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            // Jika ada tahun aktif, ambil ID-nya. Jika tidak ada, biarkan null.
            $data['academic_year_id'] = $activeYear ? $activeYear->id : null;
        }

        // Simpan data ke database.
        Announcement::create($data);

        // Redirect kembali ke index dengan pesan sukses.
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Fungsi edit ini unik. Alih-alih mereturn view, dia me-redirect ke halaman index
     * dengan membawa parameter '?edit=ID'. Ini mendukung logika di fungsi index() di atas.
     */
    public function edit(Announcement $announcement)
    {
        return redirect()->route('announcements.index', ['edit' => $announcement->id]);
    }

    /**
     * Fungsi update memperbarui data. Logikanya hampir 100% sama dengan store.
     */
    public function update(Request $request, Announcement $announcement)
    {
        // Definisi aturan dasar.
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:pembukaan,masa_pendaftaran,ditutup,biasa',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'show_on_login' => 'nullable|boolean',
        ];

        // Validasi Dinamis (sama seperti fungsi store).
        if ($request->type == 'pembukaan') {
            $rules['tanggal_pembukaan'] = 'required|date';
        }
        if ($request->type == 'masa_pendaftaran') {
            $rules['tanggal_mulai_pendaftaran'] = 'required|date';
            $rules['tanggal_selesai_pendaftaran'] = 'required|date|after_or_equal:tanggal_mulai_pendaftaran';
        }
        if ($request->type == 'ditutup') {
            $rules['tanggal_pengumuman'] = 'required|date';
            $rules['tanggal_mulai_daftar_ulang'] = 'required|date';
            $rules['tanggal_selesai_daftar_ulang'] = 'required|date|after_or_equal:tanggal_mulai_daftar_ulang';
        }

        // Eksekusi validasi.
        $data = $request->validate($rules);

        // Cek gambar baru.
        if ($request->hasFile('image')) {
            // (Opsional: Sebaiknya hapus gambar lama dulu disini jika ingin menghemat penyimpanan).
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }

        // Handle boolean show_on_login.
        $data['show_on_login'] = $request->has('show_on_login');

        // Handle Academic Year (cegah error jika user lupa set tahun ajaran, otomatis ambil yang aktif).
        if (!isset($data['academic_year_id'])) {
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $data['academic_year_id'] = $activeYear ? $activeYear->id : null;
        }

        // Update data di database.
        $announcement->update($data);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diupdate.');
    }

    // Menghapus pengumuman.
    public function destroy(Announcement $announcement)
    {
        // (Opsional: Sebaiknya tambahkan Storage::delete($announcement->image) disini untuk menghapus file fisik).
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Fungsi toggleShowOnLogin untuk mengubah status tampil di login (On/Off) secara cepat.
     * Biasanya dipanggil lewat tombol switch/toggle di tabel.
     */
    public function toggleShowOnLogin(Announcement $announcement)
    {
        // Membalikkan nilai boolean. Jika true jadi false, jika false jadi true.
        $announcement->show_on_login = !$announcement->show_on_login;
        
        // Simpan perubahan.
        $announcement->save();

        return redirect()->route('announcements.index')->with('success', 'Status tampil di login berhasil diubah.');
    }
}