<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\objects\Session;
use \Illuminate\Pagination\Paginator;
use Illuminate\Database\Capsule\Manager as DB;

final class MahasiswaController extends BaseController
{

    public function krs($request, $response, $args)
    {   
        $container = $this->container;
        $getData = $request->getQueryParams();
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $orang = $container->get('session')->get('orang');

        $mahasiswa = $mahasiswa_obj->where('orang_id', $orang->id)->without([
            'riwayat_belajar',
            'mahasiswa_bimbingan',
            'register_ulang',
            'khs'
        ])->first();

        $data = $mahasiswa->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function khs($request, $response, $args)
    {   
        $container = $this->container;
        $getData = $request->getQueryParams();
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $orang = $container->get('session')->get('orang');

        $mahasiswa = $mahasiswa_obj->where('orang_id', $orang->id)->without([
            'orang', 
            'jurusan', 
            'semester',
            'tahun_ajaran',
            'pengajuan_ks',
            'riwayat_belajar',
            'mahasiswa_bimbingan',
            'register_ulang',
        ])->first();

        $data = $mahasiswa->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function tagihan($request, $response, $args)
    {   
        $container = $this->container;
        $getData = $request->getQueryParams();
        $tagihan_obj = $this->get_object('tagihan');
        $orang = $container->get('session')->get('orang');

        $tagihan = $tagihan_obj->where('orang_id', $orang->id)->get();

        $data = $tagihan->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function cekKode($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();

        $mahasiswa_obj = $this->get_object('mahasiswa');
        $orang = $container->get('session')->get('orang');

        $mahasiswa = $mahasiswa_obj->where('orang_id', $orang->id)->first();

        $result = ["status" => "success"];

        if ($mahasiswa->tagihan->kode_pembayaran != $postData['kode']) {
            $result['status'] = 'fail';
        }


        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function krsDetail($request, $response, $args)
    {
        $container = $this->container;
        $mahasiswa_id = $args['mahasiswa_id'];
        $semester_id = $args['semester_id'];
        $pengajuan_ks_detail_obj = $this->get_object('pengajuan_ks_detail');

        $pengajuan_ks_detail = $pengajuan_ks_detail_obj
            ->whereHas('pengajuan_ks', function($q) use ($mahasiswa_id, $semester_id) {
                $q->where([['mahasiswa_id', $mahasiswa_id], ['semester_id', $semester_id]]);
            })
            ->leftJoin('riwayat_belajar_detail', function($join) use ($mahasiswa_id, $semester_id) {
                $join
                    ->on('riwayat_belajar_detail.mata_kuliah_id', '=', 'pengajuan_ks_detail.mata_kuliah_id')
                    ->whereIn('riwayat_belajar_id', function($q) use ($mahasiswa_id, $semester_id) {
                        $q->from('riwayat_belajar')->select('id')->where([['mahasiswa_id', $mahasiswa_id], ['semester_id', $semester_id]]);
                    });
            })
            ->leftJoin('mata_kuliah', 'mata_kuliah.id', '=', 'pengajuan_ks_detail.mata_kuliah_id')
            ->without('mata_kuliah');

        $data = [
            'data' => $pengajuan_ks_detail->get()
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


}
