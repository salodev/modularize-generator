<?php

namespace Salodev\Modularize\Generator\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Salodev\Modularize\CaseHelper;
use Salodev\Modularize\Generator\CodeGenerators\ControllerCodeGenerator;
use Salodev\Modularize\Generator\CodeGenerators\ModuleCodeGenerator;
use Salodev\Modularize\Generator\Modeller;

class MakeCrudModule extends Command
{
    protected $signature = 'modularize:make:crud-module ' .
        '{--module=} ' .
        '{--name=} ' .
        '{--modelName=} ' .
        '{--confirm}';
    
    protected $description = 'Make a CRUD module';
    
    public function handle()
    {
        $moduleIndex = $this->option('module');
        $data      = $this->askForModule($moduleIndex);
        $key       = $data['key'];
        $module    = $data['module'];
        $optionName = $this->option('name') ?? $this->ask('Enter module name');

        $moduleName = ucfirst($optionName);
        $childKeyPart = CaseHelper::toKebab($optionName);
        
        $modelName = ucfirst($this->option('modelName') ??
            $this->ask('Enter a model name', Str::singular($moduleName)));

        $childKey  = "{$key}.{$childKeyPart}";
        $modeller = Modeller::forNewModule($module, $moduleName);

        $moduleCodeGenerator = new ModuleCodeGenerator($modeller);
        
        $this->line('Creating module class file...');
        Artisan::call(MakeModule::class, [
            '--module' => $key,
            '--name'   => $moduleName,
        ]);
        
        require_once($modeller->modulePath);
        $childModule = $module->provide($modeller->moduleClassName);
        
        $this->line('Creating controller class file...');
        Artisan::call(MakeController::class, [
            '--module' => $childKey,
            '--name'   => $modeller->controllerClassBaseName,
            '--confirm' => true,
        ]);
        
        $this->line('Creating request class file for Create action...');
        Artisan::call(MakeRequest::class, [
            '--module' => $childKey,
            '--name'   => 'Create',
        ]);
        
        $this->line('Creating request class file for Update action...');
        Artisan::call(MakeRequest::class, [
            '--module' => $childKey,
            '--name'   => 'Update',
        ]);
        
        $this->line('Creating model class file...');
        Artisan::call(MakeModel::class, [
            '--module' => $childKey,
            '--name' => $modelName,
        ]);
        
        $this->line('Creating resource class file...');
        Artisan::call(MakeResource::class, [
            '--module' => $childKey,
            '--name' => $modelName,
        ]);
        
        
        $this->line('Adding routes to new module...');
        $moduleCodeGenerator->addCrudRoutes('api', $module);
        
        $this->addControllerMethods($modeller);

        $this->line('Registering new module...');
        
        $tableName = lcfirst($modelName);
        
        $this->line('Creationg a migration file...');
        Artisan::call(MakeMigration::class, [
            '--module'  => $childModule::getKey(),
            '--name'    => "create_{$tableName}_table",
            '--create'  => $tableName,
        ]);
    }
    
    private function addControllerMethods(Modeller $modeller)
    {
        
        $codeGenerator = new ControllerCodeGenerator($modeller->controllerPath);
        $codeGenerator->addMethod('index', [
            'request' => [
                'type' => \Illuminate\Http\Request::class,
            ],
        ]);
        
        $codeGenerator->addMethod('show', [
            'request' => [
                'type' => \Illuminate\Http\Request::class,
            ],
            $modeller->resourceUriName => [
                'type' => $modeller->resourceClassName,
            ]
        ]);
        
        $codeGenerator->addMethod('store', [
            'request' => [
                'type' => "{$modeller->requestNamespace}\\CreateRequest",
            ]
        ]);
        
        $codeGenerator->addMethod('update', [
            'request' => [
                'type' => "{$modeller->requestNamespace}\\UpdateRequest",
            ],
            $modeller->resourceUriName => [
                'type' => $modeller->resourceClassName,
            ]
        ]);
        
        $codeGenerator->addMethod('destroy', [
            'request' => [
                'type' => \Illuminate\Http\Request::class,
            ],
            $modeller->resourceUriName => [
                'type' => $modeller->resourceClassName,
            ]
        ]);
    }
}
