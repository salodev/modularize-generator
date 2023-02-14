<?php

namespace Salodev\Modularize\Generator\Commands;

use Illuminate\Database\Migrations\Migrator;
use Salodev\Modularize\Generator\ArrayJoinHelper;

use function app;

class ListMigrations extends Command
{
    protected $signature   = 'modularize:list:migrations';
    protected $description = 'List all migrations';
    
    public function handle()
    {

        $files       = $this->getFiles();
        $batches     = $this->getBatches();
        $ran         = $this->getRan();
        
        $withBatches = $files       -> joinByKey($batches -> toArray(), 'name', 'name');
        $withRan     = $withBatches -> joinByKey($ran     -> toArray(), 'name', 'name');

        $basePath = base_path();
        
        $result = array_map(function ($row) use ($basePath) {
            $directoryName = substr($row['filePath'], strlen($basePath));
            $directoryName = dirname($directoryName);

            return [
                'Ran?'      => $row['ran'] ?? 'No',
                'Migration' => $row['name'],
                'Directory' => $directoryName,
                'Batch'     => $row['batchNumber'] ?? null,
            ];
        }, $withRan->toArray());
        
        $this->table(['Ran?', 'Migration', 'Directory', 'Batch'], $result);
        
        return 0;
    }
    
    private function getRan(): ArrayJoinHelper
    {
        $ran = $this->getMigrator()->getRepository()->getRan();
        $list = [];
        foreach ($ran as $name) {
            $list[] = [
                'name' => $name,
                'ran'  => 'Yes',
            ];
        }
        
        return ArrayJoinHelper::make($list);
    }
    
    private function getBatches(): ArrayJoinHelper
    {
        $assoc = $this->getMigrator()->getRepository()->getMigrationBatches();
        $list = [];
        
        foreach ($assoc as $name => $batchNumber) {
            $list[] = [
                'name'        => $name,
                'batchNumber' => $batchNumber,
            ];
        }
        
        return ArrayJoinHelper::make($list);
    }
    
    private function getFiles(): ArrayJoinHelper
    {
        $paths = $this->getMigrator()->paths();
        $assoc = $this->getMigrator()->getMigrationFiles($paths);
        $list = [];
        
        foreach ($assoc as $name => $filePath) {
            $list[] = [
                'name'     => $name,
                'filePath' => $filePath,
            ];
        }
        
        return ArrayJoinHelper::make($list);
    }
    
    private function getMigrator(): Migrator
    {
        return app()->make('migrator');
    }
}
