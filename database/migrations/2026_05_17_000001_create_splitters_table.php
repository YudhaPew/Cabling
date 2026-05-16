<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('splitters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('nodes')->onDelete('cascade');
            $table->enum('capacity', ['1:4', '1:8', '1:16']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('splitters');
    }
};
