<?php

namespace Clive0417\DBDiagramRegularParse;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/d-b-diagram-regular-parse.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('d-b-diagram-regular-parse.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'd-b-diagram-regular-parse'
        );

        $this->app->bind('d-b-diagram-regular-parse', function () {
            return new DBDiagramRegularParse();
        });
    }
}
