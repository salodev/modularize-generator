<?php

namespace Salodev\Modularize\Generator;

use Exception;

class CodeGenerator
{
    private $stubFilePath = '';
    
    public function __construct(string $stubFilePath)
    {
        $this->stubFilePath = $stubFilePath;
    }
    
    public function generate(string $targetPath, array $replacements = [], bool $override = false)
    {
        $content = file_get_contents($this->stubFilePath);
        
        if (file_exists($targetPath) && !$override) {
            throw new Exception("File {$targetPath} already exists");
        }
        
        $content = static::makeReplacements($content, $replacements);
        
        $targetDirectory = dirname($targetPath);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }
        
        file_put_contents($targetPath, $content, FILE_IGNORE_NEW_LINES);
    }
    
    public static function makeReplacements(string $content, array $replacements): string
    {
        
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        return $content;
    }
}
