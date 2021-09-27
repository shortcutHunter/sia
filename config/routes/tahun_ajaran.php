<?php

use Slim\App;

return function (App $app) {

    // PDF
    $app->get('/rekap/semua/semester', \App\Service\TahunAjaranService::class . ':rekapSemester');

};

