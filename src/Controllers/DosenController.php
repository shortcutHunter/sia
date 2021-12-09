<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\objects\Session;
use \Illuminate\Pagination\Paginator;
use Illuminate\Database\Capsule\Manager as DB;

final class DosenController extends BaseController
{

    public function bimbingan($request, $response, $args)
    {   
        $container = $this->container;
        $getData = $request->getQueryParams();
        $karyawan_obj = $this->get_object('karyawan');
        $mahasiswa_bimbingan_obj = $this->get_object('mahasiswa_bimbingan');
        $orang = $container->get('session')->get('orang');
        
        $karyawan = $karyawan_obj->where('orang_id', $orang->id)->first();
        $mahasiswa_bimbingan = $mahasiswa_bimbingan_obj->whereHas('dosen_pa', function($q) use ($karyawan) {
            $q->where([['karyawan_id', '=', $karyawan->id], ['status', 'aktif']]);
        })->get();

        $data = json_encode($mahasiswa_bimbingan);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function mata_kuliah($request, $response, $args)
    {   
        $container = $this->container;
        $getData = $request->getQueryParams();
        $karyawan_obj = $this->get_object('karyawan');
        $mata_kuliah_diampuh_obj = $this->get_object('mata_kuliah_diampuh');
        $orang = $container->get('session')->get('orang');
        
        $karyawan = $karyawan_obj->where('orang_id', $orang->id)->first();
        $mata_kuliah_diampuh = $mata_kuliah_diampuh_obj->whereHas('dosen_pjmk', function($q) use ($karyawan) {
            $q->where([['karyawan_id', '=', $karyawan->id], ['status', 'aktif']]);
        })->get();

        $data = json_encode($mata_kuliah_diampuh);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
