<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('band_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_band_id')->constrained('bands')->cascadeOnDelete();
            $table->foreignId('child_band_id')->constrained('bands')->cascadeOnDelete();
            $table->enum('type', [
                'split_into',
                'evolved_into',
                'members_formed',
                'side_project',
                'merged_into',
                'rebranded_as',
            ]);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->timestamps();

            $table->unique(['parent_band_id', 'child_band_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('band_relationships');
    }
};
