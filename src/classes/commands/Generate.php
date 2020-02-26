<?php

namespace app\classes\commands;

class Generate extends \classes\commands\Generate {
    /**
     * @param string $content
     */
    public function index(string $content = '') {
        parent::index("<?php

namespace main;

use app\\classes\\controllers\\Test;
use classes\\Application;
use classes\\routage\\Request;
use classes\\routage\\Response;
use classes\\routage\\Router;
use ReflectionException;

require_once __DIR__ . '/vendor/autoload.php';

\$app = Application::create();

Application::context(Application::CONTEXT_" . (in_array(strtoupper($this->param('context')), self::ENABLE_APPLICATION_CONTEXTS) ? strtoupper($this->param('context')) : 'API') . ");

\$router = \$router = Router::create();

try {
\t\$router->parse_q_param();
\t\$router ->get('/', fn (Request \$req, Response \$res) =>
\t\$res->json(['status' => 'HOME']))
\t\t->get('/test/:toto/:tata', fn (Request \$req, Response \$res) =>
\t\t\t\$res->error(400)->json())
\t\t->get('/test', fn (Request \$req, Response \$res) =>
\t\t\t\$res->json(['success' => true]))
\t\t->group('/toto', Test::class)
\t\t->group('/tata', Test::class);

\t\$app->add(\$router, 'route')->run();
} catch (ReflectionException \$e) {
\texit('Reflection Error'.\$e->getMessage());
}
");
    }

    public function cmd(string $content = '') {
        parent::cmd('<?php

namespace main;

use app\classes\commands\Register;
use classes\Command;

require_once __DIR__ . \'/vendor/autoload.php\';

Command::set_register(Register::class);
Main::main($argv, $argc);
');
    }

}