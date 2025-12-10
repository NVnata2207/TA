<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserDocument;
use App\Models\Notification;
use App\Models\User;
use App\Models\DocumentRequirement;

class UserDocumentController extends Controller
{
    /**
     * Menampilkan halaman upload dokumen
     */
    public function index()
    {
        $user = auth()->user();
        
        // Ambil daftar dokumen yang wajib diupload (dari database Admin)
        $requirements = DocumentRequirement::where('is_active', 1)->get();
        
        // Load relasi dokumen user agar efisien
        $user->load('documents'); 

        return view('user.documents.index', compact('user', 'requirements'));
    }

    /**
     * Proses Upload / Ganti Dokumen
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
            'requirement_id' => 'required', 
        ]);

        $user = auth()->user();

        if ($request->hasFile('file')) {
            
            // 2. HAPUS FILE LAMA (Jika ada)
            $oldDoc = UserDocument::where('user_id', $user->id)
                        ->where('requirement_id', $request->requirement_id)
                        ->first();

            if ($oldDoc) {
                // Hapus file fisik lama
                if (Storage::disk('public')->exists($oldDoc->file_path)) {
                    Storage::disk('public')->delete($oldDoc->file_path);
                }
            }

            // 3. UPLOAD FILE BARU
            $file = $request->file('file');
            
            $docName = $request->document_type ?? 'Dokumen';
            // Nama file: NamaDokumen_NamaSiswa_Timestamp.pdf
            $filename = str_replace(' ', '_', $docName) . '_' . str_replace(' ', '_', $user->name) . '_' . time() . '.pdf';
            
            $path = $file->storeAs('documents', $filename, 'public');

            // 4. SIMPAN KE DATABASE
            UserDocument::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'requirement_id' => $request->requirement_id, 
                ],
                [
                    'type' => $request->document_type, 
                    'file_path' => $path,
                ]
            );
            // 5. KIRIM NOTIFIKASI KE ADMIN
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id, 
                    'type' => 'admin', 
                    'message' => $user->name.' telah mengupload/mengganti berkas: '.$docName,
                    'read' => false,
                    
                    // 👇 INI KUNCINYA: Simpan Link Detail Siswa 👇
                    // Pastikan route 'users.show' ada di web.php Anda (bawaan Route::resource users)
                    'link' => route('users.show', $user->id),
                ]);
            }

            return back()->with('success', 'Berkas berhasil diupload.');
        }

        return back()->with('error', 'Gagal mengupload berkas.');
    }

    /**
     * Hapus Dokumen
     * Sekarang menerima parameter $id (ID Dokumen), bukan string type
     */
    public function destroy($id)
    {
        $user = auth()->user();

        // Cari dokumen berdasarkan ID (dan pastikan milik user yang sedang login)
        $doc = UserDocument::where('id', $id)->where('user_id', $user->id)->first();

        if ($doc) {
            // Hapus file fisik
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            
            // Hapus data database
            $doc->delete();
            
            return back()->with('success', 'Berkas berhasil dihapus.');
        }

        return back()->with('error', 'Berkas tidak ditemukan.');
    }
}