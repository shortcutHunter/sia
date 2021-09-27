<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Components extends BaseRoute {

    function register_route() {
        $this->app->get('/components/{view_name}', \App\Controllers\ComponentController::class . ':components');
    }

}

