<?php

namespace Salodev\Modularize\Generator\CodeGenerators;

use Exception;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;

class CodeGenerator
{
    protected string $pathToFile = '';
    protected ?PhpFile $parsedFile = null;
    protected ?ClassType $parsedClass = null;
    
    public function __construct(string $pathToFile)
    {
        $this->pathToFile = $pathToFile;
    }
    
    public function getParsedFile(): PhpFile
    {
        if (!$this->parsedFile) {
            $code = file_get_contents($this->pathToFile);
            $this->parsedFile = PhpFile::fromCode($code);
        }
        
        return $this->parsedFile;
    }
    
    public function getNewFile(): PhpFile {
        if (!$this->parsedFile) {
            $this->parsedFile = new PhpFile();
        }
        
        return $this->parsedFile;
    }
    
    public function withFile(): PhpFile {
        if (!file_exists($this->pathToFile)) {
            $this->parsedFile = new PhpFile;
            return $this->parsedFile;
        } else {
            return $this->getParsedFile();
        }
    }
    
    public function getNewClass(): Classtype {
        if (!$this->parsedClass) {
            $this->parsedClass = new ClassType();
        }
        
        return $this->parsedClass;
    }
    
    public function getParsedClass(): ClassType
    {
        if (!$this->parsedClass) {
            $classes = $this->getParsedFile()->getClasses();
            if (count($classes) === 0) {
                throw new Exception('No classes found in file');
            }

            $this->parsedClass = array_values($classes)[0];
        }
        
        return $this->parsedClass;
    }
    
    public function withClass(string $className): ClassType
    {
        if (!$this->parsedClass) {
            $file = $this->withFile();
            $classes = $file->getClasses();
            if (count($classes) === 0) {
                $this->parsedClass = $file->addClass($className);
            } else {
                $this->parsedClass = array_values($classes)[0];
            }
        }
        return $this->parsedClass;
    }
    
    public function addMethod(string $name): Method
    {
        return $method = $this->getParsedClass()->addMethod($name);
    }
    
    public function hasMethod(string $name): bool
    {
        return $this->getParsedClass()->hasMethod($name);
    }
    
    public function withMethod(string $name): Method
    {
        $class = $this->getParsedClass();
        if (!$class->hasMethod($name)) {
            return $class->addMethod($name);
        }
        return $class->getMethod($name);
    }
    
    public function updateFile(): void
    {
        if (!$this->getParsedFile()) {
            throw new \Exception('No file to save');
        }
        
        $directory = dirname($this->pathToFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents(
            $this->pathToFile, 
            (string) $this->getParsedFile(), 
            FILE_IGNORE_NEW_LINES
        );
    }
}
