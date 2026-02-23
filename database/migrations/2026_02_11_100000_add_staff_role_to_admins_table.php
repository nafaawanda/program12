<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tidak perlu tambah kolom baru, cukup update data role
        // Jika ingin menambah enum, bisa gunakan tipe string
        // Pastikan field role sudah ada
    }
    public function down(): void
    {
        // Tidak perlu rollback, role staff hanya data
    }
};
