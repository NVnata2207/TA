<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $academic_years = AcademicYear::orderByDesc('name')->paginate(10);
        $academicYearEdit = null;
        if ($request->has('edit')) {
            $academicYearEdit = AcademicYear::find($request->edit);
        }
        return view('admin.academic_years.index', compact('academic_years', 'academicYearEdit'));
    }

    public function create()
    {
        return view('admin.academic_years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:academic_years,name',
            'kuota' => 'nullable|integer',
            'mulai_pendaftaran' => 'nullable|date',
            'selesai_pendaftaran' => 'nullable|date',
            'mulai_seleksi' => 'nullable|date',
            'selesai_seleksi' => 'nullable|date',
            'tanggal_pengumuman' => 'nullable|date',
            'mulai_daftar_ulang' => 'nullable|date',
            'selesai_daftar_ulang' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);
        AcademicYear::create([
            'name' => $request->name,
            'kuota' => $request->kuota,
            'mulai_pendaftaran' => $request->mulai_pendaftaran,
            'selesai_pendaftaran' => $request->selesai_pendaftaran,
            'mulai_seleksi' => $request->mulai_seleksi,
            'selesai_seleksi' => $request->selesai_seleksi,
            'tanggal_pengumuman' => $request->tanggal_pengumuman,
            'mulai_daftar_ulang' => $request->mulai_daftar_ulang,
            'selesai_daftar_ulang' => $request->selesai_daftar_ulang,
            'is_active' => $request->is_active ? true : false,
        ]);
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academic_year)
    {
        return view('admin.academic_years.edit', compact('academic_year'));
    }

    public function update(Request $request, AcademicYear $academic_year)
    {
        $request->validate([
            'name' => 'required|string|unique:academic_years,name,' . $academic_year->id,
            'kuota' => 'nullable|integer',
            'mulai_pendaftaran' => 'nullable|date',
            'selesai_pendaftaran' => 'nullable|date',
            'mulai_seleksi' => 'nullable|date',
            'selesai_seleksi' => 'nullable|date',
            'tanggal_pengumuman' => 'nullable|date',
            'mulai_daftar_ulang' => 'nullable|date',
            'selesai_daftar_ulang' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);
        $academic_year->update([
            'name' => $request->name,
            'kuota' => $request->kuota,
            'mulai_pendaftaran' => $request->mulai_pendaftaran,
            'selesai_pendaftaran' => $request->selesai_pendaftaran,
            'mulai_seleksi' => $request->mulai_seleksi,
            'selesai_seleksi' => $request->selesai_seleksi,
            'tanggal_pengumuman' => $request->tanggal_pengumuman,
            'mulai_daftar_ulang' => $request->mulai_daftar_ulang,
            'selesai_daftar_ulang' => $request->selesai_daftar_ulang,
            'is_active' => $request->is_active ? true : false,
        ]);
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil diupdate.');
    }

    public function destroy(AcademicYear $academic_year)
    {
        $academic_year->delete();
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function setActive(AcademicYear $academic_year)
    {
        AcademicYear::query()->update(['is_active' => false]);
        $academic_year->update(['is_active' => true]);
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran aktif berhasil diubah.');
    }

    public function unsetActive(AcademicYear $academic_year)
    {
        $academic_year->update(['is_active' => false]);
        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil dinonaktifkan.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            AcademicYear::whereIn('id', $ids)->delete();
        }
        return redirect()->route('academic_years.index')->with('success', 'Data terpilih berhasil dihapus.');
    }
} 