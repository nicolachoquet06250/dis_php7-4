<?php

namespace dis\core\classes\routage;


class Response {
    private array $error_messages = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not found',
        405 => 'Method Not Allowed',
        407 => 'Proxy Authentication Required',

        498 => 'Token expired/invalid',

        500 => 'Internal Server Error',
        501 => 'Not implemented',
        503 => 'Service Unavailable',
        520 => 'Unknown Error'
    ];
    private array $vars = [];
    private ?array $error = null;

    private function is_error() {
        return !empty($this->error);
    }

    public function assign($var, $val): Response {
        $this->vars[$var] = $val;
        return $this;
    }
    public function render($tpl, array $params = []): string {
        if($this->is_error()) {
            $params = array_merge($params, [
                'error.code' => $this->error['code'],
                'error.message' => $this->error['message'],
            ]);
        }
        if(file_exists($tpl)) {
            $vars = array_merge($this->vars, $params);
            $file = file_get_contents($tpl);

            foreach ($vars as $var => $val) {
                $file = str_replace('{'.$var.'}', $val, $file);
            }
            return $file;
        }
        return '';
    }
    public function json($_ = null): string {
        if($this->is_error()) {
            return json_encode($this->error);
        }
        $tmp = [];
        foreach ($_ as $k => $v) {
            foreach ($this->vars as $var => $val) {
                $k = str_replace('{'.$var.'}', $val, $k);
                $v = str_replace('{'.$var.'}', $val, $v);
            }
            $tmp[$k] = $v;
        }
        return json_encode($tmp);
    }
    public function xml($_ = null): string {
        return $this->html($_);
    }
    public function html($_ = null): string {
        if($this->is_error()) {
            return '<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8" />
            <title>Erreur '.$this->error['code'].'</title>
        </head>
        <body>
            <h1>Erreur '.$this->error['code'].'</h1>
            <p>'.$this->error['message'].'</p>
        </body>
    </html>';
        }
        return $_;
    }

    public function error(int $code, ?string $message = null): Response {
        header('HTTP/1.0 '.$code.' '.($message ?? $this->error_messages[$code]));
        $this->error = [
            'code' => $code,
            'message' => $message ?? $this->error_messages[$code],
        ];
        return $this;
    }
}