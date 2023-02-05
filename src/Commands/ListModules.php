<?php

namespace Salodev\Modularize\Generator\Commands;

use Salodev\Modularize\Generator\Repository;

class ListModules extends Command
{
    protected $signature   = 'modularize:list:modules';
    protected $description = 'List all modules';
    
    public function handle()
    {
        
        $list = Repository::getAsList();
        $rs = [];
        foreach ($list as $moduleInfo) {
            $rs[] = [
                'Key'   => $moduleInfo['key'],
                'Class' => get_class($moduleInfo['instance']),
            ];
        }
        
        $this->table(['Key', 'Class'], $rs);
        
        return 0;
    }
}
