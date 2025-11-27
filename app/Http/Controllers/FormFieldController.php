<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Mengimpor DB Facade untuk akses query builder manual (tabel settings)
use App\Models\FormField; // Mengimpor Model FormField

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
     * Fungsi updateSettings menyimpan pengaturan field mana yang aktif/tidak,
     * serta menyimpan setting global seperti 'tahun_pelajaran'.
     */
    public function updateSettings(Request $request)
    {
        // Ambil array ID field yang dicentang (aktif). Jika kosong, array kosong.
        $activeFields = $request->input('fields', []);

        // Loop semua field di database.
        foreach (FormField::all() as $field) {
            // Cek apakah ID field ini ada di daftar $activeFields.
            // Jika ada, is_active = true. Jika tidak, is_active = false.
            $field->is_active = in_array($field->id, $activeFields);
            $field->save();
        }

        // Simpan setting 'tahun_pelajaran' ke tabel 'settings'.
        if ($request->has('tahun_pelajaran')) {
            // updateOrInsert: Jika key 'tahun_pelajaran' ada -> update value-nya.
            // Jika tidak ada -> buat baris baru.
            DB::table('settings')->updateOrInsert([
                'key' => 'tahun_pelajaran' // Kondisi pencarian
            ], [
                'value' => $request->tahun_pelajaran // Data yang disimpan
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan formulir berhasil disimpan.');
    }

    // --- BAGIAN TAMBAH FIELD SPESIFIK KE KATEGORI ---

    /**
     * Fungsi storeField mirip dengan store(), tapi biasanya dipanggil dari modal
     * "Tambah Field di Kategori X", sehingga logic-nya sedikit lebih spesifik.
     */
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
            'order' => $request->order ?? 0, // Jika order kosong, set 0.
            'is_active' => $request->has('is_active'), // Cek checkbox is_active.
        ]);

        return back()->with('success', 'Field berhasil ditambahkan ke kategori.');
    }

    // Duplikasi fungsi destroy untuk endpoint yang berbeda (mungkin untuk routing yang lebih spesifik).
    public function destroyField($id)
    {
        $field = FormField::findOrFail($id);
        $field->delete();
        return back()->with('success', 'Field berhasil dihapus.');
    }
}