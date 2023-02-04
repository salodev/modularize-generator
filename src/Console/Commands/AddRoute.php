<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Salodev\Modularize\CaseHelper;
use Salodev\Modularize\Generator\CodeGenerators\ControllerCodeGenerator;
use Salodev\Modularize\Generator\CodeGenerators\ModuleCodeGenerator;
use Salodev\Modularize\Generator\Modeller;

class AddRoute extends Command
{
    protected $signature   = 'modularize:add:route {--module=} {--verb=} {--uri=} {--method-name=} {--resource-name=}';
    protected $description = 'Add a module route';
    
    public function handle()
    {
        
        $optionIndex  = $this->option('module');
        $data         = $this->askForModule($optionIndex);
        $module       = $data['module'];
        $defaultResourceName = Str::singular($module->getName());
        $key          = $data['key'   ];
        $httpVerb     = $this->option('verb') ?? $this->choice('Http verb', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']);
        $uri          = $this->option('uri') ?? $this->ask("Uri for {$httpVerb}", '');
        
        $methodName = $this->option('method-name');
        
        if (!$methodName) {
            $defaultMethodName = $this->getInferredMethodNameForUri($uri);
            $methodName   = $this->option('method-name') ?? $this->ask("Action name", $defaultMethodName);
        }
        
        $resourceName = CaseHelper::toPascalCase($this->option('resource-name') ?? $this->ask("Resource name", $defaultResourceName));
        
        preg_match_all('/({([a-z\-]*)})*/', $uri, $matches);
        $uriParams = array_filter(end($matches));
        
        if (empty($methodName)) {
            $this->error('Missing action name');
            return 1;
        }
        
        $modeller = Modeller::fromModule($module);
        $moduleCodeGenerator = new ModuleCodeGenerator($modeller);
        $moduleCodeGenerator->enableLineBreaks();
        $moduleCodeGenerator->addRoute('api', strtolower($httpVerb), $uri, $methodName);
        
        $controllerCodeGenerator = new ControllerCodeGenerator($modeller->controllerPath);
        
        $paramsMap = [
            'request' => [
                'type' => \Illuminate\Http\Request::class,
            ]
        ];
        
        foreach($uriParams as $paramName) {
            $paramsMap['$paramName'] = [];
        }
        
        print_r($paramsMap);
        
        $controllerCodeGenerator->addMethod($methodName, $paramsMap);
        
        $this->createResource($key, $resourceName);
        
        return 0;
    }
    
    private function getInferredMethodNameForUri(string $uri): ?string
    {
        $uriParts = explode('/', $uri);
        $uriLastPart = end($uriParts);
        
        if (strpos($uriLastPart, '{') !== false) {
            return null;
        }
        
        return $this->fromKebabToCamelCase($uriLastPart);
    }
    
    private function createResource(string $key, string $name)
    {
        Artisan::call(MakeResource::class, [
            '--module' => $key,
            '--name'   => $name,
        ]);
    }
}
