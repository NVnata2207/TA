<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facades\Pdf;
use App\Models\AcademicYear;
use App\Models\Notification;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $users = \App\Models\User::where('academic_year_id', $activeYear?->id)->orderByDesc('created_at')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $documents = \App\Models\UserDocument::where('user_id', $user->id)->get()->keyBy('type');
        $types = [
            'formulir' => 'Formulir Pendaftaran',
            'akta' => 'Akta Kelahiran',
            'kk' => 'Kartu Keluarga',
            'ijazah' => 'Ijazah (jika ada)'
        ];
        // Mark notif as read if notif param exists
        if (request('notif')) {
            $notif = \App\Models\Notification::find(request('notif'));
            if ($notif && $notif->user_id == auth()->id()) {
                $notif->read = true;
                $notif->save();
            }
        }
        return view('admin.users.show', compact('user', 'documents', 'types'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'status_pendaftaran' => 'nullable|in:Pendaftar Baru,Sudah Diverifikasi,Berkas Kurang,Berkas Tidak Sesuai',
            'hasil' => 'nullable|in:Di Terima,Tidak Diterima',
            'daftar_ulang' => 'nullable|in:Belum Daftar Ulang,Sudah Daftar Ulang',
            'status_comment' => 'nullable|string|max:255',
        ]);
        if ($request->has('status_pendaftaran')) {
            $user->status_pendaftaran = $request->status_pendaftaran;
            // Notifikasi jika status Berkas Kurang/Tidak Sesuai
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
            // Notifikasi jika status diverifikasi
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generate and download registration form as PDF for the authenticated user.
     */
    public function downloadRegistrationForm()
    {
        $user = auth()->user();
        $fields = \App\Models\FormField::where('is_active', true)
            ->orderBy('category')
            ->orderBy('order')
            ->get();
        $academicYear = \App\Models\AcademicYear::where('is_active', 1)->first();
        $data = [
            'user' => $user,
            'fields' => $fields,
            'academicYear' => $academicYear,
        ];
        $pdf = app('dompdf.wrapper')->loadView('pdf.registration_form', $data);
        return $pdf->download('formulir_pendaftaran_'.$user->kode_pendaftaran.'.pdf');
    }
}
