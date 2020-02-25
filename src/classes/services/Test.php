<?php

namespace app\classes\services;

use traits\Singleton;

/**
 * Class Test
 * @package classes\services
 *
 * @method static Test create()
 */
class Test {
    use Singleton;

    public function test() {
        var_dump('HELLO');
    }
}