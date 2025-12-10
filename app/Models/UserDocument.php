<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $table = 'user_documents';
    
    // GANTI $guarded = []; MENJADI SEPERTI INI:
    protected $fillable = [
        'user_id',
        'requirement_id', // <--- INI PENTING (Kolom Baru)
        'type',
        'file_path',
    ];

    public $timestamps = true;
}