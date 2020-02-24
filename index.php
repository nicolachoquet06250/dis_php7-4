<?php

use classes\Application;
use classes\routage\Request;
use classes\routage\Response;
use classes\routage\Router;

require_once __DIR__ . '/vendor/autoload.php';

$app = Application::create();

Application::context(Application::CONTEXT_API);

$router = Router::create()
    ->get('/test/:toto/:tata', function (Request $req, Response $res) {
        return $res->error(400)->json();
    })
    ->get('/test', function (Request $req, Response $res) {
        echo '<pre>';
        var_dump($req, $res);
        echo '</pre>';
    })
    ->group('/toto', \app\classes\controllers\Test::class);

$app->add($router, 'route');

$app->run();