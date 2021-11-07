<?php

namespace Felix\MakePivotTable;

use Felix\MakePivotTable\Commands\PivotMakeCommand;
use Illuminate\Support\ServiceProvider;

class MakePivotServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('command.felix.make.pivot', function ($app) {
            return $app[PivotMakeCommand::class];
        });

        $this->commands('command.felix.make.pivot');
    }
}
