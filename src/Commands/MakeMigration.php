<?php

namespace Salodev\Modularize\Generator\Commands;

use Illuminate\Support\Facades\Artisan;
use Salodev\Modularize\Generator\Modeller;

class MakeMigration extends Command
{
    protected $signature   = 'modularize:make:migration {--module=} {--name=} {--create=}';
    protected $description = 'Make a module migration';
    
    public function handle()
    {
        $optionIndex = $this->option('module', null);
        $data     = $this->askForModule($optionIndex);
        $module   = $data['module'];
        $name     = $this->option('name', null);
        if (!$name) {
            $name = $this->ask('Migration name', null);
        }
        if (!$name) {
            $this->error('Must specify a name');
        }
        $modeller = Modeller::fromModule($module);
        $create   = $this->option('create', null);
        
        $options = [
            'name'   => $name,
            '--path' => "{$modeller->getBasePath($modeller->moduleRootPath)}/Migrations",
        ];
        
        if ($create) {
            $options['--create'] = $create;
        }
        
        Artisan::call("make:migration", $options);
            
        $this->line("Migration file created successful");
        
        return 0;
    }
}
