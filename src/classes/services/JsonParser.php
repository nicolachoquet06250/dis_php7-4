<?php


namespace app\classes\services;


class JsonParser {
    public function parse(array $test) {
        return json_encode($test);
    }
}