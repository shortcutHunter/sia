<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Reports extends BaseRoute {

    function register_route() {
        $this->app->get('/report/kartu_peserta/{pmb_id}', \App\Controllers\ReportController::class . ':kartu_peserta');

        $this->app->get('/report/transkrip/{mahasiswa_id}', \App\Controllers\ReportController::class . ':transkrip');
        $this->app->get('/report/krs/{mahasiswa_id}', \App\Controllers\ReportController::class . ':krs');
        $this->app->get('/report/khs/{khs_id}', \App\Controllers\ReportController::class . ':khs');


        $this->app->get('/report/mahasiswa/mata_kuliah/{mata_kuliah_diampuh_id}', \App\Controllers\ReportController::class . ':mahasiswa');
        $this->app->get('/report/nilai/mata_kuliah/{mata_kuliah_diampuh_id}', \App\Controllers\ReportController::class . ':nilai');


        $this->app->get('/report/dosen/pjmk', \App\Controllers\ReportController::class . ':dosenPJMK');
        $this->app->get('/report/dosen/pa/{dosen_pa_id}/mahasiswa/bimbingan', \App\Controllers\ReportController::class . ':mahasiswaBimbingan');

        $this->app->get('/report/tagihan/mahasiswa', \App\Controllers\ReportController::class . ':tagihanMahasiswa');
        $this->app->get('/report/tagihan/mahasiswa/{mahasiswa_id}', \App\Controllers\ReportController::class . ':tagihanMahasiswaId');

        $this->app->get('/report/mahasiswa/user_login/{pendaftaran_id}', \App\Controllers\ReportController::class . ':userLoginMahasiswa');

        $this->app->get('/send/login/detail', \App\Controllers\ReportController::class . ':loginDetail');
    }

}

