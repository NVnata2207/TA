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

class UserController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $users = User::where('academic_year_id', $activeYear?->id)->orderByDesc('created_at')->paginate(10);
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

    public function show($id)
    {
        $user = User::findOrFail($id);
        $documents = UserDocument::where('user_id', $user->id)->get()->keyBy('type');
        
        $types = [
            'formulir' => 'Formulir Pendaftaran',
            'akta' => 'Akta Kelahiran',
            'kk' => 'Kartu Keluarga',
            'ijazah' => 'Ijazah (jika ada)'
        ];

        if (request('notif')) {
            $notif = Notification::find(request('notif'));
            if ($notif && $notif->user_id == auth()->id()) {
                $notif->read = true;
                $notif->save();
            }
        }

        return view('admin.users.show', compact('user', 'documents', 'types'));
    }

    public function edit(string $id)
    {
        //
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
        $user = auth()->user();
        
        $fields = FormField::where('is_active', true)
            ->orderBy('category')
            ->orderBy('order')
            ->get();
            
        $academicYear = AcademicYear::where('is_active', 1)->first();
        
        $data = [
            'user' => $user,
            'fields' => $fields,
            'academicYear' => $academicYear,
        ];
        
        $pdf = app('dompdf.wrapper')->loadView('pdf.registration_form', $data);
        
        return $pdf->download('formulir_pendaftaran_'.$user->kode_pendaftaran.'.pdf');
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

        return $pdf->download('Bukti_Lulus_' . $user->kode_pendaftaran . '.pdf');
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
} // <--- Penutup Class (Jangan Dihapus)