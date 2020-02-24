<?php


namespace classes\routage;

use classes\abstracts\Singleton;
use classes\Application;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

/**
 * Class Router
 * @package classes\routage
 *
 * @method static Router create(...$params)
 *
 * @method Router get(string $route, callable $callback, string $group_route = '')
 * @method Router post(string $route, callable $callback, string $group_route = '')
 * @method Router put(string $route, callable $callback, string $group_route = '')
 * @method Router delete(string $route, callable $callback, string $group_route = '')
 */
class Router extends Singleton {
    private array $routes = [];
    private array $available_http_methods = ['get', 'post', 'put', 'delete'];
    private string $uri = '';
    private array $get = [];

    /**
     * @param $name
     * @param $arguments
     * @return Router|mixed
     * @throws Exception
     */
    public function __call($name, $arguments) {
        if(in_array($name, $this->available_http_methods)) {
            list($route, $callback, $group_route) = $arguments;
            $this->routes[] = [
                'http_method' => $name,
                'route' =>  $group_route.$route,
                'callback' => $callback,
            ];
            return $this;
        } elseif (in_array($name, get_class_methods(__CLASS__))) return $this->$name(...$arguments);
        else throw new Exception(__CLASS__.'::'.$name.'() method not found !!');
    }

    /**
     * @param ReflectionClass $classRef
     * @return array
     * @throws ReflectionException
     */
    private function create_params_to_inject_in_construct(ReflectionClass $classRef): array {
        $construct = $classRef->getConstructor();
        if(!is_null($construct)) {
            $construct_params = $construct->getParameters();
            $params = [];
            foreach ($construct_params as $construct_param) {
                $paramClass = $construct_param->getClass()->getName();
                $parent = (new ReflectionClass($paramClass))->getParentClass();
                if($parent && $parent->getName() === Singleton::class) {
                    $params[] = $paramClass::create();
                } else {
                    $params[] = new $paramClass();
                }
            }
            return $params;
        }
        return [];
    }

    /**
     * @param ReflectionClass $classRef
     * @return mixed
     * @throws ReflectionException
     */
    private function inject_into_object_properties(ReflectionClass $classRef) {
        $object = $classRef->newInstanceWithoutConstructor();

        foreach ($classRef->getProperties() as $property) {
            if($property->isPublic()) {
                $_class = $property->getType()->getName();
                $parent = (new ReflectionClass($_class))->getParentClass();
                if($parent && $parent->getName() === Singleton::class) {
                    $object->{$property->getName()} = $_class::create();
                } else {
                    $object->{$property->getName()} = new $_class();
                }
            }
        }
        $object->__construct(...$this->create_params_to_inject_in_construct($classRef));
        return $object;
    }

    private function add_methods_into_routes(ReflectionObject $objectRef, $object, string $group_route) {
        $methods = $objectRef->getMethods();
        foreach ($methods as $method) {
            if($method->isPublic()) {
                if(!is_null($method->getDocComment())) {
                    $doc = trim(str_replace(["\t", "    * ", "    */", "    *", "/**"], "", $method->getDocComment()));
                    $doc = explode("\n", $doc);
                    $doc_tmp = [];
                    foreach ($doc as $k => $v) {
                        if(trim($v) !== '') {
                            preg_match('/\@(?<key>[a-zA-Z0-9]+)\ (?<value>[^\@]+)$/sD', trim($v), $matches);
                            if(isset($doc_tmp[$matches['key']]) && is_string($doc_tmp[$matches['key']])) {
                                $old_val = $doc_tmp[$matches['key']];
                                $doc_tmp[$matches['key']] = [];
                                $doc_tmp[$matches['key']][] = $old_val;
                                $doc_tmp[$matches['key']][] = trim($matches['value']);
                            } elseif (!isset($doc_tmp[$matches['key']])) {
                                $doc_tmp[$matches['key']] = trim($matches['value']);
                            }
                        }
                    }
                    $doc = $doc_tmp;
                    $http_method = isset($doc['http']) ? $doc['http'] : 'get';
                    $this->{$http_method}($doc['route'], function (Request $req, Response $res) use ($object, $method) {
                        return $object->{$method->getName()}($req, $res);
                    }, $group_route);
                }
            }
        }
    }

    /**
     * @param string $route
     * @param $callback
     * @return Router
     * @throws ReflectionException
     */
    public function group(string $route, $callback): Router {
        if(is_callable($callback)) {
            $callback($this, $route);
        } else {
            $refClass = new ReflectionClass($callback);
            $object = $this->inject_into_object_properties($refClass);
//            $object = new $callback(...$this->create_params_to_inject_in_construct($refClass));
//            var_dump($object);
            $this->add_methods_into_routes(new ReflectionObject($object), $object, $route);
        }
        return $this;
    }

    private static function cast($val) {
        if(is_string($val)) {
            if(preg_match('/(true|false)$/sD', $val)) {
                $val = $val === 'true';
            } elseif (preg_match('/([0-9]+)$/sD', $val)) {
                $val = intval($val);
            }
        }
        return $val;
    }

    private function array_to_string($array) {
        if(is_array($array)) return $this->array_to_string($array[0]);
        else return $array;
    }

    private function parse_params($matches) {
        $uri_params = [];
        foreach ($matches as $var => $val) {
            if(is_string($var) && !empty($val)) {
                $val = $this->array_to_string($val);
                $uri_params[$var] = self::cast($val);
            }
        }
        return $uri_params;
    }

    private function extract_get_params($uri) {
        $_uri = explode('?', $uri);
        if(count($_uri) > 0) {
            $this->uri = $_uri[0];
            $queryString = $_uri[1];
            $queryStringArray = explode('&', $queryString);
            $tmp = [];
            foreach ($queryStringArray as $value) {
                $_ = explode('=', $value);
                $tmp[$_[0]] = $_[1];
            }
            $this->get = $tmp;
        }
    }

    private function get_uri() {
        if(empty($this->get)) {
            $this->extract_get_params($_SERVER['REQUEST_URI']);
        }
        return $this->uri;
    }

    private function http_method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function route() {
        foreach ($this->routes as $route) {
            $final_route_regex = '/'.str_replace(['/', '\\\\/'], '\/', preg_replace('/(\:([a-z]+))/sD', '(?<$2>[^\\:\\/]+)', $route['route'])).'$/sDA';

            $uri = $this->get_uri();
            if((bool)preg_match($final_route_regex, $uri, $matches)) {
                if($this->http_method() === strtoupper($route['http_method'])) {
                    $request = new Request();
                    $request->params($this->parse_params($matches));
                    foreach ($this->get as $k => $v) $request->get($k, $v);
                    $response = new Response();

                    return $route['callback']($request, $response);
                } else {
                    $error = (new Response())->error(405);
                    return Application::context() === Application::CONTEXT_API ? $error->json() : $error->html();
                }
            }
        }
        $error = (new Response())->error(404);
        return Application::context() === Application::CONTEXT_API ? $error->json() : $error->html();
    }
}