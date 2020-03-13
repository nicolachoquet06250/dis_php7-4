<?php

namespace dis\core\classes\mvc;


class Controller {
    protected string $group_route;

    public function group_route(?string $group_route = null): string {
        if(!is_null($group_route)) {
            $this->group_route = $group_route;
        }
        return $this->group_route;
    }
}