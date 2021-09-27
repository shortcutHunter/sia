<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;
use App\Models\MahasiswaModel;
use App\Models\KsModel;
use App\Models\PengajuanKsModel;
use App\Models\RegisterUlangModel;
use App\Models\RelasiPengajuanKsModel;
use App\Models\MataKuliahModel;
use App\Models\SksModel;
use App\Models\UserModel;
use App\Models\KaryawanModel;
use App\Models\PjmkModel;
use App\Models\DosenPaModel;

final class MahasiswaService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mahasiswa = new MahasiswaModel($container);
        $this->ks = new KsModel($container);
        $this->pengajuan_ks = new PengajuanKsModel($container);
        $this->register_ulang = new RegisterUlangModel($container);
        $this->mata_kuliah = new MataKuliahModel($container);
        $this->relasi_pengajuan_ks = new RelasiPengajuanKsModel($container);
        $this->sks = new SksModel($container);
        $this->user = new UserModel($container);
        $this->dosen = new KaryawanModel($container);
        $this->pjmk = new PjmkModel($container);
        $this->dosen_pa = new DosenPaModel($container);
    }

    public function krs(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $ks = $this->ks;
        $sks = $this->sks;
        
        $ks->get([['status', 'aktif'], ['mahasiswa_id', $id]]);
        $ks_data = $ks->data[0];

        $sks->get([['ks_id', $ks_data->id]]);
        $data = $sks->data->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function khs(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $ks = $this->ks;
        $ks->get([['status', 'nonaktif'], ['mahasiswa_id', $id]]);
        $data = $ks->data->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function pengajuan_ks(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $pengajuan_ks = $this->pengajuan_ks;
        $mahasiswa = $this->mahasiswa;
        $relasi_pengajuan_ks = $this->relasi_pengajuan_ks;
        $mahasiswa->read($id);
        $pengajuan_ks->get([['mahasiswa_id', $id], ['semester', $mahasiswa->data->tahun_ajaran->semester], ['status', 'prosess']]);
        $pengajuan_ks_id = $pengajuan_ks->data[0]->id;

        $relasi_pengajuan_ks->get([['pengajuan_ks_id', $pengajuan_ks_id]]);
        $data = $relasi_pengajuan_ks->data;

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function register_ulang(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $register_ulang = $this->register_ulang;
        $register_ulang->get([['mahasiswa_id', $id]]);
        $data = $register_ulang->data->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function regulangGetProses(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $id = $args['id'];
        $register_ulang = $this->register_ulang;
        $register_ulang->get([['status', 'proses']]);
        $data = $register_ulang->data->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getSks(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $sks = $this->sks;
        $sks->get([['ks_id', $id]]);
        $data = $sks->data->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function register_ulang_upload(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $result = ['success' => true];
        $id = $args['id'];
        $register_ulang = $this->register_ulang;
        $mahasiswa = $this->mahasiswa;
        $mahasiswa->read($id);

        $postData = $request->getParsedBody();
        $bukti_pembayaran = $request->getUploadedFiles();

        $postData['mahasiswa_id'] = $id;

        try {
            $register_ulang->create($postData);
            $file_dir = "register_ulang/".$register_ulang->data->id;
            foreach ($bukti_pembayaran as $key => $value) {
                $this->container->get('moveUploadedFile')($file_dir, $value, $value->getClientFilename());
            }            
            $mahasiswa->update($id, ['register_ulang' => false]);
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function mata_kuliah(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $semester = $args['semester'];
        $mata_kuliah = $this->mata_kuliah;
        $relasi_pengajuan_ks = $this->relasi_pengajuan_ks;
        $pengajuan_ks = $this->pengajuan_ks;

        $mata_kuliah->get([['semester', $semester]]);
        $mata_kuliah_data = $mata_kuliah->data;

        $pengajuan_ks->get([['mahasiswa_id', $id], ['semester', $semester], ['status', '!=', 'tolak']]);
        $mata_kuliah_id = [];

        foreach ($pengajuan_ks->data->pluck('id') as $value) {
            $relasi_pengajuan_ks->get([['pengajuan_ks_id', $id]]);
            array_merge($mata_kuliah_id, $relasi_pengajuan_ks->data->pluck('mata_kuliah_id'));
        }

        $mata_kuliah_data = $mata_kuliah_data->filter(function($item){
            return !in_array($item->id, $mata_kuliah_id);
        });

        
        $response->getBody()->write($mata_kuliah_data->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function user(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $user = $this->user;
        $user->get([['orang_id', $id]]);
        $user_data = $user->data[0];
        $user_data = json_encode($user_data);
        
        $response->getBody()->write($user_data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getMahasiswa(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $mahasiswa = $this->mahasiswa;
        $mahasiswa->get([['orang_id', $id]]);
        $mahasiswa_data = $mahasiswa->data[0];
        $mahasiswa_data = json_encode($mahasiswa_data);
        
        $response->getBody()->write($mahasiswa_data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getDosen(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $dosen = $this->dosen;
        $pjmk = $this->pjmk;
        $dosen_pa = $this->dosen_pa;

        $dosen->get([['orang_id', $id]]);
        $dosen_data = $dosen->data[0];

        $pjmk->get([['karyawan_id', $dosen_data->id], ['status', 'aktif']]);
        $pjmk_data = $pjmk->data[0];

        $dosen_pa->get([['karyawan_id', $dosen_data->id], ['status', 'aktif']]);
        $dosen_pa_data = $dosen_pa->data[0];

        $dosen_data->dosen_pa = $dosen_pa_data;
        $dosen_data->pjmk = $pjmk_data;

        $dosen_data = json_encode($dosen_data);
        
        $response->getBody()->write($dosen_data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function pengajuan_krs(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $result = ['success' => true];
        $id = $args['id'];
        $pengajuan_ks = $this->pengajuan_ks;
        $relasi_pengajuan_ks = $this->relasi_pengajuan_ks;
        $mahasiswa = $this->mahasiswa;
        $mahasiswa->read($id);

        $postData = $request->getParsedBody();

        try {
            $pengajuan_ks_value = [
                'mahasiswa_id' => $id,
                'tahun_ajaran_id' => $mahasiswa->data->tahun_ajaran_id,
                'semester' => $mahasiswa->data->tahun_ajaran->semester,
                'status' => "prosess",
            ];
            $pengajuan_ks->create($pengajuan_ks_value);

            foreach ($postData as $key => $value) {
                $relasi_pengajuan_ks->create([
                    'pengajuan_ks_id' => $pengajuan_ks->data->id,
                    'mata_kuliah_id' => $value,
                ]);
            }
            $mahasiswa->update($id, ['ajukan_sks' => false, 'pengajuan' => true]);
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function cetakKhs(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $ks = $this->ks;
        $sks = $this->sks;

        $ks->read($id);
        $ks_data = $ks->data;

        $sks->get([['ks_id', $ks_data->id]]);
        $sks_data = $sks->data;

        if ($ks_data->mahasiswa->orang->tanggal_lahir) {
            $tgl_lahir = strtotime($ks_data->mahasiswa->orang->tanggal_lahir);
            $tgl_lahir = date('d-m-Y', $tgl_lahir);
        }else{
            $tgl_lahir = '';
        }

        $value = [
            'ks' => $ks_data,
            'tgl_lahir' => $tgl_lahir,
            'sks' => $sks_data,
        ];

        $pdfContent = $this->container->get('renderPDF')("html_templates/khs.phtml", $value);
        $response->getBody()->write($pdfContent);

        return $response->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="Kartu Peserta '.$orang->nik.' '.$orang->nama.'.pdf"')
            ->withStatus(201);
    }

    public function cetakKrs(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $mahasiswa = $this->mahasiswa;
        $ks = $this->ks;
        $sks = $this->sks;

        $mahasiswa->read($id);
        $mahasiswa_data = $mahasiswa->data;

        $postData = $request->getParsedBody();

        $ks->get([['mahasiswa_id', $mahasiswa_data->id], ['status', 'aktif']]);
        $ks_data = $ks->data[0];

        $sks->get([['ks_id', $ks_data->id]]);
        $sks_data = $sks->data;

        $value = [
            'mahasiswa' => $mahasiswa_data,
            'sks' => $sks_data
        ];

        $pdfContent = $this->container->get('renderPDF')("html_templates/krs.phtml", $value);
        $response->getBody()->write($pdfContent);
                
        return $response->withHeader('Content-Type', 'application/pdf')
            ->withStatus(201);
    }

    public function cek_kode_verifkiasi(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $result = ['success' => true];
        $id = $args['id'];
        $mahasiswa = $this->mahasiswa;
        $register_ulang = $this->register_ulang;
        $postData = $request->getParsedBody();
        
        $mahasiswa->read($id);
        $register_ulang->get([['status', 'terverifikasi'], ['semester', $mahasiswa->data->tahun_ajaran->semester], ['mahasiswa_id', $id]]);

        if ($register_ulang->data->isEmpty()) {
            $result['success'] = false;
        }else{
            $register_ulang_data = $register_ulang->data[0];
            if (intval($postData['nomor_registrasi']) != intval($register_ulang_data->kode_register)) {
                $result['success'] = false;
            } 
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function importMahasiswa(ServerRequestInterface $request, ResponseInterface $response)
    {
        $myFiles = $request->getUploadedFiles();
        $file = $myFiles['file'];
        $uri = $file->getStream()->getMetadata('uri');
        $mahasiswa = $this->mahasiswa;

        $csvAsArray  = file($uri);

        foreach ($csvAsArray as $key => $value) {
            if ($key == 0) {
                continue;
            }
            echo $value."<br>";
            $col = explode(",", $value);
            $mahasiswa_value = [
                'orang' => [
                    'nik' => $col[0],
                    'nama' => $col[2],
                    'tempat_lahir' => $col[3],
                    'tanggal_lahir' => $col[4],
                    'jenis_kelamin' => $col[6] == 'L' ? 'l' : 'p',
                ],
                'nim' => $col[1],
                'tahun_ajaran_id' => 1,
                'ajukan_sks' => true,
            ];
            $mahasiswa->create($mahasiswa_value);
        }

        $response->getBody()->write("['status' => 'sukses']");
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
