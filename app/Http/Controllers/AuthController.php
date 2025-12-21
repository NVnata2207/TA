<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AcademicYear; // Pastikan ini ada di atas

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except(['adminDashboard', 'userDashboard', 'logout']);
        $this->middleware('auth')->only(['adminDashboard', 'userDashboard', 'logout']);
    }

    public function showLoginForm()
    {
        if (auth()->check()) {
            if (auth()->user()->role === 'admin') {
                return redirect()->route('dashboard.admin');
            } else {
                return redirect()->route('dashboard.user');
            }
        }
        return view('auth.login');
    }

public function showRegisterForm()
    {
        // 1. Cek apakah user sudah login (KODINGAN LAMA - BIARKAN)
        if (auth()->check()) {
            if (auth()->user()->role === 'admin') {
                return redirect()->route('dashboard.admin');
            } else {
                return redirect()->route('dashboard.user');
            }
        }

        // 2. Cek Tahun Ajaran Aktif (KODINGAN LAMA - BIARKAN)
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            return redirect()->route('login')->with('error', 'Pendaftaran ditutup karena tidak ada tahun ajaran yang aktif.');
        }
        
        if ($activeYear->kuota !== null && $activeYear->kuota <= 0) {
            return redirect()->route('login')->with('error', 'Kuota pendaftaran sudah habis.');
        }

        // ============================================================
        // 3. TAMBAHAN BARU: LOGIKA PEMBATASAN ADMIN
        // ============================================================
        
        // Hitung berapa admin yang sudah ada
        $jumlahAdmin = \App\Models\User::where('role', 'admin')->count();
        
        // Cek apakah masih boleh daftar admin? (Jika kurang dari 2, boleh. Jika sudah 2, tidak boleh)
        $bisaDaftarAdmin = ($jumlahAdmin < 2);

        // ============================================================

        // 4. Update Return View (Kirim variabel $bisaDaftarAdmin ke tampilan)
        return view('auth.register', compact('bisaDaftarAdmin'));
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nisn', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('dashboard.admin');
            } else {
                return redirect()->route('dashboard.user');
            }
        }
        return back()->withErrors(['nisn' => 'NISN atau password salah.']);
    }

    public function register(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $academic_year_id = $activeYear ? $activeYear->id : null;

        if ($activeYear && $activeYear->kuota !== null && $activeYear->kuota <= 0) {
            return redirect()->route('login')->with('error', 'Kuota pendaftaran sudah habis.');
        }

        // === 1. LOGIKA ADMIN ===
        if ($request->role === 'admin') {
            $request->validate([
                'nisn' => 'required|string|max:20|unique:users', // Tambah max:20
                'password' => 'required|string|min:8|confirmed', // Ubah jadi min:8 biar lebih aman
                'role' => 'required|in:admin,user',
            ]);

            $user = User::create([
                'name' => 'Administrator',
                'nisn' => $request->nisn,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'email' => $request->email ?? null,
                'academic_year_id' => $academic_year_id,
                'status_pendaftaran' => 'Pendaftar Baru',
            ]);

        // === 2. LOGIKA USER (CALON SISWA) ===
        } else {
            // TERAPKAN VALIDASI KETAT DI SINI
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users',
                'nisn' => 'required|string|max:20|unique:users', // Wajib max 20 karakter
                'password' => 'required|string|min:8|confirmed',  // Wajib minimal 8 karakter
                'role' => 'required|in:admin,user',
                'jenjang_tambahan' => 'required|string',          // Wajib dipilih (SD/SMP)
            ]);
            
            // TRIK GABUNGKAN NAMA (Agar masuk database tanpa error kolom baru)
            // Jika Anda belum membuat kolom 'jenjang_tambahan' di database, pakai cara ini:
            $namaGabungan = $request->name . ' (' . $request->jenjang_tambahan . ')';

            // Simpan Data Siswa
            $user = User::create([
                'name' => $namaGabungan, // Nama disimpan sebagai "Budi (SD)"
                'nisn' => $request->nisn,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'email' => $request->email,
                'academic_year_id' => $academic_year_id,
                'status_pendaftaran' => 'Pendaftar Baru',
                // 'jenjang_tambahan' => $request->jenjang_tambahan, <--- Hapus komentar ini HANYA JIKA Anda sudah buat kolomnya di database
            ]);
        }

        // Kurangi Kuota
        if ($activeYear && $activeYear->kuota !== null && $activeYear->kuota > 0) {
            $activeYear->decrement('kuota');
        }

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect()->route('dashboard.admin');
        } else {
            return redirect()->route('dashboard.user');
        }
    }

    /**
     * Dashboard Admin (Hitung Statistik)
     */
    public function adminDashboard()
    {
        // 1. AMBIL TAHUN AJARAN AKTIF
        $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();

        // 2. HITUNG STATISTIK UTAMA
        $totalPendaftar = \App\Models\User::where('role', 'user')->count();

        $pendaftarBaru = \App\Models\User::where('role', 'user')
                             ->where('status_pendaftaran', 'Pendaftar Baru')
                             ->count();

        // Menghitung siswa yang SUDAH DITERIMA (Lulus Seleksi)
        $sudahVerifikasi = \App\Models\User::where('role', 'user')
                               ->where('hasil', 'Di Terima')
                               ->count();

        // PERBAIKAN: Menghitung siswa yang STATUSNYA BERKAS KURANG / TIDAK SESUAI
        // (Bukan menghitung yang ditolak/gagal seleksi)
        $berkasKurang = \App\Models\User::where('role', 'user')
                            ->whereIn('status_pendaftaran', ['Berkas Kurang', 'Berkas Tidak Sesuai'])
                            ->count();

        // 3. HITUNG JALUR (Set 0)
        $zonasi = 0; 
        $prestasi = 0;
        $afirmasi = 0;
        $perpindahan = 0;

        // 4. KIRIM SEMUA DATA KE VIEW
        return view('dashboard.admin', compact(
            'activeYear',
            'totalPendaftar', 
            'pendaftarBaru', 
            'sudahVerifikasi', 
            'berkasKurang',
            'zonasi', 'prestasi', 'afirmasi', 'perpindahan'
        ));
    }

    /**
     * Dashboard User
     */
    public function userDashboard()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $user = auth()->user();
        
        // Auto reject jika status belum diverifikasi hingga selesai pendaftaran
        if ($user->status_pendaftaran !== 'Sudah Diverifikasi' && $activeYear && now()->gt(\Carbon\Carbon::parse($activeYear->selesai_pendaftaran))) {
            if ($user->hasil !== 'Tidak Diterima') {
                $user->hasil = 'Tidak Diterima';
                $user->save();
            }
        }      
        // Auto reject jika sudah diverifikasi tapi tidak mengerjakan ujian hingga selesai seleksi
        if ($user->status_pendaftaran === 'Sudah Diverifikasi' && $activeYear && now()->gt(\Carbon\Carbon::parse($activeYear->selesai_seleksi))) {
        }
        return view('dashboard.user', compact('activeYear', 'user'));
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
}