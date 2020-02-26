<?php

namespace traits;


trait Singleton {
    protected static ?self $instance = null;

    private function __construct(...$params) {}

    public static function create(...$params): self {
        if(is_null(self::$instance)) {
            self::$instance = new self(...$params);
        }
        return self::$instance;
    }
}