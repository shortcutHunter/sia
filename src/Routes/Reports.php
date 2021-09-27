<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Reports extends BaseRoute {

    function register_route() {
        $this->app->get('/report/kartu_peserta/{pmb_id}', \App\Controllers\ReportController::class . ':kartu_peserta');
    }

}

