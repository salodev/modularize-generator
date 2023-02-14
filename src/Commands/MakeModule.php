<?php

namespace Salodev\Modularize\Generator\Commands;

use Salodev\Modularize\Generator\CodeGenerators\ModuleCodeGenerator;
use Salodev\Modularize\Generator\Modeller;

class MakeModule extends Command
{
    protected $signature   = 'modularize:make:module ' .
        '{--module=} {--name=} {--controller}';
    
    protected $description = 'Make a module';
    
    public function handle()
    {
        $optionIndex = $this->option('module');
        $data   = $this->askForModule($optionIndex);
        $module = $data['module'];
        $name   = $this->option('name') ?? $this->ask('Enter module name');
        
        $name = ucfirst($name);
        
        $modeller = Modeller::forNewModule($module, $name);
        (new ModuleCodeGenerator($modeller))
            ->makeEmptyModule();
        
        //$parentModuleModeller = Modeller::fromModule($module);
        ModuleCodeGenerator::fromModule($module) //($parentModuleModeller);
            ->addSubModule($modeller->moduleClassName);
    }
}
