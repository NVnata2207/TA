<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    // INI YANG PENTING: MENGIZINKAN KOLOM 'name' DAN 'is_active' UNTUK DIISI
    protected $fillable = [
        'name',
        'is_active',
    ];
}