<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/'; // Ini yang akan mengarahkan ke root URL setelah login
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->alias('files', File::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
