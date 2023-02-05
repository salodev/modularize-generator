<?php

namespace Salodev\Modularize\Generator\Commands;

use Illuminate\Support\Facades\Artisan;
use ReflectionClass;

class MakeForModule extends Command
{
    protected $subFolder      = '';
    protected $suffix         = '';
    protected $subCommand     = '';
    protected $defaultAskName = null;
    
    public function handle()
    {
        
        $key            = $this->option('module');
        $module         = $this->askForModule($key)['module'];
        $name           = $this->option('name') ?? $this->ask('Name', $this->defaultAskName);
        
        $reflection = new ReflectionClass($module);
        $namespaceName = $reflection->getNamespaceName();
        
        $className = "{$namespaceName}\\{$this->subFolder}{$name}{$this->suffix}";
        
        Artisan::call($this->subCommand, [
            'name'      => $className,
        ]);
        
        return 0;
    }
}
