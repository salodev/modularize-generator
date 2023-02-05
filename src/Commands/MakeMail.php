<?php

namespace Salodev\Modularize\Generator\Commands;

class MakeMail extends MakeForModule
{
    protected $signature   = 'modularize:make:mail {--module=} {--name=}';
    protected $description = 'Make a module mail';
    protected $subFolder   = 'Mails\\';
    protected $suffix      = 'Mail';
    protected $subCommand  = 'make:mail';
}
