<?php

namespace Salodev\Modularize\Generator\Commands;

use Illuminate\Support\Facades\Artisan;
use Salodev\Modularize\Generator\CodeGenerators\ModuleCodeGenerator;
use Salodev\Modularize\Generator\Modeller;

class AddSchedule extends Command
{
    protected $signature = 'modularize:add:schedule ' .
        '{--module=} {--command-name=} {--generate-command}';
    
    protected $description = 'Add a sheduled command';
    
    public function handle()
    {
        $optionIndex = $this->option('module');
        $data        = $this->askForModule($optionIndex);
        $module      = $data['module'];
        $key         = $data['key'   ];
        $commandName = $this->ask('Command name', '');
        $generateCommand = $this->option('generate-command')
            ? true
            : $this->confirm("Generate command {$commandName}Command ?", true);
        
        $modeller = Modeller::fromModule($module);
        $commandClassName = "\\{$modeller->commandNamespace}\\{$commandName}Command";
        
        if ($generateCommand) {
            $this->generateCommand($key, $commandName);
            require_once("{$modeller->commandRootPath}/{$commandName}Command.php");
        }
        

        $codeGenerator = new ModuleCodeGenerator($modeller);
        $codeGenerator->addScheduledCommand($commandClassName);
    }
    
    private function generateCommand(string $key, string $name): void
    {
        Artisan::call(MakeCommand::class, [
            '--module' => $key,
            '--name'   => $name,
        ]);
    }
}
