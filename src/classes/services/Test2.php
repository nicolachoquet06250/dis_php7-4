<?php

namespace app\classes\services;

use classes\mvc\Controller;
use traits\DIS;
use traits\Singleton;

/**
 * Class Test2
 * @package classes\services
 *
 * @method static Test2 create(Controller $controller)
 */
class Test2 {
    use Singleton;
    use DIS;

    private ?Controller $controller = null;

    protected function __construct(Controller $controller) {
        var_dump($controller, __LINE__);
    }

    public function test() {
        var_dump($this->controller, __LINE__);
    }
}