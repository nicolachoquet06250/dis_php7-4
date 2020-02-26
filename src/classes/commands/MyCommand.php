<?php


namespace app\classes\commands;


use app\classes\services\Test;
use traits\Command;
use traits\DIS;

/**
 * Class MyCommand
 * @package app\classes\commands
 *
 * @method static MyCommand create(Test $test)
 */
class MyCommand {
    use Command;
    use DIS;

    private ?Test $test = null;

    public function hello() {
        $this->test->test();
    }

    public function help() {
        var_dump('HELP for '.__CLASS__.' command !');
    }
}