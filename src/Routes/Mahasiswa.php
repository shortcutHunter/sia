<?php

namespace App\Routes;

use App\Routes\BaseRoute;

class Mahasiswa extends BaseRoute {

    function register_route() {
        $this->app->get('/mahasiswa/krs', \App\Controllers\MahasiswaController::class . ':krs');
        $this->app->get('/mahasiswa/khs', \App\Controllers\MahasiswaController::class . ':khs');
        $this->app->get('/mahasiswa/tagihan', \App\Controllers\MahasiswaController::class . ':tagihan');

        $this->app->get('/krs/{mahasiswa_id}/semester/{semester_id}', \App\Controllers\MahasiswaController::class . ':krsDetail');

        $this->app->get('/pmb/baru', \App\Controllers\MahasiswaController::class . ':pmbBaru');
        
        $this->app->post('/mahasiswa/cek/kode', \App\Controllers\MahasiswaController::class . ':cekKode');

        $this->app->post('/buat/tagihan/pta', \App\Controllers\MahasiswaController::class . ':buatTagihan');
        $this->app->post('/buat/tagihan/lainnya/pta', \App\Controllers\MahasiswaController::class . ':buatTagihanLainnya');
    
        $this->app->post('/terbitkan/nim', \App\Controllers\MahasiswaController::class . ':terbitkanNim');

        $this->app->post('/tahun_ajaran/ganti/semester', \App\Controllers\MahasiswaController::class . ':gantiSemester');
    }

}

