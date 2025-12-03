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
        Schema::create('periode_tahuns', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('periode',255);
            $table->string('status',255);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_tahuns');
    }
};
