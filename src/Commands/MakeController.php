<?php

namespace Salodev\Modularize\Generator\Commands;

class MakeController extends MakeForModule
{
    protected $signature      = 'modularize:make:controller {--module=} {--name=} {--confirm}';
    protected $description    = 'Make a module controller';
    protected $subCommand     = 'make:controller';
    protected $defaultAskName = 'Controller';
}
