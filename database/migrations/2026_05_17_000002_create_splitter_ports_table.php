<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('splitter_ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('splitter_id')->constrained('splitters')->onDelete('cascade');
            $table->integer('port_number');
            $table->enum('status', ['Kosong', 'Terpakai', 'Rusak'])->default('Kosong');
            $table->string('customer_name')->nullable();
            $table->string('customer_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['splitter_id', 'port_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('splitter_ports');
    }
};
