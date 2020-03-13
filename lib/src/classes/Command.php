<?php


namespace dis\core\classes;


use dis\core\classes\commands\Register;
use Exception;
use ReflectionClass;
use dis\core\traits\Instantiator;
use dis\core\traits\ObjectInstantiator;
use dis\core\traits\Runner;
use dis\core\traits\DummySingleton as Singleton;

/**
 * Class Command
 * @package dis\core\classes
 *
 * @method static Command create(array $argv, int $argc)
 */
class Command {
    use Singleton;
    use Instantiator;
    use ObjectInstantiator;
    use Runner;

    protected static string $register = commands\Register::class;

    private array $command = [];

    private function __construct(array $argv, int $argc) {
        $this->command = $this->parse_process($argv);
    }

    private function parse_process(array $argv): array {
        $command = array_shift($argv);
        $command = explode(':', $command);
        $command = [
            'command' => $command[0],
            'method'  => $command[1] ?? 'help',
        ];
        if(count($argv) > 0) {
            $params_delimiter = array_shift($argv);
            if($params_delimiter === '-p') {
                $params = $argv;
                $i = 0;
                $tmp = [];
                foreach ($params as $param) {
                    if(strstr($param, '=')) {
                        $param_array = explode('=', $param);
                        $tmp[$param_array[0]] = $param_array[1];
                    } else {
                        $tmp[$i] = $param;
                    }
                    $i++;
                }

                $command['params'] = $tmp;
            }
        }
        return $command;
    }

    public static function set_register(string $register_class = commands\Register::class) {
        static::$register = $register_class;
    }

    /**
     * @throws Exception
     */
    function run(): void {
        /** @var Register $register */
        $register = static::$register;
        $register::init_register();
        $cmd = $register::command($this->command['command']);
        /** @var \dis\core\traits\Command $command */
        $command = $this->instantiate(new ReflectionClass($cmd), 'object', $cmd);
        if($register::is_help()) $command->set_method('help');
        else {
            $command->set_method($this->command['method']);
            if(isset($this->command['params'])) $command->set_params($this->command['params']);
        }
        $command->run();
    }
}