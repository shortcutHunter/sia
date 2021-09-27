<?php

use Slim\App;

return function (App $app) {

    // Get
    $app->get('/paket/{id}/item', \App\Service\paketService::class . ':paketItem');

    $app->get('/paket_register/get/konfigurasi/data', \App\Service\paketService::class . ':paketKonfigurasi');
    $app->post('/paket_register/update/konfigurasi/register', \App\Service\paketService::class . ':updatePaketKonfigurasi');

    $app->post('/paket/item/custom/tambah', \App\Service\paketService::class . ':paketTambah');

    $app->post('/paket/{id}/item/custom/edit', \App\Service\paketService::class . ':paketEdit');

};

