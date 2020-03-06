<?php


namespace traits;


use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

trait ProptertiesInstantiator {
    /**
     * @param ReflectionProperty $property
     * @throws ReflectionException
     */
    private final function instantiate_properties(ReflectionProperty $property): void {
        if (!is_null($property->getType()) && !in_array($property->getType()->getName(), static::$primary_type_array)) {
            $_class = $property->getType()->getName();
            $propertyClass = new ReflectionClass($_class);
            $className = $propertyClass->getName();

            if ($propertyClass->hasMethod('init')) $this->{$property->getName()} = $className::init();
            elseif ($propertyClass->hasMethod('create')) $this->{$property->getName()} = $className::create();
            else $this->{$property->getName()} = new $className();
        }
    }
}