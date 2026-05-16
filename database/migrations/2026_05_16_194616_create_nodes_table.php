<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: JB-01, ODP-Panghegar-A1
            $table->enum('type', ['JB', 'ODP', 'POP']); // Tipe infrastruktur

            // Koordinat Geografis (Latitude & Longitude) sesuai kebutuhan Anda
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            $table->text('description')->nullable(); // Catatan tambahan (misal: di tiang nomor berapa)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
