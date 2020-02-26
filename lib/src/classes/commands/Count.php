<?php


namespace classes\commands;


use traits\Command;

class Count {
    use Command;

    public function number_of_classes() {
        $directories = [
            __DIR__.'/../../../src',
            __DIR__.'/../../../scripts',
        ];

        function read_dir($directory, &$n) {
            $dir = opendir($directory);
            while (($elem = readdir($dir)) !== false) {
                if($elem !== '.' && $elem !== '..') {
                    if (is_file($directory . '/' . $elem)) {
                        $n++;
                    } else {
                        read_dir($directory . '/' . $elem, $n);
                    }
                }
            }
        }

        $n = 0;
        foreach ($directories as $directory) {
            read_dir($directory, $n);
        }

        echo $n." class".($n > 1 ? 'es' : '')." found !\n";
    }

    public function number_of_lines() {
        $directories = [
            __DIR__.'/../../../src',
            __DIR__.'/../../../scripts',
        ];

        function read_dir($directory, &$n) {
            $dir = opendir($directory);
            while (($elem = readdir($dir)) !== false) {
                if($elem !== '.' && $elem !== '..') {
                    if (is_file($directory . '/' . $elem)) {
                        $content = file_get_contents($directory . '/' . $elem);
                        $n += count(explode("\n", $content));
                    } else {
                        read_dir($directory . '/' . $elem, $n);
                    }
                }
            }
        }

        $n = 0;
        foreach ($directories as $directory) {
            read_dir($directory, $n);
        }

        echo $n." line".($n > 1 ? 'es' : '')." found !\n";
    }

    public function help() {
        var_dump('HELP for '.__CLASS__.' command !');
    }
}