<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;
use App\Models\KaryawanModel;
use App\Models\OrangModel;
use App\Models\MahasiswaModel;
use App\Models\MahasiswaBimbinganModel;
use App\Models\MataKuliahModel;
use App\Models\MataKuliahDiampuhModel;
use App\Models\KonfigurasiNilaiModel;
use App\Models\PengajuanKsModel;
use App\Models\RelasiPengajuanKsModel;
use App\Models\KsModel;
use App\Models\SksModel;
use App\Models\NilaiMahasiswaModel;
use App\Models\NilaiModel;
use App\Models\PjmkModel;

final class DosenService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->karyawan = new KaryawanModel($container);
        $this->orang = new OrangModel($container);
        $this->mahasiswa = new MahasiswaModel($container);
        $this->mahasiswa_bimbingan = new MahasiswaBimbinganModel($container);
        $this->mata_kuliah = new MataKuliahModel($container);
        $this->mata_kuliah_diampuh = new MataKuliahDiampuhModel($container);
        $this->konfigurasi_nilai = new KonfigurasiNilaiModel($container);
        $this->pengajuan_ks = new PengajuanKsModel($container);
        $this->relasi_pengajuan_ks = new RelasiPengajuanKsModel($container);
        $this->ks = new KsModel($container);
        $this->sks = new SksModel($container);
        $this->nilai_mahasiswa = new NilaiMahasiswaModel($container);
        $this->nilai = new NilaiModel($container);
        $this->pjmk = new PjmkModel($container);
    }

