<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Salodev\Modularize\Generator\CodeGenerator;
use Salodev\Modularize\Generator\ModuleCodeInjector;

class MakeModule extends Command
{
    protected $signature   = 'modules:make:module ' .
        '{--module=} {--name=} {--controller} {--config} {--confirm}';
    
    protected $description = 'Make a module';
    
    public function handle()
    {
        $key            = $this->option('module');
        $data           = $this->askForModule($key);
        $module         = $data['module'];
        $key            = $data['key'   ];
        $name           = $this->option('name'      ) ?? $this->ask('Enter module name');
        $makeController = $this->option('controller') ? true : $this->confirm('Make controller?',    true );
        $makeConfig     = $this->option('config',   ) ? true : $this->confirm('Make config file?',   false);
        
        $name = ucfirst($name);
        
        $moduleRootNamespace = $this->getRootNamespace($module);
        
        $moduleRootPath    = $module->getRootPath();
        $targetPath        = "{$moduleRootPath}/{$name}/{$name}Module.php";
        $targetNamespace   = "{$moduleRootNamespace}\\{$name}";
        $targetClass       = "{$name}Module";
        $parentModuleClass = get_class($module);
        $parentModulePath  = $this->laravel->basePath(
            str_replace(
                '\\',
                DIRECTORY_SEPARATOR,
                lcfirst($parentModuleClass)
            ) .
            '.php'
        );
        
        $generator = new CodeGenerator(__DIR__ . '/stubs/module.stub');
        
        try {
            $generator->generate($targetPath, [
                '{{ namespace }}' => $targetNamespace,
                '{{ class }}' => $targetClass,
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 0;
        }
        
        $injector = new ModuleCodeInjector($module);
        $targetModuleClass = "\\{$targetNamespace}\\{$targetClass}";
        if ($injector->hasMethod('register')) {
            $injector->appendCodeToMethod('register', "\t\$this->provide({$targetModuleClass}::class);\n\t");
        } else {
            $injector->appendRegisterMethod($targetModuleClass);
        }
        
        $newModule = $module->provide($targetModuleClass);
        
        
        $newModuleKey = $newModule::getKey();
        
        if ($makeController) {
            $this->makeController($newModuleKey, $name);
        }
        
        if ($makeConfig) {
            $this->makeConfig($newModuleKey);
        }
    }
    
    private function makeController(string $key, string $name): void
    {
        Artisan::call(MakeController::class, [
            '--module' => $key,
            '--name'   => "{$name}Controller",
        ]);
    }
    
    private function makeBusiness(string $key, string $name): void
    {
        Artisan::call(MakeBusiness::class, [
            '--module' => $key,
            '--name'   => $name,
        ]);
    }
    
    private function makeConfig(string $key): void
    {
        Artisan::call(MakeConfig::class, [
            '--module' => $key
        ]);
    }
}
