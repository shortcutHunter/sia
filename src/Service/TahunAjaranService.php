<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;
use App\Models\TahunAjaranModel;
use App\Models\MahasiswaModel;
use App\Models\DosenPaModel;
use App\Models\PjmkModel;
use App\Models\KsModel;
use App\Models\SksModel;

final class TahunAjaranService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->tahun_ajaran = new TahunAjaranModel($container);
        $this->mahasiswa = new MahasiswaModel($container);
        $this->dosen_pa = new DosenPaModel($container);
        $this->pjmk = new PjmkModel($container);
        $this->ks = new KsModel($container);
        $this->sks = new SksModel($container);
    }

    public function rekapSemester(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $result = ['success' => true];
        $tahun_ajaran = $this->tahun_ajaran;
        $mahasiswa = $this->mahasiswa;
        $dosen_pa = $this->dosen_pa;
        $pjmk = $this->pjmk;
        $ks = $this->ks;
        $sks = $this->sks;

        $tahun_ajaran->get();

        try {

            foreach ($tahun_ajaran->data as $value) {
                $semester = $value->semester;
                
                $mahasiswa->get([['tahun_ajaran_id', $value->id]]);
                $mahasiswa_ids = $mahasiswa->data->pluck('id')->toArray();
                $mahasiswa->massUpdate($mahasiswa_ids, ['register_ulang' => true]);
                
                $pjmk->get([['tahun_ajaran_id', $value->id]]);
                $pjmk_ids = $pjmk->data->pluck('id')->toArray();
                $pjmk->massUpdate($pjmk_ids, ['status' => 'nonaktif']);

                $dosen_pa->get([['tahun_ajaran_id', $value->id]]);
                $dosen_pa_ids = $dosen_pa->data->pluck('id')->toArray();
                $dosen_pa->massUpdate($dosen_pa_ids, ['status' => 'nonaktif']);

                $ks->get([['semester', $semester], ['status', 'aktif']]);
                $ks_data = $ks->data;
                foreach ($ks_data as $ks_val) {
                    $sks->get([['ks_id', $ks_val->id]]);
                    $total_sks = 0;
                    $nilai_ips = 0;
                    foreach ($sks->data as $sks_val) {
                        $mata_kuliah_sks = $sks_val->mata_kuliah->sks;
                        $total_sks += $mata_kuliah_sks;
                        $nilai_ips += ($mata_kuliah_sks * $sks_val->nilai_mutu);
                    }
                    $nilai_ips = number_format((float)($nilai_ips), 2, '.', '');
                    $ks->update($ks_val->id, ['status' => 'nonaktif', 'total_sks' => $total_sks, 'nilai_ips' => $nilai_ips]);
                }
                $tahun_ajaran->update($value->id, ['semester' => ($semester+1)]);
            }

        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
