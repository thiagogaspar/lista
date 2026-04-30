<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Genres table
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Band-Genre pivot
        Schema::create('band_genre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('band_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['band_id', 'genre_id']);
        });

        // Hero image + gallery on bands
        Schema::table('bands', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('photo');
            $table->json('gallery')->nullable()->after('bio');
        });

        // Hero image + gallery on artists
        Schema::table('artists', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('photo');
            $table->json('gallery')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('band_genre');
        Schema::dropIfExists('genres');
        Schema::table('bands', function (Blueprint $table) {
            $table->dropColumn(['hero_image', 'gallery']);
        });
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn(['hero_image', 'gallery']);
        });
    }
};
