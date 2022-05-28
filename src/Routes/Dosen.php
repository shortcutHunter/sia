<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Dosen extends BaseRoute {

    function register_route() {
        $this->app->get('/dosen/mahasiswa/bimbingan', \App\Controllers\DosenController::class . ':bimbingan');
        $this->app->get('/dosen/mahasiswa/mata_kuliah', \App\Controllers\DosenController::class . ':mata_kuliah');

        $this->app->post('/migrasi/dosen/pa', \App\Controllers\DosenController::class . ':migrasiPA');
        $this->app->post('/migrasi/dosen/pjmk', \App\Controllers\DosenController::class . ':migrasiPjmk');
    }

}

