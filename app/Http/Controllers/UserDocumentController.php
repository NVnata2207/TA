<?php
// Menentukan lokasi file ini
namespace App\Http\Controllers;

// Import library yang dibutuhkan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk mengelola file fisik (simpan/hapus file)
use App\Models\UserDocument; // Model untuk tabel dokumen user
use App\Models\Notification; // Model untuk tabel notifikasi
use App\Models\User; // Model User

class UserDocumentController extends Controller
{
    /**
     * Fungsi index menampilkan halaman daftar dokumen milik user yang sedang login.
     */
    public function index()
    {
        // Ambil data user yang sedang login saat ini.
        $user = auth()->user();

        // Daftar tipe dokumen yang wajib/bisa diupload.
        $types = [
            'formulir' => 'Formulir Pendaftaran',
            'akta' => 'Akta Kelahiran',
            'kk' => 'Kartu Keluarga',
            'ijazah' => 'Ijazah (jika ada)'
        ];

        // Ambil dokumen dari database milik user tersebut.
        // 'keyBy('type')' -> Mengubah struktur data agar index array-nya menggunakan nama tipe ('akta', 'kk', dll).
        // Ini memudahkan pengecekan di View (misal: apakah user sudah upload 'akta'?).
        $documents = UserDocument::where('user_id', $user->id)->get()->keyBy('type');

        // Tampilkan view dan kirim datanya.
        return view('user.documents.index', compact('types', 'documents'));
    }

    /**
     * Fungsi store menangani proses UPLOAD dokumen.
     * Fungsi ini menangani upload baru MAUPUN penggantian file (replace).
     */
    public function store(Request $request)
    {
        // Ambil user yang login.
        $user = auth()->user();

        // Validasi input.
        $request->validate([
            'type' => 'required|in:formulir,akta,kk,ijazah', // Tipe harus sesuai daftar.
            'file' => 'required|mimes:pdf|max:2048', // File wajib PDF dan maks 2MB (2048 KB).
        ]);

        // Simpan tipe dokumen ke variabel.
        $type = $request->type;

        // Cek apakah user sudah pernah upload dokumen tipe ini sebelumnya?
        $old = UserDocument::where('user_id', $user->id)->where('type', $type)->first();

        // Jika sudah ada file lama, HAPUS dulu file lamanya agar tidak menumpuk di server (sampah).
        if ($old) {
            Storage::delete($old->file_path); // Hapus file fisik dari folder storage.
            $old->delete(); // Hapus data lama dari database.
        }

        // Simpan file baru ke folder 'storage/app/public/user_documents/ID_USER'.
        // File akan disimpan di folder spesifik per user agar rapi.
        $path = $request->file('file')->store('user_documents/'.$user->id, 'public');

        // Simpan data file baru ke database.
        UserDocument::create([
            'user_id' => $user->id,
            'type' => $type,
            'file_path' => $path,
        ]);

        // LOGIKA NOTIFIKASI KE ADMIN:
        // Ambil semua user yang role-nya 'admin'.
        $admins = User::where('role', 'admin')->get();
        
        // Loop ke setiap admin untuk dikirimi notifikasi.
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id, // ID Admin penerima notif.
                'type' => 'admin', // Tipe notif untuk admin.
                // Pesan: "NamaSiswa telah mengupload/mengganti berkas: Akta"
                'message' => $user->name.' telah mengupload/mengganti berkas: '.ucwords($type),
            ]);
        }

        // Kembali ke halaman sebelumnya dengan pesan sukses.
        return back()->with('success', 'Berkas berhasil diupload.');
    }

    /**
     * Fungsi destroy untuk menghapus dokumen tertentu.
     * Menerima parameter $type (misal: 'akta', 'kk') bukan ID, karena satu tipe cuma boleh satu file.
     */
    public function destroy($type)
    {
        // Ambil user yang login.
        $user = auth()->user();

        // Cari dokumen berdasarkan user_id dan tipe dokumennya.
        $doc = UserDocument::where('user_id', $user->id)->where('type', $type)->first();

        // Jika dokumen ditemukan, hapus.
        if ($doc) {
            Storage::delete($doc->file_path); // Hapus file fisiknya.
            $doc->delete(); // Hapus datanya dari database.
        }

        //  Kembali dengan pesan sukses.
        return back()->with('success', 'Berkas berhasil dihapus.');
    }
}