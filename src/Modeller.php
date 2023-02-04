<?php

namespace Salodev\Modularize\Generator;

use Illuminate\Support\Str;
use Salodev\Modularize\CaseHelper;
use Salodev\Modularize\Module;

class Modeller {
    
    public readonly string $modulePath;
    public readonly string $moduleRootPath;
    public readonly string $moduleName;
    public readonly string $moduleClassBaseName;
    public readonly string $moduleNamespace;
    public readonly string $moduleClassName;
    public readonly string $controllerClassBaseName;
    public readonly string $controllerClassName;
    public readonly string $controllerPath;
    public readonly string $modelName;
    public readonly string $modelClassBaseName;
    public readonly string $modelNamespace;
    public readonly string $modelClassName;
    public readonly string $modelPath;
    public readonly string $resourceName;
    public readonly string $resourceUriName;
    public readonly string $resourceClassName;
    public readonly string $resourceClassBaseName;
    public readonly string $resourcePath;
    
    public function __construct(string $modulePath, ?string $modelName = null) {
        $this->modulePath              = $modulePath;
        $this->moduleRootPath          = dirname($modulePath);
        $this->moduleName              = basename($modulePath, 'Module.php');
        $this->moduleClassBaseName     = "{$this->moduleName}Module";
        $this->moduleNamespace         = $this->getNamespaceFromPath($modulePath);
        $this->moduleClassName         = "{$this->moduleNamespace}\\{$this->moduleClassBaseName}";
        $this->controllerClassBaseName = "{$this->moduleName}Controller";
        $this->controllerClassName     = "{$this->moduleNamespace}\\{$this->controllerClassBaseName}";
        $this->controllerPath          = "{$this->moduleRootPath}/{$this->controllerClassBaseName}.php";
        
        $this->modelName = $modelName === null
            ? Str::singular($this->moduleName) 
            : Str::singular($modelName);
        
        $this->modelClassBaseName = $this->modelName;
        $this->modelNamespace = "{$this->moduleNamespace}\\Models";
        $this->modelClassName = "{$this->modelNamespace}\\{$this->modelClassBaseName}";
        $this->modelPath = "{$this->moduleRootPath}/Models/{$this->modelClassBaseName}.php";

        $this->resourceName = Str::singular($this->modelName);
        $this->resourceUriName = CaseHelper::toKebab($this->resourceName);
        $this->resourceClassName = "{$this->moduleNamespace}\\{$this->resourceName}Resource";
        $this->resourceClassBaseName = "{$this->resourceName}Resource";
        $this->resourcePath = "{$this->moduleRootPath}/Resources/{$this->resourceClassBaseName}.php";
    }
    
    public static function fromNewModule(Module $parentModule, string $name, ?string $modelName = null): static {
        $moduleRootPath    = $parentModule->getRootPath();
        $pathForModule     = "{$moduleRootPath}/{$name}/{$name}Module.php";
        return new static($pathForModule, $modelName);
    }
    
    public static function fromModule(Module $module, ?string $modelName = null): static {
        $fileName = (new \ReflectionClass($module))->getFileName();
        return new static($fileName, $modelName);
    }
    
    public function getControllerClassName(string $type): string {
        return strtolower($type) === 'web'
            ? $this->controllerWebClassName
            : $this->controllerApiClassName;
    }
    
    protected function getNamespaceFromPath(string $path): string {
        $basePath = base_path();
        $basePathLength = strlen($basePath);
        if (substr($path, -4) === '.php') {
            $step = substr(dirname($path), $basePathLength+1);
        } else {
            $step = substr($path, $basePathLength+1);
        }
        $step = ucfirst($step);
        $step = str_replace(DIRECTORY_SEPARATOR, '\\', $step);
        
        return $step;
    }
    
    public function getBasePath(string $fullPath): string {
        $basePath = base_path();
        $basePathLength = strlen($basePath);
        return substr($fullPath, $basePathLength);
    }
}
