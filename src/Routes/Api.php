<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Api extends BaseRoute {

    function register_route() {
        $this->app->get('/{object}/get', \App\Controllers\ApiController::class . ':get');
        $this->app->get('/{object}/get/{id}', \App\Controllers\ApiController::class . ':detail');
        $this->app->get('/{object}/selection', \App\Controllers\ApiController::class . ':selection');

        $this->app->get('/search/{object}', \App\Controllers\ApiController::class . ':search');

        $this->app->post('/{object}/add', \App\Controllers\ApiController::class . ':add');
        $this->app->post('/{object}/update', \App\Controllers\ApiController::class . ':massUpdate');
        $this->app->post('/{object}/update/{id}', \App\Controllers\ApiController::class . ':update');
        $this->app->post('/{object}/delete/{id}', \App\Controllers\ApiController::class . ':delete');

        $this->app->post('/login/submit', \App\Controllers\ApiController::class . ':login');
     
        $this->app->get('/session', \App\Controllers\ApiController::class . ':session');
        $this->app->get('/menu', \App\Controllers\ApiController::class . ':menu');
    }

}

