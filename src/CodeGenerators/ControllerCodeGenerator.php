<?php

namespace Salodev\Modularize\Generator\CodeGenerators;

class ControllerCodeGenerator
{
    protected CodeGenerator $codeGenerator;

    public function __construct(protected string $controllerPath)
    {
        $this->codeGenerator = new CodeGenerator($controllerPath);
    }
    
    public function addMethod(string $name, array $parametersDef): void
    {
        $method =  $this->codeGenerator->addMethod($name);
        $parameter = $method->addParameter('request');
        foreach ($parametersDef as $parameterName => $parameterDef) {
            $parameter = $method->addParameter($parameterName);
            if (!empty($parameterDef['type'])) {
                $parameter->setType($parameterDef['type']);
            }
        }
        $this->codeGenerator->updateFile();
    }
}
