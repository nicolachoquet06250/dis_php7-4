<?php


namespace classes;


use classes\abstracts\Singleton;
use classes\routage\Router;

/**
 * Class Application
 * @package classes
 *
 * @method static Application create(...$params)
 */
class Application extends Singleton {
    private array $objects_to_start = [];
    private static string $context = self::CONTEXT_API;
    const CONTEXT_API = 'api';
    const CONTEXT_WEBSITE = 'website';

    public function add(object $object, string $start_method) {
        $this->objects_to_start[] = [
            'object' => $object,
            'start_method' => $start_method,
        ];
    }

    public function run() {
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