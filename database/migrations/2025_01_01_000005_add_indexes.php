<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bands', function (Blueprint $table) {
            $table->index('genre');
            $table->index('formed_year');
            $table->index('origin');
        });
        Schema::table('artists', function (Blueprint $table) {
            $table->index('origin');
        });
        Schema::table('band_artist', function (Blueprint $table) {
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::table('bands', function (Blueprint $table) {
            $table->dropIndex(['genre']);
            $table->dropIndex(['formed_year']);
            $table->dropIndex(['origin']);
        });
        Schema::table('artists', function (Blueprint $table) {
            $table->dropIndex(['origin']);
        });
        Schema::table('band_artist', function (Blueprint $table) {
            $table->dropIndex(['is_current']);
        });
    }
};
