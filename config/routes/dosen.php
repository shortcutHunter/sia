<?php

use Slim\App;

return function (App $app) {

    // Get
    $app->get('/get/karawan/dosen_pa', \App\Service\DosenService::class . ':dosen');
    $app->get('/get/karawan/mahasiswa', \App\Service\DosenService::class . ':mahasiswa');
    $app->get('/get/karawan/matakuliah', \App\Service\DosenService::class . ':matakuliah');
    $app->get('/get/karyawan/{id}/bimbingan', \App\Service\DosenService::class . ':mahasiswaBimbingan');
    $app->get('/get/karyawan/{id}/matakuliah', \App\Service\DosenService::class . ':dosenMatakuliah');
    $app->get('/get/karyawan/diampuh/{id}/konfigurasi', \App\Service\DosenService::class . ':diampuhKonfigurasi');
    $app->get('/dosen_pjmk/{pjmk_id}/mata_kuliah/{id}/mahasisa', \App\Service\DosenService::class . ':diampuhMahasiswa');

    $app->post('/dosen_pa/mahasiswa_bimbingan/add', \App\Service\DosenService::class . ':mahasiswaAdd');
    $app->post('/dosen_pa/pengajuan/{id}/updateAll', \App\Service\DosenService::class . ':mahasiswaUpdatePengajuan');
    $app->post('/dosen_pjmk/mata_kuliah/add', \App\Service\DosenService::class . ':matakuliahAdd');
    $app->post('/dosen_pjmk/konfigurasi_nilai/add', \App\Service\DosenService::class . ':konfigurasiNilaiAdd');
    $app->post('/dosen_pjmk/{diampuh_id}/mata_kuliah/{id}/isi_nilai/save', \App\Service\DosenService::class . ':isiNilaiSave');

};

