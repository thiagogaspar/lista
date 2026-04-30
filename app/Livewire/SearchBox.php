<?php

namespace App\Livewire;

use App\Models\Artist;
use App\Models\Band;
use Livewire\Component;

class SearchBox extends Component
{
    public string $query = '';

    public bool $showResults = false;

    public function updatedQuery(): void
    {
        $this->showResults = strlen($this->query) >= 2;
    }

    public function select(string $type, string $slug): void
    {
        $this->showResults = false;
        $this->query = '';
        $this->redirect(route("{$type}s.show", $slug));
    }

    public function render()
    {
        $results = ['bands' => collect(), 'artists' => collect()];

        if ($this->showResults) {
            $q = $this->query;
            $results['bands'] = Band::where('name', 'like', "%{$q}%")
                ->limit(5)->get(['id', 'name', 'slug', 'genre']);
            $results['artists'] = Artist::where('name', 'like', "%{$q}%")
                ->limit(5)->get(['id', 'name', 'slug', 'origin']);
        }

        return view('livewire.search-box', $results);
    }
}
