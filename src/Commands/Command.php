<?php

namespace Salodev\Modularize\Generator\Commands;

use CaseHelper\CaseHelperFactory;
use Illuminate\Console\Command as Base;
use Salodev\Modularize\Generator\Repository;
use Salodev\Modularize\Module;

class Command extends Base
{
    protected function getRootNamespace(Module $module)
    {
        return $module->getRootNamespace();
    }
    
    protected function toKebabCase(string $string): string
    {
        $ch = CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_CAMEL_CASE);
        return $ch->toKebabCase($string);
    }
    
    protected function fromKebabToCamelCase(string $string): string
    {
        $ch = CaseHelperFactory::make(CaseHelperFactory::INPUT_TYPE_KEBAB_CASE);
        return $ch->toCamelCase($string);
    }
    
    protected function askForModule(?string $key = null): array
    {
        
        if ($key === null) {
            $list = Repository::getAsList();

            $modules = [];
            foreach ($list as $moduleInfo) {
                $modules[] = "{$moduleInfo['key']}";
            }

            $key    = $this->choice('Choose a module', $modules, 0);
        }
        $module = Repository::getByKey($key);
        
        return [
            'key'    => $key,
            'module' => $module,
        ];
    }
    
    protected function substractPath(string $full, string $substract): string
    {
        return str_replace($substract, '', $full);
    }
}
