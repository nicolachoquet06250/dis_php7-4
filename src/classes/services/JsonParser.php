<?php

namespace app\classes\services;

class JsonParser {
    /**
     * @param array|array $test
     * @return false|string
     */
    public function parse($test) {
        return json_encode($test);
    }

    /**
     * @param string $json
     * @return array
     */
    public function stringify(string $json) {
        return json_decode($json, true);
    }
}