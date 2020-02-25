<?php

namespace app\classes\services;

use traits\DIS;

class Test3 {
    use DIS;

    private ?Test $test = null;

    public function toto() {
        var_dump('coucou');
        $this->test->test();
    }
}