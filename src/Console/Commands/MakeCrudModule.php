<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Salodev\Modularize\Generator\CodeGenerator;
use Salodev\Modularize\Generator\ModuleCodeInjector;

class MakeCrudModule extends Command
{
    protected $signature   = 'modularize:make:crud-module ' .
        '{--module=} {--name=} {--modelName=} {--confirm}';
    
    protected $description = 'Make a CRUD module';
    
    private $stubParameters = [];
    
    public function handle()
    {
        $key       = $this->option('module');
        $data      = $this->askForModule($key);
        $module    = $data['module'];
        $key       = $data['key'   ];
        $name      = ucfirst($this->option('name') ??
            $this->ask('Enter module name'));
        
        $modelName = ucfirst($this->option('modelName') ??
            $this->ask('Enter a model name', Str::singular($name)));
        
        $moduleRootNamespace = $this->getRootNamespace($module);
        
        $moduleRootPath    = $module->getRootPath();
        $pathForModule     = "{$moduleRootPath}/{$name}/{$name}Module.php";
        $pathForController = "{$moduleRootPath}/{$name}/{$name}Controller.php";
        $pathForModel      = "{$moduleRootPath}/{$name}/Models/{$modelName}.php";
        $targetNamespace   = "{$moduleRootNamespace}\\{$name}";
        $namespaceForModel = "{$moduleRootNamespace}\\{$name}\\Models";
        $targetClass       = "{$name}Module";
        $routeResourceName = $this->toKebabCase($modelName);
        
        $this->stubParameters = [
            '{{namespace}}'      => $targetNamespace,
            '{{name}}'           => $name,
            '{{modelShortName}}' => $modelName,
        ];
        
        $this->generateCode('/stubs/crud/module.stub', $pathForModule, [
            '{{routeResourceName}}' => $routeResourceName,
        ]);
        $this->generateCode('/stubs/crud/controller.stub', $pathForController, [
            '{{modelName}}' => $modelName,
            '{{routeResourceName}}' => Str::singular($routeResourceName),
            '{{resourceName}}' => "{$modelName}Resource",
        ]);
        $this->generateCode('/stubs/all/Models/model.stub', $pathForModel, [
            '{{namespace}}' => $namespaceForModel,
            '{{name}}'      => $modelName,
        ]);
        
        $this->generateCode(
            "/stubs/all/Requests/CreateRequest.stub",
            "{$moduleRootPath}/{$name}/Requests/CreateRequest.php"
        );
        $this->generateCode(
            "/stubs/all/Requests/UpdateRequest.stub",
            "{$moduleRootPath}/{$name}/Requests/UpdateRequest.php"
        );
        
        $this->generateCode(
            "/stubs/all/Resources/resource.stub",
            "{$moduleRootPath}/{$name}/Resources/{$modelName}Resource.php"
        );
        
        
        $injector = new ModuleCodeInjector($module);
        $targetModuleClass = "\\{$targetNamespace}\\{$targetClass}";
        if ($injector->hasMethod('register')) {
            $injector->appendCodeToMethod(
                'register',
                "\t\$this->provide({$targetModuleClass}::class);\n\t"
            );
        } else {
            $injector->appendRegisterMethod($targetModuleClass);
        }

        $childModule = $module->provide("\\{$targetNamespace}\\{$targetClass}");
        
        $tableName = lcfirst($modelName);
        
        Artisan::call(MakeMigration::class, [
            '--module'  => $childModule::getKey(),
            '--name'    => "create_{$tableName}_table",
            '--create'  => $tableName,
            '--confirm' => true,
        ]);
    }
    
    public function generateCode(
        string $stubPath,
        string $targetPath,
        array $parameters = []
    ) {
        try {
            $generator = new CodeGenerator(__DIR__ . $stubPath);
            $generator->generate(
                $targetPath,
                array_merge(
                    $this->stubParameters,
                    $parameters
                )
            );
        } catch (Exception $e) {
            $this->error($e->getMessage());
            die(1);
        }
    }
}
