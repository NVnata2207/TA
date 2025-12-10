<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Mengimpor DB Facade untuk akses query builder manual (tabel settings)
use App\Models\FormField; // Mengimpor Model FormField
use Illuminate\Support\Str;

class FormFieldController extends Controller
{
    /**
     * Fungsi index menampilkan daftar field yang sudah dikelompokkan per kategori.
     */
    public function index()
    {
        // Mengambil semua data field urut berdasarkan ID.
        $fields = FormField::orderBy('id')->get();

        // Mengelompokkan data field berdasarkan kolom 'category'.
        // Hasilnya berupa Collection: ['Data Diri' => [field1, field2], 'Orang Tua' => [field3...]]
        $fieldsByCategory = $fields->groupBy('category');

        // Mengurutkan nama kategori (keys) berdasarkan ID field pertama di dalam grup tersebut.
        // Tujuannya agar urutan kategori konsisten sesuai urutan pembuatan field di dalamnya.
        $categories = $fieldsByCategory->sortBy(function($group) {
            return $group->first()->id;
        })->keys(); // Mengambil hanya nama kategorinya saja.

        // Mengirim data ke view.
        return view('admin.form_fields.index', compact('fieldsByCategory', 'categories'));
    }

    /**
     * Fungsi store menyimpan field baru (General).
     */
    public function store(Request $request)
    {
        // Validasi input. 'name' (attribute name html) harus unik.
        $request->validate([
            'label' => 'required|string|max:255', // Label yang tampil di layar
            'name' => 'required|string|max:255|unique:form_fields', // Nama teknis (input name="")
            'category' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        // Simpan hanya kolom yang diizinkan.
        FormField::create($request->only('label', 'name', 'category', 'order'));

        return back()->with('success', 'Field berhasil ditambahkan.');
    }

    /**
     * Fungsi update memperbarui data field.
     * Catatan: Kolom 'name' biasanya tidak diupdate untuk mencegah error pada data siswa yang sudah masuk.
     */
    public function update(Request $request, $id)
    {
        $field = FormField::findOrFail($id);
        
        $request->validate([
            'label' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        // Update label, kategori, dan urutan saja.
        $field->update($request->only('label', 'category', 'order'));

        return back()->with('success', 'Field berhasil diupdate.');
    }

    // Menghapus field berdasarkan ID.
    public function destroy($id)
    {
        $field = FormField::findOrFail($id);
        $field->delete();
        return back()->with('success', 'Field berhasil dihapus.');
    }

    // --- BAGIAN PENGELOLAAN KATEGORI (GROUP) ---

    /**
     * Fungsi storeCategory membuat kategori baru.
     * KARENA tidak ada tabel 'categories', cara membuat kategori adalah dengan
     * membuat satu field "Dummy" (palsu/sembunyi) yang memiliki nama kategori tersebut.
     */
    public function storeCategory(Request $request)
    {
        // Validasi nama kategori harus unik di kolom category.
        $request->validate([
            'category' => 'required|string|max:255|unique:form_fields,category',
        ]);

        // 2. Membuat field dummy.
        FormField::create([
            'label' => 'Field Dummy', // Label asal.
            'name' => 'dummy_'.uniqid(), // Nama teknis acak agar tidak bentrok.
            'category' => $request->category, // INI YANG PENTING: Menyimpan nama kategori baru.
            'order' => 0,
            'is_active' => false, // Field ini disembunyikan (tidak aktif).
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Fungsi updateCategory mengubah nama kategori.
     * Ini akan mengubah kolom 'category' pada SEMUA field yang memiliki nama kategori lama.
     */
    public function updateCategory(Request $request, $oldCategory)
    {
        $request->validate([
            'category' => 'required|string|max:255|unique:form_fields,category',
        ]);

        // Bulk Update: Update semua baris yang kategorinya == $oldCategory menjadi $request->category.
        FormField::where('category', $oldCategory)->update(['category' => $request->category]);

        return back()->with('success', 'Kategori berhasil diupdate.');
    }

    /**
     * Fungsi destroyCategory menghapus kategori BESERTA ISINYA.
     */
    public function destroyCategory($category)
    {
        // Hapus semua field yang memiliki kategori tersebut.
        FormField::where('category', $category)->delete();

        return back()->with('success', 'Kategori dan seluruh field di dalamnya berhasil dihapus.');
    }
/**
     * Fungsi untuk menyimpan checklist pengaturan formulir
     * WAJIB BERNAMA: updateAll (sesuai route admin.form.settings)
     */
    public function updateAll(Request $request)
    {
        // 1. Ambil array ID field yang dicentang
        $checkedFieldIds = $request->input('fields', []);

        // 2. Matikan semua field dulu (set is_active = 0)
        \App\Models\FormField::query()->update(['is_active' => 0]);

        // 3. Nyalakan field yang dipilih (set is_active = 1)
        if (!empty($checkedFieldIds)) {
            \App\Models\FormField::whereIn('id', $checkedFieldIds)->update(['is_active' => 1]);
        }

        return redirect()->back()->with('success', 'Pengaturan formulir berhasil disimpan!');
    }

    // --- BAGIAN TAMBAH FIELD SPESIFIK KE KATEGORI ---

    /**
     * Fungsi storeField mirip dengan store(), tapi biasanya dipanggil dari modal
     * "Tambah Field di Kategori X", sehingga logic-nya sedikit lebih spesifik.
     */
    // Pastikan baris ini ada di paling atas file (di bawah namespace)
    public function storeField(Request $request)
    {
        // 1. BUAT NAME OTOMATIS + ANGKA ACAK (Biar Unik)
        $autoName = \Illuminate\Support\Str::slug($request->label, '_') . '_' . rand(100, 999);
        $request->merge(['name' => $autoName]);

        // 2. VALIDASI
        $request->validate([
            'label'    => 'required|string|max:255',
            'name'     => 'required|string|max:255|unique:form_fields,name',
            'category' => 'required',
            'order'    => 'nullable|integer',
        ]);

        // 3. SIMPAN KE DATABASE
        \App\Models\FormField::create([
            'category'  => $request->category,
            'label'     => $request->label,
            'name'      => $request->name,
            'order'     => $request->order ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
            // 'type' => 'text'  <-- BARIS INI SAYA HAPUS KARENA BIKIN ERROR
        ]);

        return back()->with('success', 'Field berhasil ditambahkan.');
    }

    // Duplikasi fungsi destroy untuk endpoint yang berbeda (mungkin untuk routing yang lebih spesifik).
    public function destroyField($id)
    {
        $field = FormField::findOrFail($id);
        $field->delete();
        return back()->with('success', 'Field berhasil dihapus.');
    }
    /**
     * FUNGSI BARU: Untuk Mengedit Field (Label & Urutan)
     */
    public function updateField(Request $request, $id)
    {
        $request->validate([
            'label' => 'required',
            'order' => 'integer',
        ]);

        $field = FormField::findOrFail($id);
        
        $field->update([
            'label'     => $request->label,
            'order'     => $request->order,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Field berhasil diperbarui.');
    }
}