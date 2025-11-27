<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        if (auth()->check()) {
            if (auth()->user()->role === 'admin') {
                return redirect()->route('dashboard.admin');
            } else {
                return redirect()->route('dashboard.user');
            }
        }
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return redirect()->route('login')->with('error', 'Pendaftaran ditutup karena tidak ada tahun ajaran yang aktif.');
        }
        if ($activeYear->kuota !== null && $activeYear->kuota <= 0) {
            return redirect()->route('login')->with('error', 'Kuota pendaftaran sudah habis.');
        }
        return view('auth.register');
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
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $academic_year_id = $activeYear ? $activeYear->id : null;

        if ($activeYear && $activeYear->kuota !== null && $activeYear->kuota <= 0) {
            return redirect()->route('login')->with('error', 'Kuota pendaftaran sudah habis.');
        }

        // LOGIKA ADMIN (Jarang dipakai registrasi publik, tapi kita perbaiki juga)
        if ($request->role === 'admin') {
            $request->validate([
                'nisn' => 'required|string|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|in:admin,user',
                 // Admin biasanya tidak wajib email di awal, tapi kalau mau diisi boleh
            ]);
            $user = User::create([
                'name' => 'Administrator', // Kasih nama default biar tidak error
                'nisn' => $request->nisn,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'email' => $request->email ?? null, // <--- UBAH INI (Ambil dari request, atau null)
                'academic_year_id' => $academic_year_id,
                'status_pendaftaran' => 'Pendaftar Baru',
            ]);

        // LOGIKA USER / PENDAFTAR SISWA (INI YANG PENTING)
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users', // <--- TAMBAHKAN VALIDASI INI
                'nisn' => 'required|string|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|in:admin,user',
            ]);
            
            $user = User::create([
                'name' => $request->name,
                'nisn' => $request->nisn,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'email' => $request->email, // <--- UBAH INI (Jangan dikosongkan lagi!)
                'academic_year_id' => $academic_year_id,
                'status_pendaftaran' => 'Pendaftar Baru',
            ]);
        }

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

    public function adminDashboard()
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard.user')->with('error', 'Akses ditolak.');
        }
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        return view('dashboard.admin', compact('activeYear'));
    }

    public function userDashboard()
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
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
            $soalCount = \App\Models\ExamQuestion::where('academic_year_id', $activeYear->id)->count();
            $jawabCount = \App\Models\ExamAnswer::where('user_id', $user->id)->whereIn('exam_question_id', \App\Models\ExamQuestion::where('academic_year_id', $activeYear->id)->pluck('id'))->count();
            if ($soalCount > 0 && $jawabCount == 0 && $user->hasil !== 'Tidak Diterima') {
                $user->hasil = 'Tidak Diterima';
                $user->save();
            }
        }
        return view('dashboard.user', compact('activeYear', 'user'));
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

// ... di dalam method controller Anda (misalnya: index)

    public function index(Request $request)
    {
    // 1. Ambil kata kunci pencarian dari request
    // 'search' adalah nama input di form Anda
    $kataKunci = $request->input('search');

    // 2. Mulai query ke database
    $query = User::query();

    // 3. Jika ada kata kunci, tambahkan filter 'where'
    if ($kataKunci) {
        // 'LIKE' digunakan untuk mencari sebagian kata
        // '%' adalah wildcard, artinya "apapun"
        // "%winata%" berarti mencari apapun yang mengandung "winata"
        $query->where('name', 'LIKE', '%' . $kataKunci . '%');
    }

    // 4. Ambil datanya (misal: 10 per halaman)
    $users = $query->paginate(10);

    // 5. Kirim data ke view
    return view('admin.users.index', [
        'users' => $users,
        'kataKunci' => $kataKunci // Untuk menampilkan kembali di kotak search
    ]);
}








} 
