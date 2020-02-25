<?php

namespace traits;

use ReflectionClass;
use ReflectionException;

trait DIS {
    private array $primary_type_array = [
        'string',
        'array',
        'int',
        'float',
        'object',
        'bool',
    ];
    private static bool $is_init = false;

    private final function is_singleton(ReflectionClass $classObject) {
        return $classObject->hasMethod('create');
    }

    /**
     * @throws ReflectionException
     */
    protected function inject_into_properties() {
        $classRef = new ReflectionClass(self::class);
        foreach ($classRef->getProperties() as $property) {
            $_class = $property->getType()->getName();
            if(!in_array($_class, $this->primary_type_array)) {
                $propertyClass = new ReflectionClass($_class);
                $className = $propertyClass->getName();

                if($propertyClass->hasMethod('init')) $this->{$property->getName()} = $className::init();
                elseif ($propertyClass->hasMethod('create')) $this->{$property->getName()} = $className::create();
                else $this->{$property->getName()} = new $className();
            }
        }
        return $this;
    }

    /**
     * @return static
     * @throws ReflectionException
     */
    private static function inject_into_construct(): self {
        $classRef = new ReflectionClass(self::class);
        $construct = $classRef->getConstructor();
        $params = [];
        if(!is_null($construct)) {
            $construct_params = $construct->getParameters();
            foreach ($construct_params as $construct_param) {
                $paramClass = $construct_param->getClass()->getName();

                if($construct_param->getClass()->hasMethod('init')) $params[] = $paramClass::init();
                elseif ($construct_param->getClass()->hasMethod('create')) $params[] = $paramClass::create();
                else $params[] = new $paramClass();
            }
        }
        $object = self::is_singleton($classRef) ? self::create(...$params) : $classRef->newInstanceWithoutConstructor();
        /** @var DIS $object */
        $object = $object->inject_into_properties();
        if(!self::is_singleton($classRef) && !is_null($construct)) {
            $object->__construct(...$params);
        }
        return $object;
    }

    /**
     * @throws ReflectionException
     */
    public static function init(): self {
        return self::inject_into_construct();
    }
}