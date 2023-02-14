<?php

namespace Salodev\Modularize\Generator;

use Illuminate\Support\ServiceProvider as Base;
use Salodev\Modularize\Generator\Commands\AddRoute;
use Salodev\Modularize\Generator\Commands\AddSchedule;
use Salodev\Modularize\Generator\Commands\ListMigrations;
use Salodev\Modularize\Generator\Commands\ListModules;
use Salodev\Modularize\Generator\Commands\MakeCommand;
use Salodev\Modularize\Generator\Commands\MakeConfig;
use Salodev\Modularize\Generator\Commands\MakeController;
use Salodev\Modularize\Generator\Commands\MakeCrudModule;
use Salodev\Modularize\Generator\Commands\MakeMail;
use Salodev\Modularize\Generator\Commands\MakeMigration;
use Salodev\Modularize\Generator\Commands\MakeModel;
use Salodev\Modularize\Generator\Commands\MakeModule;
use Salodev\Modularize\Generator\Commands\MakeRequest;
use Salodev\Modularize\Generator\Commands\MakeResource;

class ServiceProvider extends Base
{
    public function register()
    {
        
        $this->commands([
            AddRoute       :: class,
            AddSchedule    :: class,
            ListModules    :: class,
            ListMigrations :: class,
            MakeCommand    :: class,
            MakeConfig     :: class,
            MakeController :: class,
            MakeCrudModule :: class,
            MakeMail       :: class,
            MakeMigration  :: class,
            MakeModel      :: class,
            MakeModule     :: class,
            MakeRequest    :: class,
            MakeResource   :: class,
        ]);
    }
}
