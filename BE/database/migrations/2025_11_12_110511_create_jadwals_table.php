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
            $table->string('sesi');
            $table->year('tahun_ajaran');
            $table->foreignId('id_prodi');
            $table->foreignId('id_makul');
            $table->foreignId('id_dosen');
            $table->foreignId('id_laboran');
            $table->foreignId('id_ruangan');
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
