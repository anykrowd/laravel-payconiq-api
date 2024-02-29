<?php

namespace Anykrowd\PayconiqApi;

use Illuminate\Support\ServiceProvider;

class PayconiqApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('payconiq-api.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'payconiq-api');

        // Register the main class to use with the facade
        $this->app->singleton('payconiq-api', function () {
            $apiUrl = config('payconiq-api.api_url');
            $apiKey = config('payconiq-api.api_key');

            return new PayconiqApiClient($apiUrl, $apiKey);
        });
    }
}
