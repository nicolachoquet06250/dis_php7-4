<?php

use main\Main;

require_once __DIR__.'/../../vendor/autoload.php';

Main::main(['cmd.php', 'generate:cmd'], 2);
Main::main(['cmd.php', 'generate:htaccess'], 2);
Main::main(['cmd.php', 'generate:index', '-p', 'context=api'], 4);