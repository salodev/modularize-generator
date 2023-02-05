<?php

namespace Salodev\Modularize\Generator\Commands;

use Illuminate\Support\Facades\Artisan;

class MakeCommand extends Command
{
    protected $signature   = 'modularize:make:command {--module=} {--name=}';
    protected $description = 'Make a module command';
    
    public function handle()
    {
        
        $key      = $this->option('module', null);
        $name     = $this->option('name', null);
        
        $data     = $this->askForModule($key);
        
        $moduleKey        = $data['key'   ];
        $module           = $data['module'];
        $namespace        = $this->getRootNamespace($module) . '\Commands\\';
        $name             = $name ?? $this->ask("Module class name {$namespace}");
        $className        = "{$namespace}{$name}Command";
        $commandName      = $this->toKebabCase($name);
        $commandNameSpace = str_replace('.', ':', $moduleKey);
        
        Artisan::call("make:command", [
            'name'      => $className,
            '--command' => "{$commandNameSpace}:{$commandName}",
        ]);
        
        $this->line("Command created successful");
        
        return 0;
    }
}
