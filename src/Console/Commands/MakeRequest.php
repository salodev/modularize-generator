<?php

namespace Salodev\Modularize\Generator\Console\Commands;

class MakeRequest extends MakeForModule
{
    protected $signature   = 'modularize:make:request {--module=} {--name=}';
    protected $description = 'Make a module controller';
    protected $subFolder   = 'Requests\\';
    protected $suffix      = 'Request';
    protected $subCommand  = 'make:request';
}
