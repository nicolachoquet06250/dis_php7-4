<?php


namespace dis\core\classes\commands;


use dis\core\traits\Command;

class Generate {
    use Command;

    const ENABLE_APPLICATION_CONTEXTS = ['API', 'WEBSITE'];

    /**
     * @param string $content
     */
    public function index(string $content = '') {
        $path = __DIR__.(strstr(__DIR__, 'vendor') ? '/../../../../../../..' : '/../../../..').'/index.php';
        if($content !== '') unlink($path);
        if(!file_exists($path)) {
            if($content === '') {
                $content = '<?php

namespace main;

use dis\core\classes\Application;
use dis\core\classes\routage\Request;
use dis\core\classes\routage\Response;
use dis\core\classes\routage\Router;
use ReflectionException;

require_once __DIR__ . \'/vendor/autoload.php\';

$app = Application::create();

Application::context(Application::CONTEXT_' . (in_array(strtoupper($this->param('context')), self::ENABLE_APPLICATION_CONTEXTS) ? strtoupper($this->param('context')) : 'API') . ');

$router = $router = Router::create();

try {
    $router->parse_q_param();
    $router->get(\'/\', fn (Request $req, Response $res) => $res->json([\'status\' => \'HOME\']));

    $app->add($router, \'route\')->run();
} catch (ReflectionException $e) {
    exit(\'Reflection Error\'.$e->getMessage());
}
';
            }
            file_put_contents($path, $content);
        } else echo realpath($path)." already exists\n";
    }

    /**
     * @param string $content
     */
    public function cmd(string $content = '') {
        $path = __DIR__.(strstr(__DIR__, 'vendor') ? '/../../../../../../..' : '/../../../..').'/cmd.php';
        if($content !== '') unlink($path);
        if(!file_exists($path)) {
            if($content === '') {
                $content = '<?php

namespace main;

use dis\core\main\Main;

require_once __DIR__ . \'/vendor/autoload.php\';

Main::main($argv, $argc);
';
            }
            file_put_contents($path, $content);
        } else echo realpath($path)." already exists\n";
    }

    /**
     * @param string $content
     */
    public function htaccess(string $content = '') {
        $path = __DIR__.(strstr(__DIR__, 'vendor') ? '/../../../../../../..' : '/../../../..').'/.htaccess';
        if(!file_exists($path)) {
            file_put_contents($path, 'Options +FollowSymlinks
RewriteEngine on
RewriteOptions Inherit

RewriteRule ^(.+)$  index.php?q=/$1 [L,QSA]
');
        } else echo realpath($path)." already exists\n";
    }

    public function help() {
        var_dump('HELP for '.__CLASS__.' command !');
    }
}