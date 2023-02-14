<?php

namespace Salodev\Modularize\Generator\Commands;

class MakeModel extends MakeForModule
{
    protected $signature   = 'modularize:make:model {--module=} {--name=}';
    protected $description = 'Make a module model';
    protected $subFolder   = 'Models\\';
    protected $subCommand  = 'make:model';
}
