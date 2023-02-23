<?php

namespace Salodev\Modularize\Generator\CodeGenerators;

use Salodev\Modularize\Generator\Modeller;
use Salodev\Modularize\Module;

class ModuleCodeGenerator
{
    protected CodeGenerator $codeGenerator;
    protected bool $useLineBreaks = true;
    protected Modeller $modeller;

    public function __construct(Modeller $modeller)
    {
        $this->modeller = $modeller;
        $this->codeGenerator = new CodeGenerator($this->modeller->modulePath);
    }
    
    public static function fromModule(Module $module, ?string $modelName = null): self
    {
        return new static(Modeller::fromModule($module, $modelName));
    }
    
    public function disableLineBreaks(): void
    {
        $this->useLineBreaks = false;
    }
    
    public function enableLineBreaks(): void
    {
        $this->useLineBreaks = true;
    }
    
    public function makeEmptyModule()
    {
        $namespace = $this->modeller->moduleNamespace;
        $file = $this->codeGenerator->withFile();
        $file->addClass($this->modeller->moduleClassName);
        $this->codeGenerator->updateFile();
        
        $this->codeGenerator = new CodeGenerator($this->modeller->modulePath);
        $class = $this->codeGenerator->getParsedClass();
        $class->addExtend('\Salodev\Modularize\Module');
        $this->codeGenerator->updateFile();
    }
    
    public function addCrudRoutes(string $type, ?Module $parentModule = null)
    {
        $uri = $this->modeller->resourceUriName;
        $this->disableLineBreaks();
        $this->addRoute($type, 'get', '', 'index');
        $this->addRoute($type, 'post', '', 'store');
        $this->addRoute($type, 'get', "{{$uri}}", 'show');
        $this->addRoute($type, 'put-patch', "{{$uri}}", 'update');
        $this->addRoute($type, 'delete', "{{$uri}}", 'destroy');
        
        if ($parentModule) {
            $this->checkAddRoutePrefix($parentModule);
        }
        
        $this->codeGenerator->updateFile();
    }
    
    public function addRoute(string $type, string $httpMethodName, string $uri, string $controllerMethodName)
    {
        
        $controllerClassName = $this->modeller->controllerClassName;
        
        $typeCamelCase = ucfirst(strtolower($type));
        
        $methodName = "boot{$typeCamelCase}Routes";

        $code = $httpMethodName === 'put-patch'
            ? "\$this->router()->addRoute(['PUT', 'PATCH'], '{$uri}', [\\{$controllerClassName}::class, '{$controllerMethodName}']);"
            : "\$this->router()->{$httpMethodName}('{$uri}', [\\{$controllerClassName}::class, '{$controllerMethodName}']);";
            
        $this->codeGenerator->withMethod($methodName)->addBody($this->getLineBreak() . $code);
        
        $this->codeGenerator->updateFile();
    }
    
    public function addScheduledCommand(string $commandClassName)
    {
        $method = $this->codeGenerator->withMethod('bootSchedule');
        $code = "\$this->scheduler()" .
            "->command({$commandClassName}::class)" .
            "->dailyAt('00:00');\n";
            
        $method->addBody($this->getLineBreak() . $code);
        $this->codeGenerator->updateFile();
    }
    
    protected function getLineBreak(): string
    {
        return $this->useLineBreaks ? "\n" : '';
    }
    
    public function checkAddRoutePrefix(Module $parentModule)
    {
        $modeller = Modeller::fromModule($parentModule);
        if (file_exists($modeller->resourcePath)) {
            $this->addRoutePrefix($modeller->resourceUriName);
        }
    }
    
    public function addRoutePrefix(string $resourceUriName)
    {
        $class = $this->codeGenerator->getParsedClass();
        $property = $class->addProperty('apiRoutesPrefix', "{{$resourceUriName}}");
        $property->setPublic();
        $property->setType('string');
    }
    
    public function addSubModule(string $className)
    {
        $method = $this->codeGenerator->withMethod('register');
        $method->addBody("\n\$this->provide(\\{$className}::class);");
        $this->codeGenerator->updateFile();
    }
}
