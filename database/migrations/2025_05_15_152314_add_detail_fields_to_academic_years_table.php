<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->integer('kuota')->nullable();
            $table->date('mulai_pendaftaran')->nullable();
            $table->date('selesai_pendaftaran')->nullable();
            $table->date('mulai_seleksi')->nullable();
            $table->date('selesai_seleksi')->nullable();
            $table->date('tanggal_pengumuman')->nullable();
            $table->date('mulai_daftar_ulang')->nullable();
            $table->date('selesai_daftar_ulang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn([
                'kuota',
                'mulai_pendaftaran',
                'selesai_pendaftaran',
                'mulai_seleksi',
                'selesai_seleksi',
                'tanggal_pengumuman',
                'mulai_daftar_ulang',
                'selesai_daftar_ulang',
            ]);
        });
    }
};
