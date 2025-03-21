<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected $middleware = [
        VerifyCsrfToken::class,
    ];
    
    public function boot()
    {
        VerifyCsrfToken::except([
            '/vibration-log', // API kau tadi
        ]);
    }
}
