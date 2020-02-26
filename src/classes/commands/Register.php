<?php


namespace app\classes\commands;


class Register extends \classes\commands\Register {
    protected static array $commands = [
        'my-command' => MyCommand::class,
        'generate' => Generate::class,
    ];
}