<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = [
        'academic_year_id',
        'title',
        'file_path',
        'description'
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }
} 