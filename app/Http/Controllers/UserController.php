<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// PERBAIKAN PENTING: Gunakan 'Facade' (tanpa s) agar tidak error Class Not Found
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\AcademicYear;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\FormField;
use App\Models\DocumentRequirement;

class UserController extends Controller
{
public function index(Request $request)
    {
        // 1. Ambil Tahun Ajaran Aktif
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

        // 2. Dasar Query: Cari User yang Role-nya 'user' (Siswa)
        $query = \App\Models\User::where('role', 'user');

        // Jika ada tahun ajaran aktif, filter hanya siswa tahun ini
        if ($activeYear) {
            $query->where('academic_year_id', $activeYear->id);
        }

        // 3. LOGIKA PENCARIAN (SEARCH)
        // Jika ada input 'search' dari form, tambahkan filter ini
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('nisn', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('kode_pendaftaran', 'LIKE', '%' . $keyword . '%');
            });
        }

        // 4. Ambil Data (Paginate)
        // withQueryString() berguna agar saat pindah halaman (page 2), hasil pencarian tidak hilang
        $users = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

// Pastikan di bagian atas file sudah ada: 
    // use App\Models\DocumentRequirement;
    // use App\Models\Notification;

    public function show($id)
    {
        // 1. AMBIL DATA SISWA & DOKUMENNYA
        // Kita pakai 'with' agar lebih efisien
        $user = User::with('documents')->findOrFail($id);

        // 2. LOGIKA NOTIFIKASI (DIPERTAHANKAN DARI KODINGAN LAMA)
        // Agar saat diklik dari notifikasi, statusnya berubah jadi 'read'
        if (request('notif')) {
            $notif = Notification::find(request('notif'));
            if ($notif && $notif->user_id == auth()->id()) {
                $notif->read = true;
                $notif->save();
            }
        }

        // 3. AMBIL DAFTAR SYARAT DOKUMEN (DINAMIS)
        // Ini menggantikan array manual $types = [...] yang lama.
        $requirements = DocumentRequirement::where('is_active', 1)->get();

        // 4. KIRIM KE VIEW
        // Kita tidak perlu lagi kirim $types atau $documents terpisah
        return view('admin.users.show', compact('user', 'requirements'));
    } 

    public function update(Request $request, User $user)
    {
        $request->validate([
            'status_pendaftaran' => 'nullable|in:Pendaftar Baru,Sudah Diverifikasi,Berkas Kurang,Berkas Tidak Sesuai',
            'hasil' => 'nullable|in:Di Terima,Tidak Diterima',
            'daftar_ulang' => 'nullable|in:Belum Daftar Ulang,Sudah Daftar Ulang',
            'status_comment' => 'nullable|string|max:255',
        ]);

        if ($request->has('status_pendaftaran')) {
            $user->status_pendaftaran = $request->status_pendaftaran;
            
            if (in_array($request->status_pendaftaran, ['Berkas Kurang', 'Berkas Tidak Sesuai'])) {
                $comment = $request->status_comment;
                if (!$comment) {
                    return back()->withErrors(['status_comment' => 'Komentar wajib diisi untuk status ini.']);
                }
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'user',
                    'message' => 'Status pendaftaran Anda: ' . $request->status_pendaftaran . '. Komentar admin: ' . $comment,
                ]);
            }
            
            if ($request->status_pendaftaran === 'Sudah Diverifikasi') {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'user',
                    'message' => 'Status pendaftaran Anda sudah diverifikasi. Silakan lanjut ke tahap Seleksi/Ujian.',
                ]);
            }
        }

        if ($request->has('hasil')) {
            $user->hasil = $request->hasil;
        }

        if ($request->has('daftar_ulang')) {
            $user->daftar_ulang = $request->daftar_ulang;
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Data peserta berhasil diubah.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Data peserta berhasil dihapus.');
    }

    public function downloadRegistrationForm()
    {
        // 1. Ambil Tahun Ajaran Aktif
        $academicYear = \App\Models\AcademicYear::where('is_active', 1)->first();

        // 2. Ambil Field Formulir (Pertanyaan)
        $fields = \App\Models\FormField::where('is_active', 1)
                            ->orderBy('order', 'asc')
                            ->get();

        // 3. (BARU) AMBIL MASTER DOKUMEN DARI DATABASE
        // Ini agar lampiran di PDF dinamis mengikuti Admin
        $documents = \App\Models\DocumentRequirement::where('is_active', 1)->get();

        // 4. Load View dengan membawa data $documents
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.print_registration', compact('academicYear', 'fields', 'documents'));
        
        return $pdf->stream('Formulir-PPDB.pdf');
    }

    // ==========================================
    // TAMBAHAN KODINGAN BARU (LETKKAN DI SINI)
    // ==========================================

    public function printAcceptance()
    {
        $user = auth()->user();

        // Cek apakah lulus
        if ($user->hasil !== 'Di Terima') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mencetak dokumen ini.');
        }

        $activeYear = AcademicYear::where('is_active', 1)->first();

        // Generate PDF (Menggunakan Facade Pdf yang sudah diimport di atas)
        $pdf = Pdf::loadView('pdf.acceptance_letter', compact('user', 'activeYear'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Bukti_Lulus_' . $user->kode_pendaftaran . '.Pdf');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if ($ids) {
            $idsArray = explode(',', $ids);
            
            // Mencegah admin menghapus dirinya sendiri
            $idsArray = array_diff($idsArray, [auth()->id()]); 

            User::whereIn('id', $idsArray)->delete();
            
            return back()->with('success', 'Data terpilih berhasil dihapus.');
        }

        return back()->with('error', 'Tidak ada data yang dipilih.');
    }

