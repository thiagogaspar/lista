<?php

use App\Http\Controllers\ApiBandGraphController;
use App\Http\Controllers\ApiGenealogyController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenealogyController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/bands', [BandController::class, 'index'])->name('bands.index')
    ->middleware('throttle:30,1');
Route::get('/bands/{slug}', [BandController::class, 'show'])->name('bands.show');

Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index')
    ->middleware('throttle:30,1');
Route::get('/artists/{slug}', [ArtistController::class, 'show'])->name('artists.show');

Route::get('/genres/{slug}', GenreController::class)->name('genres.show');

Route::get('/genealogy', GenealogyController::class)->name('genealogy')
    ->middleware('throttle:30,1');

Route::post('/comments', [CommentController::class, 'store'])->name('comments.store')
    ->middleware('auth');

Route::post('/favorites/band/{slug}', [FavoriteController::class, 'toggleBand'])->name('favorites.toggle-band')
    ->middleware('auth');
Route::post('/favorites/artist/{slug}', [FavoriteController::class, 'toggleArtist'])->name('favorites.toggle-artist')
    ->middleware('auth');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/api/search', SearchController::class)->name('api.search')
    ->middleware('throttle:60,1');
Route::get('/api/bands/{slug}/graph', ApiBandGraphController::class)
    ->middleware('throttle:60,1');
Route::get('/api/genealogy', ApiGenealogyController::class)
    ->middleware('throttle:30,1');
