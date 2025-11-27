<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Dihapus, sesuai kode baru Anda

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable; // HasApiTokens dihapus, ini sudah benar

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nisn',
        'email',
        'password',
        'kode_pendaftaran', // Dari kode baru Anda
        'status_pendaftaran',
        'hasil',
        'daftar_ulang',     // Dari kode baru Anda
        'academic_year_id',
        'role',             // DITAMBAHKAN KEMBALI: Ini penting untuk AuthController Anda
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * (Ini sintaks baru Laravel 10+, sudah benar)
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Fungsi boot() untuk membuat kode_pendaftaran secara otomatis.
     * (Ini dari kode baru Anda, sudah benar)
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            do {
                $kode = 'DFTR-' . strtoupper(substr(md5(uniqid()), 0, 4));
            } while (static::where('kode_pendaftaran', $kode)->exists());
            
            $user->kode_pendaftaran = $kode;
        });
    }

    /**
     * Relasi ke AcademicYear.
     * (Saya gunakan versi yang lebih eksplisit dengan foreign key)
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * DITAMBAHKAN KEMBALI: Relasi ke ExamAnswer.
     * Ini diperlukan oleh logika 'Auto Reject' di userDashboard Anda.
     */
    public function examAnswers()
    {
        return $this->hasMany(ExamAnswer::class, 'user_id');
    }
}

