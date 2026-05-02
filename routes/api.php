<?php

use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\BandController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\LabelController;
use App\Http\Controllers\ApiBandGraphController;
use App\Http\Controllers\ApiGenealogyController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/bands', [BandController::class, 'index']);
    Route::get('/bands/{slug}', [BandController::class, 'show']);
    Route::get('/bands/{slug}/graph', ApiBandGraphController::class);
    Route::get('/artists', [ArtistController::class, 'index']);
    Route::get('/artists/{slug}', [ArtistController::class, 'show']);
    Route::get('/genres', [GenreController::class, 'index']);
    Route::get('/genres/{slug}', [GenreController::class, 'show']);
    Route::get('/labels', [LabelController::class, 'index']);
    Route::get('/labels/{slug}', [LabelController::class, 'show']);
    Route::get('/search', SearchController::class)->name('api.search');
    Route::get('/genealogy', ApiGenealogyController::class);
});
