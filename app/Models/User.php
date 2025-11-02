<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
