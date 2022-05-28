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

    public function migrasiPA($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();
        $result = ["status" => "fail"];


        $karyawan_baru_id = $postData['karyawan_baru'];
        $dosen_pa_id = $postData['dosen_pa_lama'];

        $dosen_pa_obj = $this->get_object('dosen_pa');
        $dosen_pa = $dosen_pa_obj->where('karyawan_id', $karyawan_baru_id)->first();

        $dosen_pa_obj = $this->get_object('dosen_pa');
        $dosen_pa_lama = $dosen_pa_obj->where('id', $dosen_pa_id)->first();

        if (empty($dosen_pa)) {
            $dosen_pa = $dosen_pa_obj->create([
                'karyawan_id' => $karyawan_baru_id,
                'tahun_ajaran_id' => $dosen_pa_lama->tahun_ajaran_id,
                'status' => 'aktif'
            ]);
        }

        $mahasiswa_bimbingan_obj = $this->get_object('mahasiswa_bimbingan');
        $mahasiswa_bimbingan = $mahasiswa_bimbingan_obj->where('dosen_pa_id', $dosen_pa_id);
        $mahasiswa_bimbingan->update(['dosen_pa_id' => $dosen_pa->id]);

        $result['status'] = 'success';

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function migrasiPjmk($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();
        $result = ["status" => "fail"];


        $karyawan_baru_id = $postData['karyawan_baru'];
        $dosen_pjmk_id = $postData['dosen_pjmk_lama'];

        $dosen_pjmk_obj = $this->get_object('dosen_pjmk');
        $dosen_pjmk = $dosen_pjmk_obj->where('karyawan_id', $karyawan_baru_id)->first();

        $dosen_pjmk_obj = $this->get_object('dosen_pjmk');
        $dosen_pjmk_lama = $dosen_pjmk_obj->where('id', $dosen_pjmk_id)->first();

        if (empty($dosen_pjmk)) {
            $dosen_pjmk = $dosen_pjmk_obj->create([
                'karyawan_id' => $karyawan_baru_id,
                'tahun_ajaran_id' => $dosen_pjmk_lama->tahun_ajaran_id,
                'semester' => $dosen_pjmk_lama->semester->pluck('id')->toArray(),
                'status' => 'aktif'
            ]);
        }

        $mata_kuliah_diampuh_obj = $this->get_object('mata_kuliah_diampuh');
        $mata_kuliah_diampuh = $mata_kuliah_diampuh_obj->where('dosen_pjmk_id', $dosen_pjmk_id);
        $mata_kuliah_diampuh->update(['dosen_pjmk_id' => $dosen_pjmk->id]);

        $result['status'] = 'success';

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
