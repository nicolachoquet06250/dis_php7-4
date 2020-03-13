<?php


namespace dis\core\traits;

use ReflectionClass;

trait ObjectInstantiator {
    /**
     * @param ReflectionClass $class
     * @param string $callback
     * @return object
     */
    private final function instantiate_object(ReflectionClass $class, string $callback) {
        if($class->hasMethod('init')) $object = $callback::init();
        elseif ($class->hasMethod('create')) $object = $callback::create();
        else $object = new $callback();

        return $object;
    }
}