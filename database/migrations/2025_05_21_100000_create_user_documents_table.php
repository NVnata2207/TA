<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // formulir, akta, kk, ijazah
            $table->string('file_path');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
}; 