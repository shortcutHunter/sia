<?php

use Slim\App;

return function (App $app) {

    // PDF
    $app->get('/kartu/peserta/{id}', \App\Service\PmbService::class . ':kartuPeserta');

    $app->get('/pmb/get/status/baru', \App\Service\PmbService::class . ':pmbGetBaru');

};

