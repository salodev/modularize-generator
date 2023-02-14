<?php

namespace Salodev\Modularize\Generator\Commands;

class MakeResource extends MakeForModule
{
    protected $signature   = 'modularize:make:resource {--module=} {--name=}';
    protected $description = 'Make a module resource';
    protected $subFolder   = 'Resources\\';
    protected $suffix      = 'Resource';
    protected $subCommand  = 'make:resource';
}
