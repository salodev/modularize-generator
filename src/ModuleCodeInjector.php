<?php

namespace Salodev\Modularize\Generator;

use Salodev\Modularize\Module;

class ModuleCodeInjector extends CodeInjector
{
    private function getProvideCode(string $moduleName): string
    {
        return "\n\t\t\$this->provide({$moduleName}::class);\n\t";
    }
    
    public function appendRegisterMethod(string $provideModuleName): void
    {
        $code = $this->getProvideCode($provideModuleName);
        $this->appendMethod('register', $code);
    }
}
