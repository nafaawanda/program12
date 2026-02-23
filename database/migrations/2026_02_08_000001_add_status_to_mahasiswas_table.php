<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->boolean('status')->default(1)->after('role'); // 1=aktif, 0=non-aktif
        });
    }
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
