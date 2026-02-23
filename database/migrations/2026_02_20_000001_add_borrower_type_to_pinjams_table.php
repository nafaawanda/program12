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
        Schema::table('pinjams', function (Blueprint $table) {
            $table->string('borrower_type')->nullable()->after('nim')->default('mahasiswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pinjams', function (Blueprint $table) {
            $table->dropColumn('borrower_type');
        });
    }
};
