<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'e-PPDB Admin')</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <style>
        html, body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif !important;
        }
        .brand-link { background: #90C67C !important; color: #fff !important; }
        .main-header { background: #90C67C !important; color: #fff !important; }
        .sidebar-dark-primary { background: #328E6E !important; }
        .user-panel .image img { object-fit: cover; }
        .content-header { padding: 1rem 1.5rem 0.5rem 1.5rem; }
        .content-wrapper { background: #f4f6f9; }
        .nav-sidebar .nav-link.active, .nav-sidebar .nav-link:hover {
            background: #90C67C !important;
            color: #fff !important;
            transition: background 0.2s, color 0.2s;
        }
        .btn, .btn-sm, .btn-primary, .btn-success, .btn-danger, .btn-warning, .btn-info {
            transition: box-shadow 0.2s, background 0.2s;
        }
        .btn:hover, .btn-sm:hover {
            box-shadow: 0 2px 8px rgba(108,99,255,0.15);
        }
        @media (max-width: 767.98px) {
            .main-sidebar { position: fixed; z-index: 1050; }
            .content-wrapper { margin-left: 0 !important; }
            .sidebar { padding-bottom: 60px; }
        }
    </style>
    {{-- Ini akan memuat style khusus dari halaman 'Data Peserta' --}}
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav ml-auto w-100 justify-content-end align-items-center">
            {{-- BAGIAN NOTIFIKASI (VERSI CUSTOM DB - AMAN DARI ERROR) --}}
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="far fa-bell fa-lg"></i>   
                        @php
                            // PERBAIKAN: Gunakan Query Manual agar sesuai tabel Anda
                            // Mencari notifikasi milik user yang 'read' = 0 (Belum dibaca)
                            $unreadNotif = \App\Models\Notification::where('user_id', auth()->id())
                                                ->where('read', 0) // Pastikan kolom di DB namanya 'read' atau 'is_read'
                                                ->latest()
                                                ->get();
                        @endphp
                        
                        @if($unreadNotif->count() > 0)
                            <span class="badge badge-danger navbar-badge">{{ $unreadNotif->count() }}</span>
                        @endif
                    </a>

                    {{-- Dropdown Menu --}}
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="alertsDropdown" style="width: 350px; border-radius: 12px; overflow: hidden;">
                        
                        {{-- 1. HEADER HIJAU --}}
                        <li class=" text-white p-3 d-flex justify-content-between align-items-center" style="background: #328E6E !important;">
                            <div>
                                <h6 class="m-0 fw-bold">Notifikasi</h6>
                            </div>
                            {{-- Tombol Tandai Semua --}}
                            <a href="{{ route('notification.readAll') }}" class="badge bg-white text-success rounded-pill text-decoration-none" style="cursor: pointer;">
                                Tandai Semua Dibaca
                            </a>
                        </li>

                        {{-- 2. LIST NOTIFIKASI --}}
                        <div style="max-height: 400px; overflow-y: auto;">
                            @forelse($unreadNotif as $notif)
                                <li>
                                    {{-- Link ke Route Read agar status berubah jadi 1 --}}
                                    <a class="dropdown-item d-flex align-items-start p-3 border-bottom" href="{{ route('notification.read', $notif->id) }}" style="white-space: normal; transition: background 0.2s;">
                                        
                                        {{-- Ikon --}}
                                        <div class="flex-shrink-0 me-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #e8f5e9;">
                                                <i class="fas fa-info-circle text-success fa-lg"></i>
                                            </div>
                                        </div>

                                        {{-- Konten --}}
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">Info Sistem</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                            </div>
                                            
                                            {{-- PERBAIKAN: Panggil langsung kolom 'message' --}}
                                            <p class="mb-0 text-secondary text-wrap" style="font-size: 0.85rem; line-height: 1.4;">
                                                {{ $notif->message }}
                                            </p>
                                            <small class="text-primary" style="font-size: 0.7rem;">Klik untuk baca</small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="p-4 text-center text-muted">
                                    <i class="far fa-bell-slash fa-3x mb-3 text-light" style="color: #ccc !important;"></i><br>
                                    <small>Tidak ada notifikasi baru</small>
                                </li>
                            @endforelse
                        </div>
                    </ul>
                </li>
            <li class="nav-item d-flex align-items-center">
                <span class="mr-2">{{ auth()->user()->name ?: 'Admin utama' }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger p-0" title="Logout"><i class="fas fa-sign-out-alt"></i></button>
                </form>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link text-center">
            <span class="brand-text font-weight-light">E-PPDB</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?: 'Admin utama') }}&background=90C67C&color=fff" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ auth()->user()->name ?: 'Admin utama' }}</a>
                    {{-- SAYA PERBAIKI TYPO DI SINI --}}
                    <span class="small" style="color: #90C67C;">
                        <i class="fas fa-circle"></i> Online
                    </span>
                </div>
            </div>
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    @if(auth()->user() && auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('dashboard.admin') }}" class="nav-link {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        {{-- ITEM 1: MASTER FIELD FORMULIR --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.form_fields.index') }}" class="nav-link {{ request()->routeIs('admin.form_fields.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list-alt"></i>
                                <p>Master Field Formulir</p>
                            </a>
                        </li> {{-- <--- PASTIKAN INI DITUTUP DULU --}}

                        {{-- ITEM 2: PENGATURAN DOKUMEN UPLOAD (MENU BARU) --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.documents.index') }}" class="nav-link {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-upload"></i>
                                <p>Master Dokumen</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('academic_years.index') }}" class="nav-link {{ request()->routeIs('academic_years.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Tahun Ajaran</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Data Peserta</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('announcements.index') }}" class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-bullhorn"></i>
                                <p>Pengumuman</p>
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('dashboard.user') }}" class="nav-link {{ request()->routeIs('dashboard.user') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
    <!---------------------------- USER MENU ------------------------>

                        @if(auth()->user()->role === 'user')

                            {{-- 1. ISI FORMULIR (LINK SUDAH BENAR) --}}
                            <li class="nav-item">
                                <a href="{{ route('user.form') }}" class="nav-link {{ request()->routeIs('user.form') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-edit"></i>
                                    <p>Isi Formulir Disini</p>
                                </a>
                            </li>

                            {{-- 2. UPLOAD BERKAS --}}
                            <li class="nav-item">
                                <a href="{{ route('user.documents.index') }}" class="nav-link {{ request()->routeIs('user.documents.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-upload"></i>
                                    <p>Upload Berkas</p>
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                {{-- Ini akan memuat judul dari 'Data Peserta' --}}
                <h1 class="m-0 pb-2 border-bottom">@yield('page_title', 'Dashboard')</h1>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                {{-- Ini adalah tempat 'Data Peserta' akan disisipkan --}}
                @yield('content')
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

{{-- Ini akan memuat script khusus dari halaman 'Data Peserta' --}}
@stack('scripts')
</body>
</html>