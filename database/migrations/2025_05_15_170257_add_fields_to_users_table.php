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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_pendaftaran')->nullable()->after('id');
            $table->string('jurusan')->nullable()->after('name');
            $table->string('jalur')->nullable()->after('jurusan');
            $table->string('hasil')->nullable()->after('status_pendaftaran');
            $table->string('daftar_ulang')->nullable()->after('hasil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_pendaftaran', 'jurusan', 'jalur', 'hasil', 'daftar_ulang']);
        });
    }
};
