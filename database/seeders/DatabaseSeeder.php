<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $fields = [
            // Data Registrasi
            ['label' => 'Tahun Pelajaran', 'name' => 'tahun_pelajaran', 'category' => 'Data Registrasi', 'is_active' => true, 'order' => 1],
            ['label' => 'Jalur Pendaftaran', 'name' => 'jalur_pendaftaran', 'category' => 'Data Registrasi', 'is_active' => true, 'order' => 2],
            ['label' => 'No Peserta UN', 'name' => 'no_peserta_un', 'category' => 'Data Registrasi', 'is_active' => true, 'order' => 3],
            ['label' => 'No Seri Ijazah', 'name' => 'no_seri_ijazah', 'category' => 'Data Registrasi', 'is_active' => true, 'order' => 4],
            ['label' => 'No Seri SKHU', 'name' => 'no_seri_skhu', 'category' => 'Data Registrasi', 'is_active' => true, 'order' => 5],
            ['label' => 'Jurusan', 'name' => 'jurusan', 'category' => 'Data Registrasi', 'is_active' => true, 'order' => 6],
            // Data Pribadi
            ['label' => 'Nama Peserta', 'name' => 'nama_peserta', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 1],
            ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 2],
            ['label' => 'NISN', 'name' => 'nisn', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 3],
            ['label' => 'NIK', 'name' => 'nik', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 4],
            ['label' => 'Tempat Lahir', 'name' => 'tempat_lahir', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 5],
            ['label' => 'Tanggal Lahir', 'name' => 'tanggal_lahir', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 6],
            ['label' => 'No Registrasi Akta Lahir', 'name' => 'no_registrasi_akta_lahir', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 7],
            ['label' => 'Agama', 'name' => 'agama', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 8],
            ['label' => 'Kewarganegaraan', 'name' => 'kewarganegaraan', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 9],
            ['label' => 'Berkebutuhan Khusus', 'name' => 'berkebutuhan_khusus', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 10],
            ['label' => 'Alamat Lengkap', 'name' => 'alamat_lengkap', 'category' => 'Data Pribadi', 'is_active' => true, 'order' => 11],
            // Data Ayah
            ['label' => 'Nama Ayah', 'name' => 'nama_ayah', 'category' => 'Data Ayah', 'is_active' => true, 'order' => 1],
            ['label' => 'NIK Ayah', 'name' => 'nik_ayah', 'category' => 'Data Ayah', 'is_active' => true, 'order' => 2],
            ['label' => 'Tahun Lahir Ayah', 'name' => 'tahun_lahir_ayah', 'category' => 'Data Ayah', 'is_active' => true, 'order' => 3],
            ['label' => 'Pendidikan Ayah', 'name' => 'pendidikan_ayah', 'category' => 'Data Ayah', 'is_active' => true, 'order' => 4],
            ['label' => 'Pekerjaan Ayah', 'name' => 'pekerjaan_ayah', 'category' => 'Data Ayah', 'is_active' => true, 'order' => 5],
            ['label' => 'Penghasilan bulanan Ayah', 'name' => 'penghasilan_bulanan_ayah', 'category' => 'Data Ayah', 'is_active' => true, 'order' => 6],
            // Data Ibu
            ['label' => 'Nama Ibu', 'name' => 'nama_ibu', 'category' => 'Data Ibu', 'is_active' => true, 'order' => 1],
            ['label' => 'NIK Ibu', 'name' => 'nik_ibu', 'category' => 'Data Ibu', 'is_active' => true, 'order' => 2],
            ['label' => 'Tahun Lahir Ibu', 'name' => 'tahun_lahir_ibu', 'category' => 'Data Ibu', 'is_active' => true, 'order' => 3],
            ['label' => 'Pendidikan Ibu', 'name' => 'pendidikan_ibu', 'category' => 'Data Ibu', 'is_active' => true, 'order' => 4],
            ['label' => 'Pekerjaan Ibu', 'name' => 'pekerjaan_ibu', 'category' => 'Data Ibu', 'is_active' => true, 'order' => 5],
            ['label' => 'Penghasilan bulanan Ibu', 'name' => 'penghasilan_bulanan_ibu', 'category' => 'Data Ibu', 'is_active' => true, 'order' => 6],
            // Data Kontak
            ['label' => 'No Telepon Rumah', 'name' => 'no_telepon_rumah', 'category' => 'Data Kontak', 'is_active' => true, 'order' => 1],
            ['label' => 'No Handphone', 'name' => 'no_handphone', 'category' => 'Data Kontak', 'is_active' => true, 'order' => 2],
            ['label' => 'Email', 'name' => 'email', 'category' => 'Data Kontak', 'is_active' => true, 'order' => 3],
            // Data Priodik
            ['label' => 'Tinggi Badan', 'name' => 'tinggi_badan', 'category' => 'Data Priodik', 'is_active' => true, 'order' => 1],
            ['label' => 'Berat Badan', 'name' => 'berat_badan', 'category' => 'Data Priodik', 'is_active' => true, 'order' => 2],
            ['label' => 'Jarak Ke sekolah', 'name' => 'jarak_ke_sekolah', 'category' => 'Data Priodik', 'is_active' => true, 'order' => 3],
            ['label' => 'Jumlah saudara kandung', 'name' => 'jumlah_saudara_kandung', 'category' => 'Data Priodik', 'is_active' => true, 'order' => 4],
        ];
        foreach ($fields as $f) {
            DB::table('form_fields')->updateOrInsert(
                ['name' => $f['name']],
                $f
            );
        }
    }
}
