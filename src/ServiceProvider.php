<?php

namespace Clive0417\DBDiagramRegularParse;


use Clive0417\DBDiagramRegularParse\Commands\Migrations\MigrationGenerateCommand;
use Clive0417\DBDiagramRegularParse\Commands\Models\ModelGenerateCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/d-b-diagram-regular-parse.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('d-b-diagram-regular-parse.php'),
        ], 'config');
        $this->commands([
            // 產生generation 的 command
            MigrationGenerateCommand::class,
            // 產生 model 的 command
            ModelGenerateCommand::class,
        ]);
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
