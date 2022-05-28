<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Template extends BaseRoute {

    function register_route() {
        $this->app->get('/template/table/{object}', \App\Controllers\TemplateController::class . ':table');
        $this->app->get('/template/detail/{object}', \App\Controllers\TemplateController::class . ':detail');
        $this->app->get('/template/form/{object}', \App\Controllers\TemplateController::class . ':form');

        $this->app->get('/template/{folder_name}/{file}', \App\Controllers\TemplateController::class . ':dynamicTemplate');

        $this->app->get('/template/component/{component_name}', \App\Controllers\TemplateController::class . ':component');

        $this->app->get('/menu/view', \App\Controllers\TemplateController::class . ':menu');
     
        $this->app->get('/', \App\Controllers\TemplateController::class . ':home');
        $this->app->get('/login', \App\Controllers\TemplateController::class . ':login');

        $this->app->get('/register', \App\Controllers\TemplateController::class . ':register');
        $this->app->get('/register/online', \App\Controllers\TemplateController::class . ':registerOnline');
        $this->app->get('/register/offline', \App\Controllers\TemplateController::class . ':registerOffline');

        $this->app->post('/register/submit', \App\Controllers\TemplateController::class . ':registerSubmit');
        $this->app->get('/register/sukses', \App\Controllers\TemplateController::class . ':sukses');

        $this->app->get('/new', \App\Controllers\TemplateController::class . ':newTemplate');
    }

}

