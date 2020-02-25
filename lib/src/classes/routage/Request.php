<?php

namespace classes\routage;

class Request {
    private array $params = [];
    private array $get = [];
    public function params(?array $params = null): ?array {
        if(is_null($params)) return $this->params;
        else $this->params = $params;
        return null;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function param($key) {
        if(isset($this->params[$key])) {
            return $this->params[$key];
        }
        return null;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function global($key) {
        if(isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        return null;
    }

    public function get(string $key, $val = null) {
        if(!is_null($val)) {
            $this->get[$key] = $val;
        }
        if(isset($this->get[$key])) {
            return $this->get[$key];
        }
        return null;
    }
}