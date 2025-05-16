<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        VerifyCsrfToken::except([
            '/vibration-log', // API kau tadi
        ]);

        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
    public function register(): void
    {
        //
    }

    protected $middleware = [
        VerifyCsrfToken::class,
    ];
    
    
}
