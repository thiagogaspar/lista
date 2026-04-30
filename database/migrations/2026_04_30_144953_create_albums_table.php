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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('band_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->unsignedSmallInteger('release_year')->nullable();
            $table->string('cover_art')->nullable();
            $table->text('description')->nullable();
            $table->json('tracklist')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['band_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
