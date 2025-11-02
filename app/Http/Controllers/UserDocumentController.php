<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserDocument;
use App\Models\Notification;
use App\Models\User;

class UserDocumentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $types = [
            'formulir' => 'Formulir Pendaftaran',
            'akta' => 'Akta Kelahiran',
            'kk' => 'Kartu Keluarga',
            'ijazah' => 'Ijazah (jika ada)'
        ];
        $documents = UserDocument::where('user_id', $user->id)->get()->keyBy('type');
        return view('user.documents.index', compact('types', 'documents'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'type' => 'required|in:formulir,akta,kk,ijazah',
            'file' => 'required|mimes:pdf|max:2048',
        ]);
        $type = $request->type;
        $old = UserDocument::where('user_id', $user->id)->where('type', $type)->first();
        if ($old) {
            Storage::delete($old->file_path);
            $old->delete();
        }
        $path = $request->file('file')->store('user_documents/'.$user->id, 'public');
        UserDocument::create([
            'user_id' => $user->id,
            'type' => $type,
            'file_path' => $path,
        ]);
        // Notifikasi ke admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'admin',
                'message' => $user->name.' telah mengupload/mengganti berkas: '.ucwords($type),
            ]);
        }
        return back()->with('success', 'Berkas berhasil diupload.');
    }

    public function destroy($type)
    {
        $user = auth()->user();
        $doc = UserDocument::where('user_id', $user->id)->where('type', $type)->first();
        if ($doc) {
            Storage::delete($doc->file_path);
            $doc->delete();
        }
        return back()->with('success', 'Berkas berhasil dihapus.');
    }
} 