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
        Schema::create('music', function (Blueprint $table) {
            $table->foreignId('artist_id')->constrained('artist')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('album_name', 255)->nullable();
            $table->enum('genre', ['rnb', 'country', 'classic', 'rock', 'jazz']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music');
    }
};
