<?php

namespace App\Routes;

use Slim\App;

class BaseRoute {

    function __construct(App $app) {
        $this->app = $app;
    }

    function register_route() {
        return true;
    }

}


