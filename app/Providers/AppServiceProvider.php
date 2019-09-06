<?php

namespace App\Providers;

use App\CloudProvider;
use App\CloudProviders\DigitalOcean;
use App\Script;
use App\VersionControl;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        CloudProvider::register($this->app);
        VersionControl::register($this->app);
        Script::register($this->app);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
