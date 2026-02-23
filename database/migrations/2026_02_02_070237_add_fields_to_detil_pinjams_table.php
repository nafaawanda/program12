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
        Schema::table('detil_pinjams', function (Blueprint $table) {
            $table->string('nama_penulis')->nullable()->after('kode_buku');
            $table->string('jenis_buku')->nullable()->after('nama_penulis');
            $table->string('kode_rak')->nullable()->after('jenis_buku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detil_pinjams', function (Blueprint $table) {
            $table->dropColumn(['nama_penulis', 'jenis_buku', 'kode_rak']);
        });
    }
};
