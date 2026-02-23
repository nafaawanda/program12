<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detil_pinjams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinjam_id');
            $table->string('kode_buku');
            $table->integer('jml_buku');
            $table->boolean('status')->default(1); // 1: dipinjam, 0: dikembalikan
            $table->timestamps();

            $table->foreign('pinjam_id')->references('id')->on('pinjams')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detil_pinjams');
    }
};
