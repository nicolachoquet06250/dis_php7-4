<?php


namespace dis\core\classes\commands;


use dis\core\traits\Command;

class Help {
    use Command;

    public function help() {
        var_dump('HELP for '.__CLASS__.' command !');
    }
}