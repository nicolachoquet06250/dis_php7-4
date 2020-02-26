<?php

use app\classes\commands\Register;
use classes\Command;
use main\Main;

require_once __DIR__.'/../../vendor/autoload.php';

Command::set_register(Register::class);
Main::main(['cmd.php', 'generate:index', '-p', 'context=api'], 4);