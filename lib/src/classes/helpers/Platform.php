<?php


namespace dis\core\classes\helpers;


class Platform {
    private string $macOsX_platform = 'Darwin';

    private array $unix_platforms = [
        'CYGWIN_NT-5.1',
        'Darwin',
        'FreeBSD',
        'HP-UX',
        'IRIX64',
        'Linux',
        'NetBSD',
        'OpenBSD',
        'SunOS',
        'Unix',
    ];

    private array $windows_platforms = [
        'WIN32',
        'WINNT',
        'Windows'
    ];

    public function is_windows() {
        return in_array(PHP_OS, $this->windows_platforms);
    }

    public function is_unix() {
        return in_array(PHP_OS, $this->unix_platforms);
    }

    public function is_osX() {
        return PHP_OS === $this->macOsX_platform;
    }

    public function is_cli() {
        return strstr(php_sapi_name(), 'cli') !== false;
    }

    public function is_cgi() {
        return strstr(php_sapi_name(), 'cgi') !== false;
    }
}