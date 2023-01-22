<?php

namespace Salodev\Modularize\Generator\Console\Commands;

use Illuminate\Support\Facades\Artisan;

class MakeMigration extends Command
{
    protected $signature   = 'modularize:make:migration {--module=} {--name=} {--create=} {--confirm}';
    protected $description = 'Make a module migration';
    
    public function handle()
    {
        $key      = $this->option('module', null);
        $name     = $this->option('name', null);
        $create   = $this->option('create', null);
        $confirm  = $this->option('confirm', false);
        
        
        $data         = $this->askForModule($key);
        
        $module       = $data['module'];
        $rootPath     = $module->getRootPath();
        $relativePath = $this->substractPath($rootPath, base_path());
        $basePath     = $relativePath . DIRECTORY_SEPARATOR . 'Migrations';
        $name         = $name ?? $this->ask("Migration name in {$basePath}");
        $fileName     = $basePath . DIRECTORY_SEPARATOR . $name . '.php';
        
        $confirm = $confirm ? $confirm : $this->confirm("Your migration will be placed in: '{$fileName}', create now?");
        
        if (!$confirm) {
            $this->line("No changes made");
            return 0;
        }
        
        $options = [
            'name'   => $name,
            '--path' => "{$basePath}",
        ];
        
        if ($create) {
            $options['--create'] = $create;
        }
        
        Artisan::call("make:migration", $options);
            
        $this->line("Migration file created successful");
        
        return 0;
    }
}
