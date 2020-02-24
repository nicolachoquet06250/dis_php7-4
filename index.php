<?php

use classes\Application;
use classes\routage\Request;
use classes\routage\Response;
use classes\routage\Router;

require_once __DIR__ . '/vendor/autoload.php';

$app = Application::create();

Application::context(Application::CONTEXT_API);

$router = Router::create()
    ->get('/', fn (Request $req, Response $res) => $res->json(['status' => 'HOME']))
    ->get('/test/:toto/:tata', fn (Request $req, Response $res) => $res->error(400)->json())
    ->get('/test', fn (Request $req, Response $res) => $res->json(['success' => true]))
    ->group('/toto', \app\classes\controllers\Test::class);

$app->add($router, 'route');

$app->run();