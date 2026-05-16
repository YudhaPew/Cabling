<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('splicing_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cable_id')->constrained('cables')->onDelete('cascade');
            $table->integer('tube_number');
            $table->integer('core_number');

            // Status Core
            $table->enum('status', ['Kosong', 'Terpakai', 'Rusak'])->default('Kosong');

            // Keterangan Core ini lari ke mana (Bebas diisi oleh teknisi)
            // Contoh: "Masuk ke Splitter ODP-PANGHEGAR-A1 Port 1" atau "Disambung ke Kabel Feeder 2 Tube 1 Core 4"
            $table->string('connected_to')->nullable();

            $table->string('updated_by')->nullable(); // Nama teknisi yang mengubah
            $table->timestamps();

            // Mencegah duplikasi: Satu core di dalam satu kabel hanya punya 1 status sambungan
            $table->unique(['cable_id', 'tube_number', 'core_number'], 'cable_core_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('splicing_connections');
    }
};
