<?php

namespace classes\routage;

use classes\Application;
use classes\mvc\Controller;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use traits\Singleton;

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
class Router {
    use Singleton;

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
                            } elseif (!isset($doc_tmp[$matches['key']])) $doc_tmp[$matches['key']] = trim($matches['value']);
                        }
                    }
                    $doc = $doc_tmp;
                    $http_method = isset($doc['http']) ? $doc['http'] : 'get';
                    if(!is_null($doc['route'])) {
                        $this->{$http_method}($doc['route'], function (Request $req, Response $res) use ($object, $method) {
                            return $object->{$method->getName()}($req, $res);
                        }, $group_route);
                    }
                }
            }
        }
    }

    private function instantiate(ReflectionClass $refClass, $callback) {
        if($refClass->hasMethod('init')) $object = $callback::init();
        elseif ($refClass->hasMethod('create')) $object = $callback::create();
        else $object = new $callback();

        return $object;
    }

    /**
     * @param string $route
     * @param callable|Controller $callback
     * @return Router
     * @throws ReflectionException
     */
    public function group(string $route, $callback): Router {
        if(is_callable($callback)) $callback($this, $route);
        else {
            $refClass = new ReflectionClass($callback);
            $object = $this->instantiate($refClass, $callback);
            /** @var Controller $object */
            $object->group_route($route);
            $this->add_methods_into_routes(new ReflectionObject($object), $object, $route);
        }
        return $this;
    }

    private static function cast($val) {
        if(is_string($val)) {
            // for booleans
            if(preg_match('/(true|false)$/sD', $val)) $val = $val === 'true';
            // for numbers
            elseif (preg_match('/([0-9\.]+)$/sD', $val)) $val = intval($val);
            // for arrays and objects
            elseif (preg_match('/\[(.+)\]$/sD', $val) || preg_match('/\{(.+)\}$/sD', $val)) $val = json_decode($val, true);
            // for null
            elseif (preg_match('/(null|NULL)$/sD', $val)) $val = null;

            // for void
            if($val === '') $val = true;
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

    private function extract_get_params($uri, $query_string) {
        if(!strstr($uri, '?')) {
            $query_string = str_replace('q=/index.php&q=', '', $query_string);
            $esperluet_position = strpos($query_string, '&');
            if($esperluet_position) {
                $query_string = substr($query_string, 0, $esperluet_position) . '?' . substr($query_string, $esperluet_position + 1, strlen($query_string));
            }
            $uri = $query_string;
        }

        $_uri = explode('?', $uri);
        if(count($_uri) > 0) {
            $this->uri = $_uri[0];
            $query_string = $_uri[1];
        } else {
            $this->uri = $uri;
            $query_string = '';
        }

        $query_string_array = explode('&', $query_string);
        $tmp = [];
        foreach ($query_string_array as $value) {
            $_ = explode('=', $value);
            $tmp[$_[0]] = $this->cast(urldecode($_[1]));
        }
        $this->get = $tmp;
    }

    private function get_uri() {
        if(empty($this->get)) {
            $this->extract_get_params($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']);
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
                    foreach ($_POST as $k => $v) $request->post($k, $v);
                    $post_body = file_get_contents('php://input');
                    if(!is_null($json_post_body = json_decode($post_body, true)))
                        foreach ($json_post_body as $k => $v) $request->post($k, $v);
                    else $request->post('html', $post_body);
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