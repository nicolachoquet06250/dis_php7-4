<?php

namespace classes\commands;

class Register {
    private static bool $is_help = false;
    private static array $base_commands = [
        'help' => Help::class,
        'generate' => Generate::class,
    ];
    protected static array $commands = [];

    static final function init(): void {
        $_commands = [];
        foreach (static::$base_commands as $k => $v) $_commands[$k] = $v;
        foreach (static::$commands as $k => $v) $_commands[$k] = $v;
        static::$commands = $_commands;
    }

    public static function commands(): array {
        return static::$commands;
    }

    public static function command(string $name): string {
        if(isset(static::commands()[$name])) {
            static::$is_help = false;
            return static::commands()[$name];
        } else {
            static::$is_help = true;
            return static::commands()['help'];
        }
    }

    public static function is_help(): bool {
        return static::$is_help;
    }
}