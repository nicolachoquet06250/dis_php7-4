<?php


namespace classes\commands;


use classes\helpers\Platform;
use traits\Command;
use traits\DIS;

class Count {
    use Command;
    use DIS;

    private ?Platform $platformHelper = null;

    const LINES = 1;
    const CLASSES = 0;

    protected array $directories = [
        __DIR__.'/../../../src',
        __DIR__.'/../../../scripts',
    ];

    protected function read_dir(string $directory, int &$n, int $type): void {
        $dir = opendir($directory);
        while (($elem = readdir($dir)) !== false)
            if($elem !== '.' && $elem !== '..'):
                if (is_file($directory . '/' . $elem))
                    switch($type) {
                        case self::CLASSES:
                            $n++;
                            break;
                        case self::LINES:
                            $content = file_get_contents($directory . '/' . $elem);
                            $n += count(explode("\n", $content));
                            break;
                        default:
                            break;
                    }
                else $this->read_dir($directory . '/' . $elem, $n, $type);
            endif;
    }

    public function number_of_classes() {
        $n = 0;
        foreach ($this->directories as $directory) $this->read_dir($directory, $n, self::CLASSES);

        echo $n." class".($n > 1 ? 'es' : '')." found !\n";
    }

    public function number_of_lines() {
        $n = 0;
        foreach ($this->directories as $directory) $this->read_dir($directory, $n, self::LINES);

        echo $n." line".($n > 1 ? 'es' : '')." found !\n";
    }

    public function help() {
        var_dump('HELP for '.__CLASS__.' command !');
    }
}