// --- FUNGSI EXPORT EXCEL (VERSI TAMPILAN RAPI / HTML TABLE) ---
    public function exportExcel()
    {
        // Nama file
        $fileName = 'rekap_peserta_' . date('d-m-Y_H-i') . '.xls';
        
        // Ambil data peserta (kecuali admin)
        $users = User::where('role', '!=', 'admin')->orderBy('created_at', 'desc')->get(); 

        // Header agar browser mengenali ini sebagai file Excel
        $headers = [
            "Content-Type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$fileName\""
        ];

        return response()->streamDownload(function() use($users) {
            // Kita buat tabel HTML biasa, Excel akan otomatis mengubahnya jadi kotak-kotak
            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';
            echo '<body>';
            echo '<table border="1" style="border-collapse: collapse;">';
            
            // --- HEADER TABEL (WARNA ABU) ---
            echo '<thead>';
            echo '<tr style="background-color: #d9edf7; font-weight: bold; text-align: center;">';
            echo '<th style="width: 150px;">Kode Pendaftaran</th>';
            echo '<th style="width: 200px;">Nama Lengkap</th>';
            echo '<th style="width: 200px;">Email</th>';
            echo '<th style="width: 150px;">Status Pendaftaran</th>';
            echo '<th style="width: 100px;">Hasil Seleksi</th>';
            echo '<th style="width: 150px;">Status Daftar Ulang</th>';
            echo '<th style="width: 150px;">Tanggal Daftar</th>';
            echo '</tr>';
            echo '</thead>';
            
            // --- ISI DATA ---
            echo '<tbody>';
            foreach ($users as $user) {
                // Tentukan warna status hasil
                $bgHasil = '';
                if($user->hasil == 'Di Terima') $bgHasil = '#dff0d8'; // Hijau muda
                if($user->hasil == 'Tidak Diterima') $bgHasil = '#f2dede'; // Merah muda

                echo '<tr>';
                // Gunakan style mso-number-format untuk memaksa Kode dibaca sebagai Text (agar 0 di depan tidak hilang)
                echo '<td style="mso-number-format:\@;">' . $user->kode_pendaftaran . '</td>';
                echo '<td>' . $user->name . '</td>';
                echo '<td>' . $user->email . '</td>';
                echo '<td>' . $user->status_pendaftaran . '</td>';
                echo '<td style="background-color: '.$bgHasil.';">' . $user->hasil . '</td>';
                echo '<td>' . $user->daftar_ulang . '</td>';
                echo '<td>' . $user->created_at->format('d/m/Y H:i') . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</body></html>';
            
        }, $fileName, $headers);
    }
    // =======================================================================
    // TAMBAHKAN INI AGAR ERROR "METHOD DOES NOT EXIST" HILANG
    // =======================================================================

    public function showForm()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();
        
        // Tampilkan file view formulir
        return view('user.form_registration', compact('user'));
    }

   public function updateProfile(Request $request)
    {
    $user = auth()->user();

    // 1. Validasi (Sesuaikan kebutuhan)
    $request->validate([
        'name' => 'required|string',
        'nisn' => 'nullable|numeric',
        // ... validasi lain ...
    ]);

    // 2. Simpan Data Akun Utama (Nama di tabel users)
    $user->update([
        'name' => $request->name
    ]);

    // 3. Simpan Data Detail Siswa (Di tabel student_details)
    // updateOrCreate: Jika belum ada data detail, buat baru. Jika sudah ada, update.
    $user->studentDetail()->updateOrCreate(
        ['user_id' => $user->id], // Kunci pencarian
        $request->except(['_token', '_method', 'name', 'email']) // Data yang disimpan (kecuali token & data user utama)
    );

    return redirect()->back()->with('success', 'Data biodata berhasil disimpan!');
    }
} // <--- Penutup Class (Jangan Dihapus)