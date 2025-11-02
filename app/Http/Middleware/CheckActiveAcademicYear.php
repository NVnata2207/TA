<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AcademicYear;

class CheckActiveAcademicYear
{
    public function handle(Request $request, Closure $next)
    {
        $hasActiveYear = AcademicYear::where('is_active', true)->exists();
        
        if (!$hasActiveYear) {
            return redirect()->route('login')->with('error', 'Pendaftaran ditutup karena tidak ada tahun ajaran yang aktif.');
        }

        return $next($request);
    }
} 