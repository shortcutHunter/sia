<?php

use Slim\App;

return function (App $app) {

    $app->get('/', \App\Service\RootService::class . ':root');

    // Base Template
    $app->get('/template/home', \App\Service\RootService::class . ':home');
    $app->get('/components/{view_name}', \App\Service\RootService::class . ':components');

    $app->get('/get/current/session', \App\Service\RootService::class . ':currentSession');

    $app->get('/register', \App\Service\RootService::class . ':register');
    $app->get('/register/sukses', \App\Service\RootService::class . ':registerSukses');
    $app->post('/register/submit', \App\Service\RootService::class . ':registerSubmit');

    $app->get('/login', \App\Service\RootService::class . ':login');
    $app->post('/login/submit', \App\Service\RootService::class . ':loginSubmit');

    $app->get('/inject/admin', \App\Service\RootService::class . ':injectAdmin');

    $app->get('/session/logout', \App\Service\RootService::class . ':logout');

};

