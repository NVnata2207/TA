<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'kuota',
        'mulai_pendaftaran',
        'selesai_pendaftaran',
        'mulai_seleksi',
        'selesai_seleksi',
        'tanggal_pengumuman',
        'mulai_daftar_ulang',
        'selesai_daftar_ulang',
    ];
} 