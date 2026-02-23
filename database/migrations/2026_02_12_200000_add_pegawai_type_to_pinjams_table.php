<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('pinjams', function (Blueprint $table) {
            $table->string('pegawai_type')->nullable()->after('pegawai_id');
        });
    }
    public function down() {
        Schema::table('pinjams', function (Blueprint $table) {
            $table->dropColumn('pegawai_type');
        });
    }
};
