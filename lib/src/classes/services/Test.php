<?php

namespace classes\services;

use classes\abstracts\Singleton;

/**
 * Class Test
 * @package classes\services
 *
 * @method static Test create(...$params)
 */
class Test extends Singleton {
    public function test() {
        var_dump('HELLO');
    }
}