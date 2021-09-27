<?php

use Slim\App;

return function (App $app) {

    // Template
    $app->get('/template/table/{model}', \App\Service\ApiService::class . ':tableTemplate');
    $app->get('/template/detail/{model}', \App\Service\ApiService::class . ':detailTemplate');
    $app->get('/template/form/{model}', \App\Service\ApiService::class . ':formTemplate');

    // GET
    $app->get('/{model}/get', \App\Service\ApiService::class . ':get');
    $app->get('/{model}/get/{id}', \App\Service\ApiService::class . ':detail');
    $app->get('/{model}/selection', \App\Service\ApiService::class . ':selection');

    // POST
    $app->post('/{model}/add', \App\Service\ApiService::class . ':add');
    $app->post('/{model}/update/{id}', \App\Service\ApiService::class . ':update');
    $app->post('/{model}/delete/{id}', \App\Service\ApiService::class . ':delete');
    $app->post('/{model}/upload/file/{id}', \App\Service\ApiService::class . ':upload');

};

