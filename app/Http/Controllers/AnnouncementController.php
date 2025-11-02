<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::latest()->paginate(10);
        $editAnnouncement = null;
        if ($request->has('edit')) {
            $editAnnouncement = Announcement::find($request->edit);
        }
        return view('admin.announcements.index', compact('announcements', 'editAnnouncement'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:pembukaan,masa_pendaftaran,ditutup,biasa',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'show_on_login' => 'nullable|boolean',
        ];
        // Validasi tanggal dinamis
        if ($request->type == 'pembukaan') {
            $rules['tanggal_pembukaan'] = 'required|date';
        }
        if ($request->type == 'masa_pendaftaran') {
            $rules['tanggal_mulai_pendaftaran'] = 'required|date';
            $rules['tanggal_selesai_pendaftaran'] = 'required|date|after_or_equal:tanggal_mulai_pendaftaran';
        }
        if ($request->type == 'ditutup') {
            $rules['tanggal_pengumuman'] = 'required|date';
            $rules['tanggal_mulai_daftar_ulang'] = 'required|date';
            $rules['tanggal_selesai_daftar_ulang'] = 'required|date|after_or_equal:tanggal_mulai_daftar_ulang';
        }
        $data = $request->validate($rules);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }
        $data['show_on_login'] = $request->has('show_on_login');
        if (!isset($data['academic_year_id'])) {
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $data['academic_year_id'] = $activeYear ? $activeYear->id : null;
        }
        Announcement::create($data);
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Announcement $announcement)
    {
        return redirect()->route('announcements.index', ['edit' => $announcement->id]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:pembukaan,masa_pendaftaran,ditutup,biasa',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'show_on_login' => 'nullable|boolean',
        ];
        if ($request->type == 'pembukaan') {
            $rules['tanggal_pembukaan'] = 'required|date';
        }
        if ($request->type == 'masa_pendaftaran') {
            $rules['tanggal_mulai_pendaftaran'] = 'required|date';
            $rules['tanggal_selesai_pendaftaran'] = 'required|date|after_or_equal:tanggal_mulai_pendaftaran';
        }
        if ($request->type == 'ditutup') {
            $rules['tanggal_pengumuman'] = 'required|date';
            $rules['tanggal_mulai_daftar_ulang'] = 'required|date';
            $rules['tanggal_selesai_daftar_ulang'] = 'required|date|after_or_equal:tanggal_mulai_daftar_ulang';
        }
        $data = $request->validate($rules);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }
        $data['show_on_login'] = $request->has('show_on_login');
        if (!isset($data['academic_year_id'])) {
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $data['academic_year_id'] = $activeYear ? $activeYear->id : null;
        }
        $announcement->update($data);
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diupdate.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function toggleShowOnLogin(Announcement $announcement)
    {
        $announcement->show_on_login = !$announcement->show_on_login;
        $announcement->save();
        return redirect()->route('announcements.index')->with('success', 'Status tampil di login berhasil diubah.');
    }
} 