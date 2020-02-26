<?php


namespace traits;

use ReflectionParameter;

trait ParamsInstantiator {
    private array $primary_type_array = [
        'string',
        'array',
        'int',
        'float',
        'object',
        'bool',
    ];

    /**
     * @param ReflectionParameter $parameter
     * @param array $params
     */
    private final function instantiate_params(ReflectionParameter $parameter, array &$params): void {
        if(!is_null($parameter->getType()) && !in_array($parameter->getType()->getName(), $this->primary_type_array)) {
            $paramClass = $parameter->getClass()->getName();

            if ($parameter->getClass()->hasMethod('init')) $params[] = $paramClass::init();
            elseif ($parameter->getClass()->hasMethod('create')) $params[] = $paramClass::create();
            else $params[] = new $paramClass();
        }
    }
}