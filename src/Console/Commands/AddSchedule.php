<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Salodev\Modularize\Generator\ModuleCodeInjector;
use ReflectionClass;

class AddSchedule extends Command
{
    protected $signature = 'modularize:add:schedule ' .
        '{--module=} {--command-name=} {--generate-command}';
    
    protected $description = 'Add a sheduled command';
    
    public function handle()
    {
        $key         = $this->option('module');
        $data        = $this->askForModule($key);
        $module      = $data['module'];
        $key         = $data['key'   ];
        $commandName = $this->ask('Command name', '');
        $generateCommand = $this->option('generate-command')
            ? true
            : $this->confirm("Generate command {$commandName}Command ?", true);
        
        $reflection       = new ReflectionClass($module);
        $namespaceName    = $reflection->getNamespaceName();
        $commandClassName = "\\{$namespaceName}\\Commands\\{$commandName}Command";
        
        $injector = new ModuleCodeInjector($module);
        
        $methodCode = $this->getRouteCode($commandClassName);
        
        if ($injector->hasMethod('bootSchedule')) {
            $injector->appendCodeToMethod('bootSchedule', $methodCode);
        } else {
            $injector->appendMethod('bootSchedule', $methodCode);
        }
        
        if ($generateCommand) {
            $this->generateCommand($key, $commandName);
        }
    }
    
    private function getRouteCode(string $className): string
    {
        return "\n\t\t\$this->scheduler()" .
            "->command({$className}::class)" .
            "->dailyAt('00:00');\n\t";
    }
    
    private function generateCommand(string $key, string $name): void
    {
        Artisan::call(MakeCommand::class, [
            '--module' => $key,
            '--name'   => $name,
        ]);
    }
}
