<?php

namespace classes\abstracts;

class Singleton {
    /**
     * @var Singleton[] $instance
     */
    protected static array $instance = [];

    protected function __construct(...$params) {}

    public static function create(...$params): Singleton {
        $class = static::class;
        if(!isset(static::$instance[$class])) {
            static::$instance[$class] = new $class(...$params);
        }
        return static::$instance[$class];
    }
}