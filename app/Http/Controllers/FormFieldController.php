<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FormField;

class FormFieldController extends Controller
{
    public function index()
    {
        $fields = FormField::orderBy('id')->get();
        $fieldsByCategory = $fields->groupBy('category');
        $categories = $fieldsByCategory->sortBy(function($group) {
            return $group->first()->id;
        })->keys();
        return view('admin.form_fields.index', compact('fieldsByCategory', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:form_fields',
            'category' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);
        FormField::create($request->only('label', 'name', 'category', 'order'));
        return back()->with('success', 'Field berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $field = FormField::findOrFail($id);
        $request->validate([
            'label' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);
        $field->update($request->only('label', 'category', 'order'));
        return back()->with('success', 'Field berhasil diupdate.');
    }

    public function destroy($id)
    {
        $field = FormField::findOrFail($id);
        $field->delete();
        return back()->with('success', 'Field berhasil dihapus.');
    }

    // CRUD kategori (group)
    public function storeCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255|unique:form_fields,category',
        ]);
        // Tambahkan field dummy untuk kategori baru
        FormField::create([
            'label' => 'Field Dummy',
            'name' => 'dummy_'.uniqid(),
            'category' => $request->category,
            'order' => 0,
            'is_active' => false,
        ]);
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $oldCategory)
    {
        $request->validate([
            'category' => 'required|string|max:255|unique:form_fields,category',
        ]);
        FormField::where('category', $oldCategory)->update(['category' => $request->category]);
        return back()->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroyCategory($category)
    {
        FormField::where('category', $category)->delete();
        return back()->with('success', 'Kategori dan seluruh field di dalamnya berhasil dihapus.');
    }

    public function updateSettings(Request $request)
    {
        $activeFields = $request->input('fields', []);
        foreach (FormField::all() as $field) {
            $field->is_active = in_array($field->id, $activeFields);
            $field->save();
        }
        // Simpan tahun pelajaran jika ada
        if ($request->has('tahun_pelajaran')) {
            // Simpan ke config/setting, bisa pakai DB atau file
            DB::table('settings')->updateOrInsert([
                'key' => 'tahun_pelajaran'
            ], [
                'value' => $request->tahun_pelajaran
            ]);
        }
        return redirect()->back()->with('success', 'Pengaturan formulir berhasil disimpan.');
    }

    // Tambah field ke kategori tertentu
    public function storeField(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:form_fields',
            'category' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        FormField::create([
            'label' => $request->label,
            'name' => $request->name,
            'category' => $request->category,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);
        return back()->with('success', 'Field berhasil ditambahkan ke kategori.');
    }

    public function destroyField($id)
    {
        $field = FormField::findOrFail($id);
        $field->delete();
        return back()->with('success', 'Field berhasil dihapus.');
    }
} 