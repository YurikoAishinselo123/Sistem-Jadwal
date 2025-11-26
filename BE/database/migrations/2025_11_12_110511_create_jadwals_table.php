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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('hari_jadwal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('sesi');
            $table->year('tahun_ajaran');
            $table->foreignId('prodi_id')->constrained();
            $table->foreignId('makul_id')->constrained();
            $table->foreignId('dosen_1')->references('id')->on('dosens');
            $table->foreignId('dosen_2')->references('id')->on('dosens');
            $table->foreignId('laboran_id')->constrained();
            $table->foreignId('ruangan_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
