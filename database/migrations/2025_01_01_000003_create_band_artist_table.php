<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('band_artist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('band_id')->constrained()->cascadeOnDelete();
            $table->foreignId('artist_id')->constrained()->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->unsignedSmallInteger('joined_year')->nullable();
            $table->unsignedSmallInteger('left_year')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();

            $table->unique(['band_id', 'artist_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('band_artist');
    }
};
