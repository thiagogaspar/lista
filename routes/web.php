<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EditSuggestionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenealogyController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/bands', [BandController::class, 'index'])->name('bands.index')
    ->middleware('throttle:30,1');
Route::get('/bands/{slug}', [BandController::class, 'show'])->name('bands.show');

Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index')
    ->middleware('throttle:30,1');
Route::get('/artists/{slug}', [ArtistController::class, 'show'])->name('artists.show');

Route::get('/labels', [LabelController::class, 'index'])->name('labels.index')
    ->middleware('throttle:30,1');
Route::get('/labels/{slug}', [LabelController::class, 'show'])->name('labels.show')
    ->middleware('throttle:30,1');

Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index')
    ->middleware('throttle:30,1');
Route::get('/albums/{slug}', [AlbumController::class, 'show'])->name('albums.show')
    ->middleware('throttle:30,1');

Route::get('/genres/{slug}', GenreController::class)->name('genres.show');

Route::get('/genealogy', GenealogyController::class)->name('genealogy')
    ->middleware('throttle:30,1');

Route::post('/comments', [CommentController::class, 'store'])->name('comments.store')
    ->middleware(['auth', 'throttle:5,1']);

Route::post('/favorites/band/{slug}', [FavoriteController::class, 'toggleBand'])->name('favorites.toggle-band')
    ->middleware(['auth', 'throttle:30,1']);
Route::post('/favorites/artist/{slug}', [FavoriteController::class, 'toggleArtist'])->name('favorites.toggle-artist')
    ->middleware(['auth', 'throttle:30,1']);
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index')
    ->middleware(['auth', 'throttle:30,1']);

Route::post('/suggestions', [EditSuggestionController::class, 'store'])->name('suggestions.store')
    ->middleware(['auth', 'throttle:5,1']);

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index')
    ->middleware('throttle:30,1');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show')
    ->middleware('throttle:30,1');

Route::get('/register', [RegisterController::class, 'create'])->name('register')
    ->middleware('throttle:10,1');
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');

// Redirect Laravel's default 'login' route to admin login
Route::get('/login', fn () => redirect()->route('filament.admin.auth.login'))->name('login')
    ->middleware('throttle:10,1');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index')
    ->middleware('auth');
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('profile.show')
    ->middleware(['auth', 'throttle:30,1']);

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout')->middleware('auth');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap')
    ->middleware('throttle:10,1');

Route::get('/setup', function () {
    \Illuminate\Support\Facades\Artisan::call('app:create-admin-user');
    \Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'ProductionMockDataSeeder',
        '--force' => true,
    ]);

    return response()->json([
        'admin_user' => 'admin@lista.site / 1234 (role: admin)',
        'seeded' => true,
        'counts' => [
            'bands' => \App\Models\Band::count(),
            'artists' => \App\Models\Artist::count(),
            'labels' => \App\Models\Label::count(),
            'albums' => \App\Models\Album::count(),
            'genres' => \App\Models\Genre::count(),
        ],
    ]);
})->middleware('throttle:3,60');
