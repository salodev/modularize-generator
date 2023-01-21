<?php

namespace Salodev\Modularize\Generator\Console\Commands;

class MakeModel extends MakeForModule
{
    protected $signature   = 'modules:make:model {--module=} {--name=}';
    protected $description = 'Make a module model';
    protected $subFolder   = 'Models\\';
    protected $subCommand  = 'make:model';
}