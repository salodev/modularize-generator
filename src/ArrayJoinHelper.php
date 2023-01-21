<?php

namespace Salodev\Modularize\Generator;

class ArrayJoinHelper
{
    private array $base = [];
    
    public function __construct(array $base)
    {
        $this->base = $base;
    }
    
    public function joinByKey(array $toBeJoined, string $foreignKey, string $localKey, ?callable $callback = null)
    {
        $base = $this->base;
        foreach ($base as &$baseRow) {
            foreach ($toBeJoined as $toBeJoinedRow) {
                if ($baseRow[$localKey] === $toBeJoinedRow[$foreignKey]) {
                    if (is_callable($callback)) {
                        $base = $callback($baseRow, $toBeJoinedRow);
                    } else {
                        $baseRow = array_merge($baseRow, $toBeJoinedRow);
                    }
                }
            }
        }
        
        return new static($base);
    }
    
    public function toArray(): array
    {
        return $this->base;
    }
    
    public static function make(array $base): self
    {
        return new static($base);
    }
}
