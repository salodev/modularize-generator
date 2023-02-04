<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use Salodev\Modularize\Generator\CodeGenerators\ModuleCodeGenerator;
use Salodev\Modularize\Generator\Modeller;

class MakeModule extends Command
{
    protected $signature   = 'modularize:make:module ' .
        '{--module=} {--name=} {--controller} {--config} {--confirm}';
    
    protected $description = 'Make a module';
    
    public function handle()
    {
        $optionIndex = $this->option('module');
        $data   = $this->askForModule($optionIndex);
        $module = $data['module'];
        $name   = $this->option('name') ?? $this->ask('Enter module name');
        
        $name = ucfirst($name);
        
        $modeller = Modeller::fromNewModule($module, $name);
        $moduleCodeGenerator = new ModuleCodeGenerator($modeller);
        $moduleCodeGenerator->makeEmptyModule();
        
        $parentModuleModeller = Modeller::fromModule($module);
        $parentModuleCodeGenerator = new ModuleCodeGenerator($parentModuleModeller);
        $parentModuleCodeGenerator->addSubModule(
            $modeller->moduleClassName, 
            $modeller->moduleClassBaseName
        );

    }
}
