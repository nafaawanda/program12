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
        Schema::table('pegawais', function (Blueprint $table) {
            if (!Schema::hasColumn('pegawais', 'nim')) {
                $table->integer('nim')->unique()->after('id');
            }
            if (!Schema::hasColumn('pegawais', 'nama')) {
                $table->string('nama')->after('nim');
            }
            if (!Schema::hasColumn('pegawais', 'role')) {
                $table->string('role')->default('admin')->after('nama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            if (Schema::hasColumn('pegawais', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('pegawais', 'nama')) {
                $table->dropColumn('nama');
            }
            if (Schema::hasColumn('pegawais', 'nim')) {
                $table->dropColumn('nim');
            }
        });
    }
};
