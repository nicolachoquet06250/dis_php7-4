<?php


namespace traits;


use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;

trait Instantiator {
	private static array $primary_type_array = [
		'string',
		'array',
		'int',
		'float',
		'object',
		'bool',
	];
    /**
     * @param ReflectionProperty|ReflectionParameter|ReflectionClass $object
     * @param string $type
     * @param array|string $params
     * @param bool $static
     * @return object|null
     */
    private final function instantiate($object, string $type = 'properties', &$params = [], $static = false) {
        if(gettype($object) === 'object') {
            if(in_array('instantiate_'.$type, get_class_methods(self::class))) {
                return $static ? self::{'instantiate_'.$type}($object, $params) : $this->{'instantiate_'.$type}($object, $params);
            }
        }
        return null;
    }
}