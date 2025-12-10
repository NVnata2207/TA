<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Aktifkan jika butuh verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// KARENA KITA SUDAH DI NAMESPACE 'App\Models', 
// SEBENARNYA TIDAK PERLU PAKAI 'use App\Models\...' LAGI.
// TAPI KALAU MAU DIPAKAI SUPAYA TIDAK MERAH DI VSCODE, TIDAK APA-APA.
use App\Models\UserDocument; 
use App\Models\ExamAnswer;
use App\Models\AcademicYear;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'kode_pendaftaran',
        'status_pendaftaran',
        'hasil',
        'daftar_ulang',
        'academic_year_id',
        'role',
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
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Relasi ke UserDocument (Berkas yang diupload user)
     */
    public function documents()
    {
        return $this->hasMany(UserDocument::class, 'user_id'); 
    }
    
    // Relasi: Satu User punya Satu Detail Siswa
    public function studentDetail()
    {
        return $this->hasOne(StudentDetail::class);
    }
}