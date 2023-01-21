<?php

namespace Salodev\Modularize\Generator;

use Salodev\Modularize\Module;

class CodeInjector
{
    private $reflection;
    
    public function __construct($class)
    {
        $this->reflection = new \ReflectionClass($class);
    }
    
    public function hasMethod(string $name): bool
    {
        if (!$this->reflection->hasMethod($name)) {
            return false;
        }
        
        $reflectionMethod = $this->reflection->getMethod($name);
        return $reflectionMethod
            ->getDeclaringClass()
            ->getName() === $this->reflection->getName();
    }
    
    public function appendMethod(
        string $methodName,
        string $methodCode = '',
        string $visibility = 'public',
        string $static = '',
        string $returnType = '',
        string $methodSign = ''
    ) {
        $endLine  = $this->reflection->getEndLine();
        $stringReturnType = $returnType ? ":{$returnType} " : '';
        $this->appendCode($endLine, "\n\t{$visibility} {$static} function {$methodName}({$methodSign}) {$stringReturnType}{{$methodCode}\n\t}\n");
    }
    
    public function appendCodeToMethod(string $methodName, string $codeToAppend)
    {
        $reflection = $this->reflection->getMethod($methodName);
        $endLine    = $reflection->getEndLine();
        $this->appendCode($endLine, $codeToAppend);
    }
    
    private function appendCode(int $endLine, string $codeToAppend)
    {
        $fileName     = $this->reflection->getFileName();
        $endLineIndex = $endLine - 1;
        $code         = file_get_contents($fileName);
        $codeLines    = $this->splitLines($code);
        $endLineCode  = $codeLines[$endLineIndex];
        $newCode      = str_replace("}", "{$codeToAppend}}", $endLineCode);
        $codeLines[$endLineIndex] = $newCode;
        file_put_contents(
            $fileName,
            implode("\n", $codeLines),
            FILE_IGNORE_NEW_LINES
        );
    }
    
    private function splitLines(string $code): array
    {
        return explode("\n", $code);
    }
}
