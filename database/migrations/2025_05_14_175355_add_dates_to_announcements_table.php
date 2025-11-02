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
        Schema::table('announcements', function (Blueprint $table) {
            $table->date('tanggal_pembukaan')->nullable();
            $table->date('tanggal_mulai_pendaftaran')->nullable();
            $table->date('tanggal_selesai_pendaftaran')->nullable();
            $table->date('tanggal_pengumuman')->nullable();
            $table->date('tanggal_mulai_daftar_ulang')->nullable();
            $table->date('tanggal_selesai_daftar_ulang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_pembukaan',
                'tanggal_mulai_pendaftaran',
                'tanggal_selesai_pendaftaran',
                'tanggal_pengumuman',
                'tanggal_mulai_daftar_ulang',
                'tanggal_selesai_daftar_ulang',
            ]);
        });
    }
};
