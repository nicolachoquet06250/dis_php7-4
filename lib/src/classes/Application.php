<?php

namespace classes;

use classes\routage\Router;
use traits\Runner;
use traits\Singleton;

/**
 * Class Application
 * @package classes
 *
 * @method static Application create(...$params)
 */
class Application {
    use Singleton;
    use Runner;

    private array $objects_to_start = [];
    private static string $context = self::CONTEXT_API;
    const CONTEXT_API = 'api';
    const CONTEXT_WEBSITE = 'website';

    public function add(object $object, string $start_method): Application {
        $this->objects_to_start[] = [
            'object' => $object,
            'start_method' => $start_method,
        ];
        return $this;
    }

    public function run(): void {
        foreach ($this->objects_to_start as $object) {
            if(get_class($object['object']) === Router::class) {
                echo $object['object']->{$object['start_method']}();
            } else {
                $object['object']->{$object['start_method']}();
            }
        }
    }

    public static function context(?string $context = null): ?string {
        if(!is_null($context)) {
            self::$context = $context;
        }
        return self::$context;
    }
}