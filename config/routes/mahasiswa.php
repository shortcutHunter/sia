<?php

use Slim\App;

return function (App $app) {

    // Get
    $app->get('/mahasiswa/get/krs/{id}', \App\Service\MahasiswaService::class . ':krs');
    $app->get('/mahasiswa/get/khs/{id}', \App\Service\MahasiswaService::class . ':khs');
    $app->get('/mahasiswa/get/pengajuan_ks/{id}', \App\Service\MahasiswaService::class . ':pengajuan_ks');
    $app->get('/mahasiswa/get/register_ulang/{id}', \App\Service\MahasiswaService::class . ':register_ulang');

    $app->get('/registrasi_ulang/get/status/proses', \App\Service\MahasiswaService::class . ':regulangGetProses');

    $app->get('/mahasiswa/ks/{id}/get/sks', \App\Service\MahasiswaService::class . ':getSks');

    $app->get('/orang/{id}/get/user', \App\Service\MahasiswaService::class . ':user');
    $app->get('/orang/{id}/get/mahasiswa', \App\Service\MahasiswaService::class . ':getMahasiswa');
    $app->get('/orang/{id}/get/dosen', \App\Service\MahasiswaService::class . ':getDosen');

    $app->get('/mata_kuliah/get/{semester}/{id}', \App\Service\MahasiswaService::class . ':mata_kuliah');

    $app->get('/cetak/khs/{id}', \App\Service\MahasiswaService::class . ':cetakKhs');
    $app->get('/cetak/krs/{id}', \App\Service\MahasiswaService::class . ':cetakKrs');

    // Post
    $app->post('/pengajuan/krs/{id}', \App\Service\MahasiswaService::class . ':pengajuan_krs');
    $app->post('/mahasiswa/register_ulang/{id}/upload', \App\Service\MahasiswaService::class . ':register_ulang_upload');
    $app->post('/mahasiswa/{id}/cek/kode', \App\Service\MahasiswaService::class . ':cek_kode_verifkiasi');

    $app->post('/import/data/mahasiswa/csv', \App\Service\MahasiswaService::class . ':importMahasiswa');

};

