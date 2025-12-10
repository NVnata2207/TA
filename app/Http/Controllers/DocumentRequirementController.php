<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequirement;

class DocumentRequirementController extends Controller
{
    public function index()
    {
        // Mengambil semua data dokumen yang aktif
        $documents = DocumentRequirement::all();
        return view('admin.documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        
        DocumentRequirement::create([
            'name' => $request->name,
            'is_active' => 1
        ]);

        return back()->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $doc = DocumentRequirement::findOrFail($id);
        $doc->delete();
        
        return back()->with('success', 'Dokumen dihapus.');
    }
}