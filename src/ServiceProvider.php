<?php

namespace Salodev\Modularize\Generator;

use Illuminate\Support\ServiceProvider as Base;
use Salodev\Modularize\Generator\Console\Commands\AddRoute;
use Salodev\Modularize\Generator\Console\Commands\AddSchedule;
use Salodev\Modularize\Generator\Console\Commands\ListMigrations;
use Salodev\Modularize\Generator\Console\Commands\ListModules;
use Salodev\Modularize\Generator\Console\Commands\MakeCommand;
use Salodev\Modularize\Generator\Console\Commands\MakeConfig;
use Salodev\Modularize\Generator\Console\Commands\MakeController;
use Salodev\Modularize\Generator\Console\Commands\MakeCrudModule;
use Salodev\Modularize\Generator\Console\Commands\MakeCqrsModule;
use Salodev\Modularize\Generator\Console\Commands\MakeMail;
use Salodev\Modularize\Generator\Console\Commands\MakeMigration;
use Salodev\Modularize\Generator\Console\Commands\MakeModel;
use Salodev\Modularize\Generator\Console\Commands\MakeModule;
use Salodev\Modularize\Generator\Console\Commands\MakeRequest;
use Salodev\Modularize\Generator\Console\Commands\MakeResource;

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
            MakeCqrsModule :: class,
            MakeMail       :: class,
            MakeMigration  :: class,
            MakeModel      :: class,
            MakeModule     :: class,
            MakeRequest    :: class,
            MakeResource   :: class,
        ]);
    }
}
