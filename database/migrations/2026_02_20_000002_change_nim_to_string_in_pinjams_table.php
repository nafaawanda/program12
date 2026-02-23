<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pinjams', function (Blueprint $table) {
            // Ubah nim dari integer ke string untuk support staff/admin ID
            $table->string('nim', 20)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pinjams', function (Blueprint $table) {
            $table->integer('nim')->change();
        });
    }
};
