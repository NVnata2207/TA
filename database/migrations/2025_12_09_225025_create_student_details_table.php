<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('student_details', function (Blueprint $table) 
        {
            $table->id();
            // HUBUNGKAN DENGAN USER (Foreign Key)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // IDENTITAS
            $table->string('nisn')->nullable();
            $table->string('nik')->nullable();
            $table->enum('jenjang', ['SD', 'SMP'])->nullable();
            $table->string('asal_sekolah')->nullable();

            // DATA PRIBADI
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('agama')->nullable();
            $table->string('golongan_darah', 5)->nullable();
            $table->string('kewarganegaraan')->default('WNI');
            $table->string('tempat_tinggal')->nullable(); // Bersama Orang Tua, Wali, dll
            $table->text('alamat')->nullable();

            // ORANG TUA / WALI
            $table->string('nama_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            
            $table->string('nama_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();

            // KONTAK & LAINNYA
            $table->string('penghasilan_ortu')->nullable();
            $table->string('no_hp')->nullable();

            $table->timestamps();
        });
    }
};
