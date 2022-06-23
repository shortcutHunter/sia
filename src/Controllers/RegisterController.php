<?php

namespace App\Controllers;

use App\Controllers\BaseController;

final class RegisterController extends BaseController
{
    public function getKonfiguration()
    {
        $konfigurasi = $this->get_object('konfigurasi')->first();

        return $konfigurasi;
    }

    public function isRegister()
    {
        $pendaftaran_obj = $this->get_object('pendaftaran');

        $konfigurasi = $this->getKonfiguration();
        $register = false;

        if ($konfigurasi->registrasi) {
            $pendaftaran = $pendaftaran_obj->where('status', 'open')->first();

            if (!empty($pendaftaran)) {
                $register = true;
            }
        }

        return $register;
    }

    public function register($request, $response)
    {
        $container = $this->container;
        $konfigurasi = $this->getKonfiguration();

        if (!$this->isRegister()) {
            return $response->withHeader('Location', '/');
        }

        $data = [
            'konfigurasi' => $konfigurasi
        ];

        return $container->get('twig')->render($response, 'root/register/homepage.twig', $data);
    }

    public function registerOnline($request, $response)
    {
        $container = $this->container;
        $object = $this->get_object('orang');
        $paintia_obj = $this->get_object('panitia');
        $pendaftaran_obj = $this->get_object('pendaftaran');
        $konfigurasi = $this->getKonfiguration();

        if (!$this->isRegister()) {
            return $response->withHeader('Location', '/');
        }

        $panitia = $konfigurasi->getNextPanitia();
        $pendaftaran = $pendaftaran_obj->where('status', 'open')->first();
        $pembiayaan_tahun_ajaran = $pendaftaran->tahun_ajaran->pembiayaan_tahun_ajar->where('registrasi', true)->first();
        $panitia = $paintia_obj->find($panitia);

        $data = [
            'option'    => [],
            'selection' => [],
            'konfigurasi' => $konfigurasi,
            'isactive' => 'online',
            'panitia' => $panitia,
            'pta' => $pembiayaan_tahun_ajaran
        ];

        foreach ($object->selection_fields as $value) {
            $data['option'][$value] = $object->{$value."_enum"};
        }

        foreach ($object::$relation as $model) {
            $model_name = $model['name'];
            $object_relation = $this->get_object($model['name']);
            $data[$model_name] = [
                'option'    => [],
                'selection' => []
            ];

            if ($model['is_selection']) {
                $data['selection'][$model['name']] = $object_relation->all();
            }else{
                foreach ($object_relation->selection_fields as $value) {
                    $data[$model_name]['option'][$value] = $object_relation->{$value."_enum"};
                }
                
                foreach ($object_relation::$relation as $value) {
                    if ($value['is_selection']) {
                        $object_relation_selection = $this->get_object($value['name']);
                        $data[$model_name]['selection'][$value['name']] = $object_relation_selection->all();
                    }
                }
            }
        }

        return $container->get('twig')->render($response, 'root/register/online.twig', $data);
    }

    public function registerOffline($request, $response)
    {
        $container = $this->container;
        $konfigurasi = $this->getKonfiguration();

        if (!$this->isRegister()) {
            return $response->withHeader('Location', '/');
        }

        $data = [
            'konfigurasi' => $konfigurasi,
            'isactive' => 'offline'
        ];

        return $container->get('twig')->render($response, 'root/register/offline.twig', $data);
    }

