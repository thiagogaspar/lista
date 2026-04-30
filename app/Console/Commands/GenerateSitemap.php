<?php

namespace App\Console\Commands;

use App\Actions\GenerateSitemapAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap.xml file';

    public function handle(GenerateSitemapAction $action): int
    {
        Cache::forget('sitemap');
        $action->handle();
        $this->info('Sitemap regenerated.');

        return self::SUCCESS;
    }
}
