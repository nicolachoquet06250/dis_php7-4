<?php


namespace dis\core\classes\commands;


use dis\core\classes\helpers\Platform;
use dis\core\traits\Command;
use dis\core\traits\DIS;

class Count {
    use Command;
    use DIS;

    private ?Platform $platformHelper = null;

    const CLASSES = 0;
    const LINES = 1;
    const TEMPLATES = 2;

    protected array $directories = [
        'src' => __DIR__.'/../../../src',
        'scripts' => __DIR__.'/../../../scripts',
        'classes' => __DIR__.'/../../../src/classes',
        'main' => __DIR__.'/../../../src/main',
        'interfaces' => __DIR__.'/../../../src/interfaces',
        'traits' => __DIR__.'/../../../src/traits',
        'templates' => __DIR__.'/../../../templates',
    ];

    protected function read_dir(string $directory, int &$n, int $type): void {
        $dir = opendir($directory);
        while (($elem = readdir($dir)) !== false)
            if($elem !== '.' && $elem !== '..'):
                if (is_file($directory . '/' . $elem))
                    switch($type) {
                        case self::CLASSES:
                            if(substr($elem, strlen($elem) - strlen('.php'), strlen('.php')) === '.php') {
                                $n++;
                            }
                            break;
                        case self::LINES:
                            $content = file_get_contents($directory . '/' . $elem);
                            $n += count(explode("\n", $content));
                            break;
                        case self::TEMPLATES:
                            if(substr($elem, strlen($elem) - strlen('.html'), strlen('.html')) === '.html') {
                                $n++;
                            }
                            break;
                        default:
                            break;
                    }
                else $this->read_dir($directory . '/' . $elem, $n, $type);
            endif;
    }

    public function sources() {
        $n = 0;
        $dirs = [
            $this->directories['src'] => self::CLASSES,
            $this->directories['scripts'] => self::CLASSES,
            $this->directories['templates'] => self::TEMPLATES,
        ];

        foreach ($dirs as $dir => $mode) $this->read_dir($dir, $n, $mode);

        echo $n." source".($n > 1 ? 's' : '')." found !\n";
    }

    public function classes() {
        $n = 0;
        $this->read_dir($this->directories['classes'], $n, self::CLASSES);
        $this->read_dir($this->directories['main'], $n, self::CLASSES);

        echo $n." class".($n > 1 ? 'es' : '')." found !\n";
    }

    public function lines() {
        $n = 0;
        $this->read_dir($this->directories['src'], $n, self::LINES);

        echo $n." line".($n > 1 ? 'es' : '')." found !\n";
    }

    public function scripts() {
        $n = 0;
        $this->read_dir($this->directories['scripts'], $n, self::CLASSES);

        echo $n." script".($n > 1 ? 'es' : '')." found !\n";
    }

    public function traits() {
        $n = 0;
        $this->read_dir($this->directories['traits'], $n, self::CLASSES);

        echo $n." trait".($n > 1 ? 'es' : '')." found !\n";
    }

    public function templates() {
        $n = 0;
        $this->read_dir($this->directories['templates'], $n, self::TEMPLATES);

        echo $n." template".($n > 1 ? 'es' : '')." found !\n";
    }

    public function help() {
        var_dump('HELP for '.__CLASS__.' command !');
    }
}