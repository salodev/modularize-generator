<?php

namespace Salodev\Modularize\Generator\Commands;

class MakeConfig extends Command
{
    protected $signature   = 'modularize:make:config {--module=}';
    protected $description = 'Make a module config file';
    
    public function handle()
    {
        $optionIndex = $this->option('module');
        $module = $this->askForModule($optionIndex)['module'];
        
        $modeller = \Salodev\Modularize\Generator\Modeller::fromModule($module);
        
        $moduleRootPath = $modeller->moduleRootPath;
        $targetFileName = $moduleRootPath . DIRECTORY_SEPARATOR . 'config.php';
        $code = [];
        
        $code[] = '<?php';
        $code[] = '';
        $code[] = 'return [';
        $code[] = '';
        $code[] = '];';
        $code[] = '';
        
        file_put_contents($targetFileName, implode("\n", $code), FILE_IGNORE_NEW_LINES);
    }
}
