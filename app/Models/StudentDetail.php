<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi
    protected $guarded = ['id'];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}