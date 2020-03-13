<?php

namespace dis\core\main;

use dis\core\classes\Command;
use Exception;

class Main {
    public static function main(array $argv, int $argc) {
        self::args_process($argv, $argc);
        try {
            Command::create($argv, $argc)->run();
        } catch (Exception $e) {
            exit($e->getMessage()."\n");
        }
    }

    private static function args_process(array &$argv, int &$argc) {
        array_shift($argv);
        $argc--;
    }
}