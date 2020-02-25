<?php

namespace main;

use app\classes\controllers\Test;
use classes\Application;
use classes\routage\Request;
use classes\routage\Response;
use classes\routage\Router;
use ReflectionException;

require_once __DIR__ . '/vendor/autoload.php';

$app = Application::create();

Application::context(Application::CONTEXT_API);

$router = $router = Router::create();

try {
    if(isset($_GET['q'])) {
        if(substr($_GET['q'], 0, strlen('/'.dirname(__DIR__))) === '/'.dirname(__DIR__))
            $_GET['q'] = str_replace('/'.dirname(__DIR__), '', $_GET['q']);
        $_SERVER['REQUEST_URI'] = $_GET['q'];
        unset($_GET['q']);
    }
    $router ->get('/', fn (Request $req, Response $res) =>
                $res->json(['status' => 'HOME']))
            ->get('/test/:toto/:tata', fn (Request $req, Response $res) =>
                $res->error(400)->json())
            ->get('/test', fn (Request $req, Response $res) =>
                $res->json(['success' => true]))
            ->group('/toto', Test::class)
            ->group('/tata', Test::class);

    $app->add($router, 'route')->run();
} catch (ReflectionException $e) {
    exit('Reflection Error'.$e->getMessage());
}
