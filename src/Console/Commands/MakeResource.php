<?php

namespace Salodev\Modularize\Generator\Console\Commands;

class MakeResource extends MakeForModule
{
    protected $signature   = 'modularize:make:resource {--module=} {--name=}';
    protected $description = 'Make a module resource';
    protected $subFolder   = 'Resources\\';
    protected $suffix      = 'Resource';
    protected $subCommand  = 'make:resource';
}
