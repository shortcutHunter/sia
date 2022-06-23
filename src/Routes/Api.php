<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Api extends BaseRoute {

    function register_route() {
        $this->app->get('/{object}/get', \App\Controllers\ApiController::class . ':get');
        $this->app->get('/{object}/get/{id}', \App\Controllers\ApiController::class . ':detail');
        $this->app->get('/{object}/selection', \App\Controllers\ApiController::class . ':selection');

        $this->app->get('/search/{object}', \App\Controllers\ApiController::class . ':search');
        $this->app->get('/matkul_diampuh/{matkul_diampuh_id}/get/mahasiswa', \App\Controllers\ApiController::class . ':getMahasiswa');

        $this->app->get('/tagihan/mahasiswa/setup_paket/{setup_paket_id}', \App\Controllers\ApiController::class . ':getTagihan');

        $this->app->get('/konfigurasi', \App\Controllers\ApiController::class . ':konfigurasi');

        $this->app->get('/rekap/semester', \App\Controllers\ApiController::class . ':rekapSemester');
        $this->app->get('/rekap/semester/{semester_id}', \App\Controllers\ApiController::class . ':rekapSemesterDetail');
        $this->app->post('/rekap/semester/mahasiswa', \App\Controllers\ApiController::class . ':rekapSemesterMahasiswa');
        $this->app->post('/rekap/semester/mahasiswa/lulus', \App\Controllers\ApiController::class . ':mahasiswaLulus');
        $this->app->post('/rekap/semester/mahasiswa/berhenti', \App\Controllers\ApiController::class . ':mahasiswaBerhenti');

        $this->app->post('/proses/nilai/mahasiswa', \App\Controllers\ApiController::class . ':prosesNilai');
        $this->app->post('/buat/tagihan/mahasiswa', \App\Controllers\ApiController::class . ':buatTagihan');

        $this->app->post('/{object}/add', \App\Controllers\ApiController::class . ':add');
        $this->app->post('/{object}/update', \App\Controllers\ApiController::class . ':massUpdate');
        $this->app->post('/{object}/update/{id}', \App\Controllers\ApiController::class . ':update');
        $this->app->post('/{object}/delete/{id}', \App\Controllers\ApiController::class . ':delete');

        $this->app->post('/login/submit', \App\Controllers\ApiController::class . ':login');
        $this->app->post('/lupa/password/submit', \App\Controllers\ApiController::class . ':lupaPassword');
        $this->app->post('/reset/password/submit', \App\Controllers\ApiController::class . ':resetPassword');
        $this->app->post('/logout', \App\Controllers\ApiController::class . ':logout');
     
        $this->app->get('/session', \App\Controllers\ApiController::class . ':session');
        $this->app->get('/menu', \App\Controllers\ApiController::class . ':menu');
    }

}

