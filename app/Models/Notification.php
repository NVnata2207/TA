<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    
    // HAPUS BARIS $guarded, GANTI DENGAN INI:
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'read',
        'link', // <--- Ini kolom baru yang kita butuhkan
    ];

    public $timestamps = true;
}