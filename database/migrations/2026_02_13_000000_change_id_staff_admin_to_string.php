<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Ubah id_staff di tabel staff menjadi string unik
        Schema::table('staff', function (Blueprint $table) {
            $table->string('id_staff', 20)->change();
        });
        // Ubah id di tabel admins menjadi string unik
        Schema::table('admins', function (Blueprint $table) {
            $table->string('id', 20)->change();
        });
        // Ubah pegawai_id di pinjams menjadi string
        Schema::table('pinjams', function (Blueprint $table) {
            $table->string('pegawai_id', 20)->nullable()->change();
        });
    }

    public function down()
    {
        // Kembalikan ke integer jika perlu rollback
        Schema::table('staff', function (Blueprint $table) {
            $table->integer('id_staff')->change();
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->integer('id')->change();
        });
        Schema::table('pinjams', function (Blueprint $table) {
            $table->integer('pegawai_id')->nullable()->change();
        });
    }
};
