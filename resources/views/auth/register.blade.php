@extends('layouts.auth')
@section('content')
<div class="row min-vh-100 g-0">
    <div class="col-md-7 d-none d-md-flex align-items-center justify-content-center bg-light p-0" style="overflow:hidden;">
        <img src="/img/banner.jpg" alt="PPDB" class="w-100 h-100" style="object-fit:cover; min-height:100vh; min-width:100%;">
    </div>
    <div class="col-md-5 d-flex align-items-center justify-content-center bg-white">
        <div class="w-100" style="max-width: 350px;">
            <div class="text-center mb-4">
                <h3 class="fw-bold">e-PPDB</h3>
                <div class="text-muted small">version 1.5.3</div>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3" id="nama-field">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name">
                </div>
                <div class="mb-3">
                    <label for="nisn" class="form-label">NISN</label>
                    <input type="text" class="form-control" id="nisn" name="nisn" required placeholder="Masukkan NISN Anda">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Daftar Sebagai</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Buat Pengguna</button>
            </form>
            <div class="mt-3 text-center">
                <a href="{{ route('login') }}">Login Page</a>
            </div>
            <script>
                const roleSelect = document.getElementById('role');
                const namaField = document.getElementById('nama-field');
                const namaInput = document.getElementById('name');
                function toggleNamaField() {
                    if (roleSelect.value === 'admin') {
                        namaField.style.display = 'none';
                        namaInput.required = false;
                    } else {
                        namaField.style.display = 'block';
                        namaInput.required = true;
                    }
                }
                roleSelect.addEventListener('change', toggleNamaField);
                window.addEventListener('DOMContentLoaded', toggleNamaField);
            </script>
        </div>
    </div>
</div>
@endsection 