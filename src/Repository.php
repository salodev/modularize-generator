<?php

namespace Salodev\Modularize\Generator;

use Salodev\Modularize\Module;

class Repository
{
    public static function getAsList()
    {
        return  \App\Modules\AppModule::getInstance()->renderList();
    }
    
    public static function getByKey(string $key): Module
    {
        $list = static::getAsList();
        foreach ($list as $moduleInfo) {
            if ($moduleInfo['key'] === $key) {
                return $moduleInfo['instance'];
            }
        }
        throw new \Exception("Module was not found by key: '{$key}'");
    }
}
