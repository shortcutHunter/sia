<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Register extends BaseRoute {

    function register_route() {
        $this->app->get('/register', \App\Controllers\RegisterController::class . ':register');
        $this->app->get('/register/online', \App\Controllers\RegisterController::class . ':registerOnline');
        $this->app->get('/register/offline', \App\Controllers\RegisterController::class . ':registerOffline');

        $this->app->get('/register/sukses', \App\Controllers\RegisterController::class . ':registerSukses');

        $this->app->post('/register/check/nik', \App\Controllers\RegisterController::class . ':checkNIK');
        $this->app->post('/register/submit', \App\Controllers\RegisterController::class . ':registerSubmit');
    }

}