// =============================================================================================================================
    // GET

    public function dosen(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $getData = $request->getQueryParams();
        $keyword = strtolower($getData['keyword']);
        $karyawan = $this->karyawan;
        $query = "orang_id IN (SELECT id FROM orang WHERE LOWER(nama) like '%".$keyword."%')";
        $karyawan->raw($query);

        $response->getBody()->write($karyawan->data->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function mahasiswa(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $getData = $request->getQueryParams();
        $keyword = strtolower($getData['keyword']);
        $mahasiswa = $this->mahasiswa;
        $query = "orang_id IN (SELECT id FROM orang WHERE LOWER(nama) like '%".$keyword."%')";        
        $mahasiswa->raw($query);
        // $mahasiswa->get();

        $response->getBody()->write($mahasiswa->data->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function matakuliah(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $getData = $request->getQueryParams();
        $mata_kuliah = $this->mata_kuliah;
        $keyword = strtolower($getData['keyword']);
        $query = "LOWER(nama) like '%".$keyword."%'";
        // $mata_kuliah->get();
        $mata_kuliah->raw($query);

        $response->getBody()->write($mata_kuliah->data->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function mahasiswaBimbingan(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $mahasiswa_bimbingan = $this->mahasiswa_bimbingan;
        $mahasiswa_bimbingan->get([['dosen_pa_id', $id]]);

        $response->getBody()->write($mahasiswa_bimbingan->data->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function dosenMatakuliah(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $mata_kuliah_diampuh = $this->mata_kuliah_diampuh;
        $mata_kuliah_diampuh->get([['pjmk_id', $id]]);

        $response->getBody()->write($mata_kuliah_diampuh->data->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function diampuhKonfigurasi(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $konfigurasi_nilai = $this->konfigurasi_nilai;
        $konfigurasi_nilai->get([['mata_kuliah_diampuh_id', $id]]);

        $response->getBody()->write($konfigurasi_nilai->data->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function diampuhMahasiswa(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $pjmk_id = $args['pjmk_id'];
        $sks = $this->sks;
        $ks = $this->ks;
        $pjmk = $this->pjmk;
        $mata_kuliah_diampuh = $this->mata_kuliah_diampuh;
        $nilai_mahasiswa = $this->nilai_mahasiswa;
        $data = [];

        $pjmk->read($pjmk_id);
        $pjmk_data = $pjmk->data;

        $ks->get([['status', 'aktif'], ['semester', $pjmk_data->semester]]);
        $ks_data = $ks->data;

        foreach ($ks_data as $value) {
            $sks->get([['ks_id', $value->id], ['mata_kuliah_id', $id]]);
            if (!$sks->data->isEmpty()) {
                $sks_data = $sks->data[0];
                $nilai_mahasiswa->get([["sks_id", $sks_data->id]]);

                $sks_data->mahasiswa = $value->mahasiswa;
                $sks_data->nilai_mahasiswa = $nilai_mahasiswa->data;

                array_push($data, $sks_data);
            }
        }
        $data = json_encode($data);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

// =============================================================================================================================
    // POST

    public function mahasiswaAdd(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $result = ['success' => true];
        $mahasiswa_bimbingan = $this->mahasiswa_bimbingan;
        $postData = $request->getParsedBody();

        try {
            $testing = [];
            foreach ($postData['mahasiswa_bimbingan'] as $value) {
                $value = [
                    'dosen_pa_id' => $postData['dosen_pa_id'],
                    'mahasiswa_id' => $value['id']
                ];

                $mahasiswa_bimbingan->get([ ['dosen_pa_id', $postData['dosen_pa_id']], ['mahasiswa_id', $value['mahasiswa_id']] ]);
                $is_exist = $mahasiswa_bimbingan->data;
                if ($is_exist->isEmpty()) {
                    $mahasiswa_bimbingan->create($value);
                }else{
                    throw new \Exception('mahasiswa sudah ada');
                }
            }
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function mahasiswaUpdatePengajuan(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $result = ['success' => true];
        
        // Model
        $mahasiswa = $this->mahasiswa;
        $pengajuan_ks = $this->pengajuan_ks;
        $relasi_pengajuan_ks = $this->relasi_pengajuan_ks;
        $ks = $this->ks;
        $sks = $this->sks;

        $mahasiswa->read($id);
        $mahasiswa_data = $mahasiswa->data;

        $pengajuan_ks->get([['mahasiswa_id', $id], ['status', 'prosess']]);
        $pengajuan_ks_data = $pengajuan_ks->data[0];

        $relasi_pengajuan_ks->get([['pengajuan_ks_id', $pengajuan_ks_data->id]]);
        $relasi_pengajuan_ks_data = $relasi_pengajuan_ks->data;

        $ks->get([['mahasiswa_id', $id], ['status', 'aktif']]);
        $ks_data = $ks->data[0];

        $postData = $request->getParsedBody();
        $status = $postData['status'];
        $pesan = $postData['alasan'];

        try {
            $pengajuan_ks->update($pengajuan_ks_data->id, ['status' => $status]);
            if ($status == 'tolak') {
                $val = [
                    'status' => 'tolak',
                    'pesan' => $pesan
                ];
                $relasi_ids = $relasi_pengajuan_ks_data->pluck('id');
                $relasi_pengajuan_ks->massUpdate($relasi_ids, $val);
                $mahasiswa->update($id, ['pengajuan' => false, 'ajukan_sks' => true]);
            }else{
                $relasi_pengajuan_ks_data = $relasi_pengajuan_ks_data->filter(function($item){return $item->status == 'terima';});
                foreach ($relasi_pengajuan_ks_data as $value) {
                    if (!$ks_data) {
                        $ks_value = [
                            "mahasiswa_id" => $id,
                            "semester" => $mahasiswa_data->tahun_ajaran->semester,
                            "status" => 'aktif'
                        ];
                        $ks->create($ks_value);
                        $ks_data = $ks->data;
                    }else{
                        $sks_value = [
                            "ks_id" => $ks_data->id,
                            "mata_kuliah_id" => $value->mata_kuliah_id,
                        ];
                        $sks->create($sks_value);
                    }
                }

                $mahasiswa->update($id, ['pengajuan' => false]);
            }

        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function matakuliahAdd(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $result = ['success' => true];
        $mata_kuliah_diampuh = $this->mata_kuliah_diampuh;
        $postData = $request->getParsedBody();

        try {
            foreach ($postData['mata_kuliah_diampuh'] as $value) {
                $value = [
                    'pjmk_id' => $postData['pjmk_id'],
                    'mata_kuliah_id' => $value['id']
                ];

                $mata_kuliah_diampuh->get([ ['pjmk_id', $value['pjmk_id']], ['mata_kuliah_id', $value['mata_kuliah_id']] ]);
                $is_exist = $mata_kuliah_diampuh->data;
                if ($is_exist->isEmpty()) {
                    $mata_kuliah_diampuh->create($value);
                }else{
                    throw new \Exception('mata kuliah sudah ada');
                }
            }
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function konfigurasiNilaiAdd(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $result = ['success' => true];
        $konfigurasi_nilai = $this->konfigurasi_nilai;
        $mata_kuliah_diampuh = $this->mata_kuliah_diampuh;
        $postData = $request->getParsedBody();
        $mata_kuliah_diampuh_id = $postData['mata_kuliah_diampuh_id'];

        try {
            foreach ($postData['konfigurasi'] as $key => $value) {
                $konfig_value = [
                    'mata_kuliah_diampuh_id' => $mata_kuliah_diampuh_id,
                    'nilai_id' => $key,
                    'persentase' => $value
                ];
                $konfigurasi_nilai->create($konfig_value);
            }
            $mata_kuliah_diampuh->update($mata_kuliah_diampuh_id, [
                'terkonfigurasi' => true
            ]);
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function isiNilaiSave(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $result = ['success' => true];
        $sks = $this->sks;
        $konfigurasi_nilai = $this->konfigurasi_nilai;
        $nilai_mahasiswa = $this->nilai_mahasiswa;
        $mata_kuliah_id = $args['id'];
        $diampuh_id = $args['diampuh_id'];

        $postData = $request->getParsedBody();
        $action = $postData['action'];
        $data_nilai = $postData['data_nilai'];
        $status = $action == 'save' ? 'prosess' : 'submit';

        $nilai_data = [];
        $konfigurasi_nilai->get([['mata_kuliah_diampuh_id', $diampuh_id]]);
        foreach ($konfigurasi_nilai->data as $value) {
            $nilai_data[$value->nilai_id] = $value;
        }

        $getMutu = function($tn) {
            $nm = 1;
            if ($tn >= 79) {
              $nm = 4;
            }
            else if ($tn >= 68) {
              $nm = 3;
            }
            else if ($tn >= 60) {
              $nm = 2;
            }
            else if ($tn >= 41) {
              $nm = 1;
            }
            return $nm;
        };

        $hitungNilai =  function($n, $n_id) use ($nilai_data) {
            $hasil = $n * $nilai_data[$n_id]->persentase / 100;
            return $hasil;
        };

        try {
            foreach ($data_nilai as $key => $value) {
                $sks->get([['id', $key], ['mata_kuliah_id', $mata_kuliah_id]]);
                $sks_data = $sks->data[0];
                $total_nilai = 0;

                // update nilai
                foreach ($value as $k => $v) {
                    $nilai_persentase = $hitungNilai($v, $k);
                    $total_nilai = $total_nilai + $nilai_persentase;
                    $nilai_value = [
                        'nilai' => $v,
                        'nilai_id' => $k,
                        'sks_id' => $sks_data->id,
                    ];
                    $nilai_mahasiswa->get([['sks_id', $sks_data->id], ['nilai_id', $k]]);
                    $nilai_mahasiswa_data = $nilai_mahasiswa->data;

                    if ($nilai_mahasiswa_data->isEmpty()) {
                        $nilai_mahasiswa->create($nilai_value);
                    }else{
                        $nilai_mahasiswa_data = $nilai_mahasiswa_data[0];
                        $nilai_mahasiswa->update($nilai_mahasiswa_data->id, $nilai_value);
                    }

                }

                // update SKS
                $nilai_mutu = $getMutu($total_nilai);
                $sks->update($sks_data->id, [
                    'nilai_mutu' => $nilai_mutu,
                    'total_nilai' => $total_nilai,
                    'status' => $status
                ]);
            }
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
