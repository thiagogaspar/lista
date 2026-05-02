<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Band;
use App\Models\Genre;
use App\Models\Label;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_bands_index(): void
    {
        Band::factory()->create();
        $response = $this->get('/bands');
        $response->assertStatus(200);
    }

    public function test_band_detail(): void
    {
        $band = Band::factory()->create();
        $response = $this->get("/bands/{$band->slug}");
        $response->assertStatus(200);
    }

    public function test_artists_index(): void
    {
        Artist::factory()->create();
        $response = $this->get('/artists');
        $response->assertStatus(200);
    }

    public function test_artist_detail(): void
    {
        $artist = Artist::factory()->create();
        $response = $this->get("/artists/{$artist->slug}");
        $response->assertStatus(200);
    }

    public function test_labels_index(): void
    {
        Label::factory()->create();
        $response = $this->get('/labels');
        $response->assertStatus(200);
    }

    public function test_label_detail(): void
    {
        $label = Label::factory()->create();
        Band::factory()->create(['label_id' => $label->id]);
        $response = $this->get("/labels/{$label->slug}");
        $response->assertStatus(200);
    }

    public function test_albums_index(): void
    {
        Album::factory()->create();
        $response = $this->get('/albums');
        $response->assertStatus(200);
    }

    public function test_album_detail(): void
    {
        $album = Album::factory()->create();
        $response = $this->get("/albums/{$album->slug}");
        $response->assertStatus(200);
    }

    public function test_genre_page(): void
    {
        $genre = Genre::factory()->create();
        Band::factory()->create()->genres()->attach($genre);
        $response = $this->get("/genres/{$genre->slug}");
        $response->assertStatus(200);
    }

    public function test_genealogy_page(): void
    {
        $response = $this->get('/genealogy');
        $response->assertStatus(200);
    }

    public function test_blog_index(): void
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
    }

    public function test_favorites_page_redirects_guest(): void
    {
        $response = $this->get('/favorites');
        $response->assertStatus(200);
    }

    public function test_sitemap(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
    }

    public function test_registration_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_search_api(): void
    {
        $response = $this->getJson('/api/search?q=test');
        $response->assertStatus(200);
    }

    public function test_api_bands(): void
    {
        Band::factory(3)->create();
        $response = $this->getJson('/api/bands');
        $response->assertStatus(200);
    }

    public function test_api_artists(): void
    {
        Artist::factory(3)->create();
        $response = $this->getJson('/api/artists');
        $response->assertStatus(200);
    }

    public function test_api_genres(): void
    {
        Genre::factory(3)->create();
        $response = $this->getJson('/api/genres');
        $response->assertStatus(200);
    }

    public function test_api_labels(): void
    {
        Label::factory(3)->create();
        $response = $this->getJson('/api/labels');
        $response->assertStatus(200);
    }

    public function test_filters_on_bands_index(): void
    {
        $band = Band::factory()->create();
        $genre = Genre::factory()->create();
        $band->genres()->attach($genre);

        $response = $this->get('/bands?genre='.$genre->slug);
        $response->assertStatus(200);
    }

    public function test_404_page(): void
    {
        $response = $this->get('/nonexistent-page');
        $response->assertStatus(404);
    }
}
