<?php

namespace Salodev\Modularize\Generator\Console\Commands;

class MakeMail extends MakeForModule
{
    protected $signature   = 'modules:make:mail {--module=} {--name=}';
    protected $description = 'Make a module mail';
    protected $subFolder   = 'Mails\\';
    protected $suffix      = 'Mail';
    protected $subCommand  = 'make:mail';
}
