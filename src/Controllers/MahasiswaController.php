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

    public function buatTagihan($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();
        $result = ["status" => "fail"];

        $pta_obj = $this->get_object('pembiayaan_tahun_ajar');
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $pta_id = $postData['pembiayaan_tahun_ajar'];

        $pta = $pta_obj->find($pta_id);

        if (!empty($pta)) {
            $mahasiswa = $mahasiswa_obj->where([
                ['tahun_ajaran_id', $pta->tahun_ajaran_id],
                ['semester_id', $pta->semester_id],
                ['status', 'mahasiswa']
            ])->get();

            foreach ($mahasiswa as $key => $value) {
                $tagihan = $pta->createTagihan($value->orang_id);
            }

            $result['status'] = 'success';
        }


        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function buatTagihanLainnya($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();
        $result = ["status" => "fail"];

        $pta_obj = $this->get_object('pembiayaan_tahun_ajar');
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $pta_id = $postData['pembiayaan_tahun_ajar'];
        $mahasiswa_ids = $postData['mahasiswa'];

        $pta = $pta_obj->find($pta_id);

        if (!empty($pta)) {
            $mahasiswa = $mahasiswa_obj->whereIn('id', $mahasiswa_ids)->get();

            foreach ($mahasiswa as $key => $value) {
                $tagihan = $pta->createTagihan($value->orang_id);
            }

            $result['status'] = 'success';
        }


        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function terbitkanNim($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();
        $result = ["status" => "fail"];

        $pendaftaran_obj = $this->get_object('pendaftaran');
        $pendaftaran_id = $postData['pendaftaran_id'];

        $pendaftaran = $pendaftaran_obj->find($pendaftaran_id);

        if (!empty($pendaftaran)) {
            
            $pendaftaran->terbitkanNIM();
            $result['status'] = 'success';
        }


        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function pmbBaru($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();

        $pmb_obj = $this->get_object('pmb');
        $orang = $container->get('session')->get('orang');

        $pmb = $pmb_obj->where('orang_id', $orang->id)->first();

        $data = json_encode([
            'data' => $pmb
        ]);
        
        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function gantiSemester($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();
        $result = ["status" => "fail"];

        $mahasiswa_obj = $this->get_object('mahasiswa');
        $tahun_ajaran_obj = $this->get_object('tahun_ajaran');
        $pta_obj = $this->get_object('pembiayaan_tahun_ajar');

        $semester_id = $postData['semester'];
        $semester_baru = $postData['semester_baru'];
        $tahun_ajaran_id = $postData['tahun_ajaran_id'];

        $mahasiswas = $mahasiswa_obj->whereIn('id', $postData['mahasiswa'])->get();
        $tahun_ajaran = $tahun_ajaran_obj->find($postData['tahun_ajaran_id']);
        $pta = $pta_obj->where([
            ['tahun_ajaran_id', $tahun_ajaran_id],
            ['semester_id', $semester_baru]
        ])->first();

        foreach ($mahasiswas as $mahasiswa) {
            $riwayat_belajar_obj = $this->get_object('riwayat_belajar');
            $mahasiswa_id = $mahasiswa->id;
            $riwayat_belajar = $riwayat_belajar_obj
                ->where([
                    ['semester_id', $semester_id],
                    ['status', 'aktif'],
                    ['mahasiswa_id', $mahasiswa_id]
                ])
                ->first();

            if (!empty($riwayat_belajar)) {
                $riwayat_belajar->nilaiMahasiswa($semester_id);
            }

            // $mahasiswa->buatTagihanMahasiswa($pta->id);
            $mahasiswa->update([
                'semester_id' => $semester_baru,
                'reg_ulang' => true,
                'ajukan_sks' => false,
                'pengajuan' => false,
                'sudah_pengajuan' => false
            ]);
        }

        
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function lulusMahasiswa($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();
        $result = ["status" => "success"];

        $mahasiswa_obj = $this->get_object('mahasiswa');
        $alumni_obj = $this->get_object('alumni');
        $riwayat_belajar_obj = $this->get_object('riwayat_belajar');

        $mahasiswa_id = $postData['mahasiswa_id'];

        $mahasiswa = $mahasiswa_obj->find($mahasiswa_id);
        $mahasiswa->update([
            'status' => 'alumni',
            'reg_ulang' => false,
            'ajukan_sks' => false,
            'pengajuan' => false,
            'sudah_pengajuan' => false
        ]);

        $riwayat_belajar = $riwayat_belajar_obj
            ->where([
                ['status', 'aktif'],
                ['mahasiswa_id', $mahasiswa->id]
            ])
        ->get();

        if ($riwayat_belajar->count() > 0) {
            foreach ($riwayat_belajar as $key => $value) {
                $value->nilaiMahasiswa($value->semester_id);
            }
        }

        $alumni = $alumni_obj->where('nim', $mahasiswa->nim)->first();
        $alumni_value = [
            "nama" => $mahasiswa->orang->nama,
            "nim" => $mahasiswa->nim,
            "tahun_lulus" => date('Y')
        ];

        if (empty($alumni)) {
            $alumni = $alumni_obj->create($alumni_value);
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function doMahasiswa($request, $response, $args)
    {   
        $container = $this->container;
        $postData = $request->getParsedBody();
        $result = ["status" => "success"];

        $mahasiswa_obj = $this->get_object('mahasiswa');
        $riwayat_belajar_obj = $this->get_object('riwayat_belajar');

        $mahasiswa_id = $postData['mahasiswa_id'];

        $mahasiswa = $mahasiswa_obj->find($mahasiswa_id);
        $mahasiswa->update([
            'status' => 'dropout',
            'reg_ulang' => false,
            'ajukan_sks' => false,
            'pengajuan' => false,
            'sudah_pengajuan' => false
        ]);

        $riwayat_belajar = $riwayat_belajar_obj
            ->where([
                ['status', 'aktif'],
                ['mahasiswa_id', $mahasiswa->id]
            ])
        ->get();

        if ($riwayat_belajar->count() > 0) {
            foreach ($riwayat_belajar as $key => $value) {
                $value->nilaiMahasiswa($value->semester_id);
            }
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function dataAlumni($request, $response, $args)
    {   
        $container = $this->container;
        $getData = $request->getQueryParams();
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $alumni_obj = $this->get_object('alumni');

        $orang = $container->get('session')->get('orang');

        $mahasiswa = $mahasiswa_obj->where('orang_id', $orang->id)->first();
        $alumni = $alumni_obj->where('nim', $mahasiswa->nim)->first();

        $data = [
            'data' => $alumni
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
