<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use Salodev\Modularize\Generator\CodeGenerator;

class MakeConfig extends Command
{
    protected $signature   = 'modularize:make:config {--module=}';
    protected $description = 'Make a module config file';
    
    public function handle()
    {
        $key            = $this->option('module');
        $module         = $this->askForModule($key)['module'];
        
        $reflectionModule    = new \ReflectionClass($module);
        $moduleRootDirectory = dirname($reflectionModule->getFileName());
        $targetFileName = $moduleRootDirectory . DIRECTORY_SEPARATOR . 'config.php';
        
        (new CodeGenerator(__DIR__ . '/stubs/config.stub'))->generate($targetFileName);
    }
}
