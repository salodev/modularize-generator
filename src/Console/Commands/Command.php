<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use CaseHelper\CaseHelperFactory;
use Illuminate\Console\Command as Base;
use Salodev\Modularize\Module;
use Salodev\Modularize\Generator\Repository;

class Command extends Base
{
    protected function getRootNamespace(Module $module)
    {
        $className = get_class($module);
        $step1     = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $step2     = dirname($step1);
        $step3     = str_replace(DIRECTORY_SEPARATOR, '\\', $step2);
        
        return $step3;
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
