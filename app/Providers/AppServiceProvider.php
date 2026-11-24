<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AnonymousTokenService;

use App\Services\EvidenceEncryptionService;
use App\Services\ZoneScoreService;
use App\Services\HotspotDetectionService;

use App\Services\BadgeService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */



public function register(): void
{
    $this->app->singleton(AnonymousTokenService::class);
    $this->app->singleton(EvidenceEncryptionService::class);
    $this->app->singleton(ZoneScoreService::class);
    $this->app->singleton(HotspotDetectionService::class);
    $this->app->singleton(BadgeService::class);
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }






}
