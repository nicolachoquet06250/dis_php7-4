<?php

namespace traits;


trait Singleton {
    protected static ?self $instance = null;

    private function __construct(...$params) {}

    public static function create(...$params): self {
        $class = self::class;
        if(is_null(self::$instance)) {
            self::$instance = new $class(...$params);
        }
        return self::$instance;
    }
}