    public function checkNIK($request, $response)
    {
        $container = $this->container;
        $postData = $request->getParsedBody();
        $orang_obj = $this->get_object('orang');

        if (!$this->isRegister()) {
            return $response->withHeader('Location', '/');
        }

        $result = false;
        $nik = $postData['nik'];
        $total_orang = $orang_obj->where('nik', $nik)->count();

        $result = $total_orang > 0 ? false : true;

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function registerSubmit($request, $response)
    {
        $container = $this->container;
        $postData = $request->getParsedBody();
        $postFile = $request->getUploadedFiles();
        $orang_obj = $this->get_object('orang');
        $pmb_obj = $this->get_object('pmb');

        if (!$this->isRegister()) {
            return $response->withHeader('Location', '/');
        }

        $orang_value = [
            'nik' => $postData['nik'],
            'nama' => $postData['nama'],
            'tempat_lahir' => $postData['tempat_lahir'],
            'tanggal_lahir' => $postData['tanggal_lahir'],
            'jenis_kelamin' => $postData['jenis_kelamin'],
            'alamat' => $postData['alamat'],
            'rt_rw' => $postData['rt_rw'],
            'kel_desa' => $postData['kel_desa'],
            'kab_kota' => $postData['kab_kota'],
            'provinsi' => $postData['provinsi'],
            'agama_id' => $postData['agama_id'],
            'email' => $postData['email'],
            'no_hp' => $postData['no_hp'],
            'nama_ayah' => $postData['nama_ayah'],
            'pekerjaan_ayah' => $postData['pekerjaan_ayah'],
            'nama_ibu' => $postData['nama_ibu'],
            'pekerjaan_ibu' => $postData['pekerjaan_ibu'],
            'penghasilan_ortu' => str_replace(",", ".", str_replace(".", "", $postData['penghasilan_ortu'])),
            'asal_sekolah' => $postData['asal_sekolah'],
            'jurusan' => $postData['jurusan'],
            'tinggi_badan' => $postData['tinggi_badan'],
            'berat_badan' => $postData['berat_badan'],

            'pasfoto' => [
                'filename' => $postFile['pasfoto']->getClientFilename(),
                'filetype' => $postFile['pasfoto']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['pasfoto']->getFilePath())),
            ],
            'ijazah' => [
                'filename' => $postFile['ijazah']->getClientFilename(),
                'filetype' => $postFile['ijazah']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['ijazah']->getFilePath())),
            ],
            'ktp' => [
                'filename' => $postFile['ktp']->getClientFilename(),
                'filetype' => $postFile['ktp']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['ktp']->getFilePath())),
            ],
            'surket_menikah' => [
                'filename' => $postFile['surket_menikah']->getClientFilename(),
                'filetype' => $postFile['surket_menikah']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['surket_menikah']->getFilePath())),
            ],
            'akte_lahir' => [
                'filename' => $postFile['akte_lahir']->getClientFilename(),
                'filetype' => $postFile['akte_lahir']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['akte_lahir']->getFilePath())),
            ],
            'kartu_keluarga' => [
                'filename' => $postFile['kartu_keluarga']->getClientFilename(),
                'filetype' => $postFile['kartu_keluarga']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['kartu_keluarga']->getFilePath())),
            ],
            'kartu_vaksin' => [
                'filename' => $postFile['kartu_vaksin']->getClientFilename(),
                'filetype' => $postFile['kartu_vaksin']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['kartu_vaksin']->getFilePath())),
            ],
        ];
        $orang = $orang_obj->create($orang_value);

        $pmb_value = [
            'orang_id' => $orang->id,
            'bukti_pembayaran' => [
                'filename' => $postFile['bukti_pembayaran']->getClientFilename(),
                'filetype' => $postFile['bukti_pembayaran']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['bukti_pembayaran']->getFilePath())),
            ],
            'pernyataan' => true,
            'biaya_pendaftaran' => $postData['biaya_pendaftaran'],
            'panitia_id' => $postData['panitia_id'],
        ];
        $pmb = $pmb_obj->create($pmb_value);

        return $response->withHeader('Location', '/register/sukses');
    }


    public function registerSukses($request, $response)
    {
        $container = $this->container;
        $konfigurasi = $this->getKonfiguration();

        if (!$this->isRegister()) {
            return $response->withHeader('Location', '/');
        }

        $data = [
            'konfigurasi' => $konfigurasi,
        ];

        return $container->get('twig')->render($response, 'root/register/register_sukses.twig', $data);
    }

}
