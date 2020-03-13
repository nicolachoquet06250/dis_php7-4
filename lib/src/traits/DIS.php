<?php

namespace dis\core\traits;

use ReflectionClass;
use ReflectionException;

trait DIS {
    use Instantiator;
    use ParamsInstantiator;
    use ProptertiesInstantiator;

    private static bool $is_init = false;

    private final function is_singleton(ReflectionClass $classObject) {
        return $classObject->hasMethod('create');
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