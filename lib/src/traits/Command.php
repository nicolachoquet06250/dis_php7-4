<?php


namespace traits;


use Exception;
use ReflectionObject;

/**
 * Trait Command
 * @package traits
 *
 * @method static self create()
 */
trait Command {
    use Runner;
    use DummySingleton;

    protected string $method;
    protected array $params = [];

    abstract public function help();

    public function set_method(string $method): self {
        $this->method = $method;
        return $this;
    }

    public function set_params(array $params): self {
        $this->params = $params;
        return $this;
    }

    protected function param($key): ?string {
        if(isset($this->params[$key])) {
            return $this->params[$key];
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public function run(): void {
        $refObject = new ReflectionObject($this);
        if(!$refObject->hasMethod($this->method)) $this->method = 'help';
        $this->{$this->method}();
    }

    public function is_command() {
        return true;
    }
}