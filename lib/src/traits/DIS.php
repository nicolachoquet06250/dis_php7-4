<?php

namespace traits;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionProperty;

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

    private final function instantiate_properties(ReflectionProperty $property): void {
        $_class = $property->getType()->getName();
        if (!in_array($_class, $this->primary_type_array)) {
            $propertyClass = new ReflectionClass($_class);
            $className = $propertyClass->getName();

            if ($propertyClass->hasMethod('init')) $this->{$property->getName()} = $className::init();
            elseif ($propertyClass->hasMethod('create')) $this->{$property->getName()} = $className::create();
            else $this->{$property->getName()} = new $className();
        }
    }

    private final function instantiate_params(ReflectionParameter $parameter, array &$params): void {
        $paramClass = $parameter->getClass()->getName();

        if($parameter->getClass()->hasMethod('init')) $params[] = $paramClass::init();
        elseif ($parameter->getClass()->hasMethod('create')) $params[] = $paramClass::create();
        else $params[] = new $paramClass();
    }

    /**
     * @param ReflectionProperty|ReflectionParameter $object
     * @param string $type
     * @param array $params
     * @param bool $static
     */
    private final function instantiate($object, string $type = 'properties', array &$params = [], $static = false) {
        if(gettype($object) === 'object') {
            if(in_array('instantiate_'.$type, get_class_methods(self::class))) {
                $static ? self::{'instantiate_'.$type}($object, $params) : $this->{'instantiate_'.$type}($object, $params);
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    protected final function inject_into_properties() {
        $classRef = new ReflectionClass(self::class);
        foreach ($classRef->getProperties() as $property)
            $this->instantiate($property, 'properties');
        return $this;
    }

    /**
     * @return static
     * @throws ReflectionException
     */
    private final static function inject_into_construct(): self {
        $classRef = new ReflectionClass(self::class);
        $construct = $classRef->getConstructor();
        $params = [];
        if(!is_null($construct)) {
            $construct_params = $construct->getParameters();
            foreach ($construct_params as $construct_param) {
                self::instantiate($construct_param, 'params', $params, true);
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
    public final static function init(): self {
        return self::inject_into_construct();
    }
}