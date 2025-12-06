<?php

namespace App\Providers;

use App\Models\Import;
use App\Models\ImportCost;
use App\Models\ImportDocument;
use App\Observers\ImportObserver;
use App\Observers\ImportCostObserver;
use App\Observers\ImportDocumentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        Import::observe(ImportObserver::class);
        ImportDocument::observe(ImportDocumentObserver::class);
        ImportCost::observe(ImportCostObserver::class);
    }
}
