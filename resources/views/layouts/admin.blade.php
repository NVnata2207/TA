<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-PPDB Admin</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <style>
        html, body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif !important;
        }
        .brand-link { background: #6c63ff !important; color: #fff !important; }
        .main-header { background: #6c63ff !important; color: #fff !important; }
        .sidebar-dark-primary { background: #22223b !important; }
        .user-panel .image img { object-fit: cover; }
        .content-header { padding: 1rem 1.5rem 0.5rem 1.5rem; }
        .content-wrapper { background: #f4f6f9; }
        .nav-sidebar .nav-link.active, .nav-sidebar .nav-link:hover {
            background: #6c63ff !important;
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
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav ml-auto w-100 justify-content-end align-items-center">
            <li class="nav-item dropdown">
                @php
                $notif = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('type', auth()->user()->role)
                    ->where('read', false)
                    ->latest()->take(10)->get();
                @endphp
                <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    @if($notif->count())
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $notif->count() }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notifDropdown" style="min-width:340px;max-width:400px;">
                    <li class="dropdown-header fw-bold">Notifikasi @if($notif->count())<span class="badge bg-success ms-2">{{ $notif->count() }}</span>@endif</li>
                    @forelse($notif as $n)
                        <li>
                            @if(auth()->user()->role === 'admin' && preg_match('/mengupload|mengganti berkas/i', $n->message, $m))
                                @php
                                    $user = \App\Models\User::where('name', explode(' ', $n->message)[0])->first();
                                @endphp
                                @if($user)
                                    <a href="{{ route('users.show', $user->id) }}?notif={{ $n->id }}" class="dropdown-item text-dark notif-link">
                                        <i class="fa fa-user me-2"></i> {{ $n->message }}
                                    </a>
                                @else
                                    <span class="dropdown-item text-dark">{{ $n->message }}</span>
                                @endif
                            @else
                                <span class="dropdown-item text-dark">{{ $n->message }}</span>
                            @endif
                        </li>
                    @empty
                        <li><span class="dropdown-item text-muted">Tidak ada notifikasi baru</span></li>
                    @endforelse
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
            <span class="brand-text font-weight-light">e-PPDB</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?: 'Admin utama') }}&background=6c63ff&color=fff" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ auth()->user()->name ?: 'Admin utama' }}</a>
                    <span class="text-success small"><i class="fas fa-circle"></i> Online</span>
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
                        <li class="nav-item">
                            <a href="{{ route('form_fields.index') }}" class="nav-link {{ request()->routeIs('form_fields.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list-alt"></i>
                                <p>Master Field Formulir</p>
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
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.exam-questions.*') ? 'active' : '' }}" href="{{ route('admin.exam-questions.index') }}">
                                <i class="fas fa-file-alt"></i>
                                <span>Manajemen Soal Ujian</span>
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('dashboard.user') }}" class="nav-link {{ request()->routeIs('dashboard.user') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @if(auth()->user()->role === 'user')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.documents.index') }}">
                                <i class="fa fa-upload"></i> Upload Berkas
                            </a>
                        </li>
                        <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('user.exam.*') ? 'active' : '' }}" href="{{ route('user.exam.index') }}">
        <i class="fas fa-file-alt"></i>
        <span>Halaman Ujian</span>
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
                <h1 class="m-0 pb-2 border-bottom">@yield('page_title', 'Dashboard')</h1>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer text-center small">
        <strong>Copyright &copy; 2025 Adrian Project.</strong> All rights reserved. <span class="float-right">Version 1.5.3 Raya</span>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html> 