<?php


namespace classes\commands;


use traits\Command;

class Help {
    use Command;

    public function help() {
        var_dump('HELP for '.__CLASS__.' command !');
    }
}