<?php

namespace Mostafaznv\PhpXsendfile;

use Illuminate\Support\ServiceProvider;

class PhpXsendfileServiceProvider extends ServiceProvider
{
    const VERSION = '1.0.0';

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/config.php' => config_path('x-sendfile.php')], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'x-sendfile');

        $this->app->singleton('x-sendfile', function() {
            return new PhpXsendfile;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [PhpXsendfile::class];
    }
}
