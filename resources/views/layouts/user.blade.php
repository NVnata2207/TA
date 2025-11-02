<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('user.exam.*') ? 'active' : '' }}" href="{{ route('user.exam.index') }}">
        <i class="fas fa-file-alt"></i>
        <span>Halaman Ujian</span>
    </a>
</li> 