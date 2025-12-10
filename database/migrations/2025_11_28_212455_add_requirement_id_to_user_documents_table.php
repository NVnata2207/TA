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
    Schema::table('user_documents', function (Blueprint $table) {
        // Tambahkan kolom requirement_id setelah user_id
        $table->unsignedBigInteger('requirement_id')->nullable()->after('user_id');
    });
}

public function down()
{
    Schema::table('user_documents', function (Blueprint $table) {
        $table->dropColumn('requirement_id');
    });
}
};
