<?php


namespace traits;


use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

trait ProptertiesInstantiator {
	private static final function instantiate_static_properties(ReflectionProperty $property, ReflectionClass $propertyClass): void {
		$className = $propertyClass->getName();
		if ( $propertyClass->hasMethod( 'init' ) ) {
			static::${$property->getName()} = $className::init();
		} elseif ( $propertyClass->hasMethod( 'create' ) ) {
			static::${$property->getName()} = $className::create();
		} else {
			static::${$property->getName()} = new $className();
		}
	}

	private final function instantiate_non_static_properties(ReflectionProperty $property, ReflectionClass $propertyClass) {
		$className = $propertyClass->getName();
		if ( $propertyClass->hasMethod( 'init' ) ) {
			$this->{$property->getName()} = $className::init();
		} elseif ( $propertyClass->hasMethod( 'create' ) ) {
			$this->{$property->getName()} = $className::create();
		} else {
			$this->{$property->getName()} = new $className();
		}
	}

	/**
	 * @param ReflectionProperty $property
	 * @throws ReflectionException
	 */
	private final function instantiate_properties(ReflectionProperty $property): void {
		if (!is_null($property->getType()) && !in_array($property->getType()->getName(), static::$primary_type_array)) {
			$_class = $property->getType()->getName();
			$propertyClass = new ReflectionClass($_class);
			$className = $propertyClass->getName();

			if($property->isStatic()) static::instantiate_static_properties($property, $propertyClass);
			else $this->instantiate_non_static_properties($property, $propertyClass);
		}
	}
}