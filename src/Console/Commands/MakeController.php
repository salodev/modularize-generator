<?php

namespace Salodev\Modularize\Generator\Console\Commands;

class MakeController extends MakeForModule
{
    protected $signature      = 'modules:make:controller {--module=} {--name=} {--confirm}';
    protected $description    = 'Make a module controller';
    protected $subCommand     = 'make:controller';
    protected $defaultAskName = 'Controller';
}
