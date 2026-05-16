<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Kabel Feeder Utama, Kabel Distribusi R02

            // Menghubungkan kabel ke dua Node (Pangkal dan Ujung)
            $table->foreignId('source_node_id')->constrained('nodes')->onDelete('cascade');
            $table->foreignId('destination_node_id')->constrained('nodes')->onDelete('cascade');

            // Spesifikasi kapasitas kabel sesuai kasus Anda
            $table->integer('total_tube')->default(1);       // Jumlah Tube (Gede/Kecil)
            $table->integer('cores_per_tube')->default(4);   // Jumlah Core per Tube

            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cables');
    }
};
