<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Mahasiswa extends BaseRoute {

    function register_route() {
        $this->app->get('/mahasiswa/krs', \App\Controllers\MahasiswaController::class . ':krs');
        $this->app->get('/mahasiswa/khs', \App\Controllers\MahasiswaController::class . ':khs');
        $this->app->get('/mahasiswa/tagihan', \App\Controllers\MahasiswaController::class . ':tagihan');
    }

}

