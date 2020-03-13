<?php


namespace dis\core\traits;

trait DummySingleton {
    private function __construct(...$params) {}

    public static function create(...$params) {
        return new static(...$params);
    }
}