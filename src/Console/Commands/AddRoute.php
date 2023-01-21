<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Salodev\Modularize\CaseHelper;
use Salodev\Modularize\Generator\CodeInjector;
use Salodev\Modularize\Generator\ModuleCodeInjector;
use ReflectionClass;

class AddRoute extends Command
{
    protected $signature   = 'modules:add:route {--module=} {--type=} {--verb=} {--uri=} {--action-name=} {--resource-name=}';
    protected $description = 'Add a module route';
    
    public function handle()
    {
        
        $key          = $this->option('module');
        $data         = $this->askForModule($key);
        $module       = $data['module'];
        $resourceName = Str::singular($module->getName());
        $key          = $data['key'   ];
        $type         = $this->option('type') ?? $this->choice('Route type', ['api', 'web'], 'api');
        $httpVerb     = $this->option('verb') ?? $this->choice('Http verb', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']);
        $uri          = $this->option('uri') ?? $this->ask("Uri for {$httpVerb}", '');
        $defaultMethodName = $this->getInferredMethodNameForUri($uri);
        $methodName   = $this->option('action-name') ?? $this->ask("Action name", $defaultMethodName);
        $resourceName = CaseHelper::toPascalCase($this->option('resource-name') ?? $this->ask("Resource name", $resourceName));
        
        preg_match_all('/({([a-z\-]*)})*/', $uri, $matches);
        $uriParams = array_filter(end($matches));
        
        
        if (empty($methodName)) {
            $this->error('Missing action name');
            return 1;
        }
        
        $reflection          = new ReflectionClass($module);
        $namespaceName       = $reflection->getNamespaceName();
        $moduleName          = ucfirst($module->getName());
        $controllerClassName = "\\{$namespaceName}\\{$moduleName}Controller";
        $resourceClassName   = "\\{$namespaceName}\\Resources\\{$resourceName}Resource";
        
        $injector = new ModuleCodeInjector($module);
        
        $methodRouteCode    = $this->getRouteCode(
            strtolower($httpVerb),
            $uri,
            $controllerClassName,
            $methodName
        );
        
        $moduleRoutesBooter = 'boot' . ucfirst($type) . 'Routes';
        
        if ($injector->hasMethod($moduleRoutesBooter)) {
            $injector->appendCodeToMethod($moduleRoutesBooter, "{$methodRouteCode}");
        } else {
            $injector->appendMethod($moduleRoutesBooter, $methodRouteCode);
        }
        
        
        if (!$this->confirm("Create controller '{$methodName}' method?", true)) {
            return 0;
        }
        
        if (!class_exists($resourceClassName)) {
            $this->createResource($key, $resourceName);
        }
        
        $created = $this->createControllerMethod($controllerClassName, $resourceClassName, $methodName, $uriParams);
        
        if (!$created) {
            return 0;
        }
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
    
    private function createControllerMethod(
        string $controllerClassName,
        string $resourceClassName,
        string $methodName,
        array $uriParams = []
    ): bool {
        if (!class_exists($controllerClassName)) {
            $this->warn("Controller class {$controllerClassName} not found.");
            return false;
        }
        
        $injector = new CodeInjector($controllerClassName);
        
        if ($injector->hasMethod($methodName)) {
            $this->warn("Controller '{$methodName}' method already exists.\n");
            return false;
        }
        
        $controllerMethodParams = [];
        $controllerMethodParams[] = 'Request $request';
        $controllerMethodParams[] = $this->getUriParams($uriParams);
        $controllerMethodSign = implode(', ', array_filter($controllerMethodParams));
        
        $injector->appendMethod($methodName, '', 'public', '', '', $controllerMethodSign);
        
        return true;
    }
    
    private function getRouteCode(
        string $httpVerb,
        string $uri,
        string $controllerClassName,
        string $methodName
    ): string {
        $stringUri      = $this->toKebabCase($uri);
        $stringHttpVerb = strtolower($httpVerb);
        return "\n\t\t\$this->router()->{$httpVerb}('{$stringUri}', [{$controllerClassName}::class, '{$methodName}']);\n\t";
    }
    
    private function getUriParams(array $uriParams = [])
    {
        return implode(', ', array_map(function ($name) {
            return "\${$name}";
        }, $uriParams));
    }
    
    private function createResource(string $key, string $name)
    {
        Artisan::call(MakeResource::class, [
            '--module' => $key,
            '--name'   => $name,
        ]);
    }
}
