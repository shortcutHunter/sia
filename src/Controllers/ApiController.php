<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\objects\Session;
use \Illuminate\Pagination\Paginator;

final class ApiController extends BaseController
{

    public function get($request, $response, $args)
    {   
        $getData = $request->getQueryParams();
        $object_name = $args['object'];
        $object = $this->get_object($object_name);
        $like_fields = $object->like_fields;
        $domain = [];

        if (array_key_exists('page', $getData)) {
            $current_page = $getData['page'];
            Paginator::currentPageResolver(function() use ($current_page) {
                return $current_page;
            });
        }else{
            $current_page = false;
        }

        foreach ($getData as $key => $value) {
            if ($key == 'page') {
                continue;
            }
            if (in_array($key, $like_fields)) {
                array_push($domain, [$key, 'like', '%'.$value.'%']);
            }else{
                array_push($domain, [$key, $value]);
            }
        }

        if (count($domain) > 0) {
            $object = $object->where($domain);
        }

        if ($current_page) {
            $object = $object->paginate($this->settings['row_per_page']);
            $data = $object->toJson();
        }else{
            $object = $object->get();
            $data = json_encode(['data' => $object]);
        }



        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function detail($request, $response, $args)
    {
        $id = $args['id'];
        $object_name = $args['object'];
        $object = $this->get_object($object_name);

        $data = $object->find($id);
        $data = json_encode(['data' => $data]);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function selection($request, $response, $args)
    {
        $object_name = $args['object'];
        $object = $this->get_object($object_name);
        $data = [
            'option'    => [],
            'selection' => [],
        ];

        foreach ($object->selection_fields as $value) {
            $data['option'][$value] = $object->{$value."_enum"};
        }

        foreach ($object::$relation as $model) {
            $model_name = $model['name'];
            $object_relation = $this->get_object($model['name']);
            $data[$model_name] = [
                'option'    => [],
                'selection' => [],
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

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function search($request, $response, $args)
    {
        $postData = $request->getParsedBody();
        $object_name = $args['object'];
        $object = $this->get_object($object_name);
        $getData = $request->getQueryParams();

        $data = $object->whereHas('orang', function($q) use ($getData) {
            $q->where('nama', 'like', '%'.$getData['nama'].'%');
        })->get();

        $data = json_encode(['data' => $data]);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getMahasiswa($request, $response, $args)
    {
        $mata_kuliah_diampuh_id = $args['matkul_diampuh_id'];
        $mahasiswa = $this->get_object('mahasiswa');
        $mata_kuliah_diampuh = $this->get_object('mata_kuliah_diampuh');

        $mata_kuliah_diampuh = $mata_kuliah_diampuh->find($mata_kuliah_diampuh_id);

        $data = $mahasiswa->whereHas(
            'pengajuan_ks', 
            function($q) use ($mata_kuliah_diampuh) {
                $q
                    ->where('status', 'terima')
                    ->where('semester_id', $mata_kuliah_diampuh->dosen_pjmk->semester_id)
                    ->where('tahun_ajaran_id', $mata_kuliah_diampuh->dosen_pjmk->tahun_ajaran_id)
                    ->whereHas(
                        'pengajuan_ks_detail', 
                        function($q) use ($mata_kuliah_diampuh) {
                            $q->where('mata_kuliah_id', $mata_kuliah_diampuh->mata_kuliah_id);
                    });
        })->get();

        foreach ($data as $key => $value) {
            $riwayat_belajar_detail_obj = $this->get_object('riwayat_belajar_detail');
            $riwayat_belajar_detail = $riwayat_belajar_detail_obj->with('riwayat_belajar_nilai')
                ->where('mata_kuliah_id', $mata_kuliah_diampuh->mata_kuliah_id)
                ->whereHas(
                    'riwayat_belajar', 
                    function($q) use ($mata_kuliah_diampuh, $value) {
                        $q
                            ->where('semester_id', $mata_kuliah_diampuh->dosen_pjmk->semester_id)
                            ->where('mahasiswa_id', $value->id);
                    }
                )->first();
            $value->{'nilai'} = $riwayat_belajar_detail;
        }

        $data = json_encode(['data' => $data]);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function add($request, $response, $args)
    {
        $postData = $request->getParsedBody();
        $object_name = $args['object'];
        $object = $this->get_object($object_name);

        if (array_key_exists('multiple', $postData)) {
            $object = $object->insert($postData['data']);
        }else{
            $object = $object->create($postData)->toJson();
        }

        $response->getBody()->write($object);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function prosesNilai($request, $response, $args)
    {
        $postData = $request->getParsedBody();
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $riwayat_belajar_obj = $this->get_object('riwayat_belajar');

        foreach ($postData['data'] as $key => $value) {
            $mahasiswa_id = $value['mahasiswa_id'];
            $semester_id = $value['semester_id'];
            $mahasiswa = $mahasiswa_obj->find($mahasiswa_id);
            $riwayat_belajar_condition = [['mahasiswa_id', $mahasiswa_id], ['semester_id', $semester_id]];
            $riwayat_belajar = $riwayat_belajar_obj->where($riwayat_belajar_condition);
            $riwayat_belajar_value = [
                'mahasiswa_id' => $mahasiswa_id,
                'semester_id' => $semester_id,
                'riwayat_belajar_detail' => $value['riwayat_belajar_detail']
            ];

            if ($riwayat_belajar->count() == 0) {
                $riwayat_belajar = $riwayat_belajar_obj->create($riwayat_belajar_value);
            }else{
                $riwayat_belajar->first()->update($riwayat_belajar_value);
            }
        }

        $data = json_encode(['status' => 'sukses']);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getTagihan($request, $response, $args)
    {
        $paket_reg_ulang_obj = $this->get_object('paket_register_ulang');
        $tagihan_obj = $this->get_object('tagihan');

        $paket_reg_ulang = $paket_reg_ulang_obj->find($args['setup_paket_id']);
        $tagihan = $tagihan_obj
            ->where('paket_register_ulang_id', $paket_reg_ulang->id)
            ->where('system', true);

        $tanggal = $tagihan->get()->max('tanggal');
        $tagihan = $tagihan->where('tanggal', $tanggal)->get();

        $data = json_encode(['data' => $tagihan]);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function buatTagihan($request, $response, $args)
    {
        $postData = $request->getParsedBody();
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $setup_tagihan_obj = $this->get_object('paket_register_ulang');
        $tagihan_obj = $this->get_object('tagihan');
        $tagihan_item_obj = $this->get_object('tagihan_item');

        $setup_tagihan = $setup_tagihan_obj->find($postData['setup_tagihan_id'])->first();
        $mahasiswa = $mahasiswa_obj->where('setup_tagihan_id', $setup_tagihan->semester_id)->get();
        $tanggal = date('d/m/Y');

        $tagihan_item_val = $setup_tagihan->paket_register_ulang_item->item->map(function($data) {
            return $data->only(['nama', 'kode', 'nominal']);
        })->toArray();

        foreach ($mahasiswa as $key => $value) {
            $tagihan_val = [
                'tanggal' => $tanggal,
                'nominal' => $setup_tagihan->nominal,
                'orang_id' => $value->orang->id,
                'tagihan_item' => $tagihan_item_val,
                'system' => true,
                'paket_register_ulang_id' => $setup_tagihan->id
            ];
            $tagihan = $tagihan_obj->create($tagihan_val);
        }

        $data = json_encode(['status' => 'sukses']);

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $postData = $request->getParsedBody();
        $object_name = $args['object'];
        $object = $this->get_object($object_name);
        $object = $object->find($id);

        $object->update($postData);

        $object = $object->toJson();

        $response->getBody()->write($object);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function massUpdate($request, $response, $args)
    {
        $postData = $request->getParsedBody();
        $object_name = $args['object'];
        $object = $this->get_object($object_name);
        $data = $postData['data'];

        foreach ($data as $value) {
            $object_id = $value['id'];
            unset($value['id']);
            $object->find($object_id)->update($value);
        }

        $object = json_encode(['status' => 'updated']);

        $response->getBody()->write($object);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function delete($request, $response, $args)
    {
        $id = $args['id'];
        $postData = $request->getParsedBody();
        $object_name = $args['object'];
        $object = $this->get_object($object_name);
        $object = $object->find($id);

        $object->delete($postData);

        $response->getBody()->write(json_encode(true));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function login($request, $response)
    {
        $container = $this->container;
        $user = $this->get_object('user');
        $postData = $request->getParsedBody();

        $sucess = $user->authenticate($postData['username'], $postData['password']);

        if ($sucess) {
            return $response->withHeader('Location', '/');
        }else{
            return $response->withHeader('Location', '/login');
        }
    }

    public function session($request, $response, $args)
    {
        $container = $this->container;
        $user = $container->get('session')->get('user');
        $orang = $container->get('session')->get('orang');

        if (!$user) {
            $user = [];
        }else{
            $user->orang = $orang;
        }

        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    public function konfigurasi($request, $response, $args)
    {
        $container = $this->container;
        $konfigurasi = $this->get_object('konfigurasi')->first();

        $data = ['data' => $konfigurasi];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function menu($request, $response, $args)
    {
        $container = $this->container;
        $user = $container->get('session')->get('user');
        $menu = [];

        if ($user) {
            
            switch ($user->role) {
                case 'admin':
                    $menu = [
                        'home' => ['name' => 'Home', 'ignore' => true, 'icon' => 'bi-home'],
                        'parentPmb' => ['name' => 'PMB', 'icon' => 'bi-blog', 'submenus'  => [
                                'pmb' => ['name' => 'PMB', 'icon' => 'bi-blog'],
                                'penerbitan_nim' => ['name' => 'Penerbitan NIM', 'icon' => 'bi-user-id'],
                            ]
                        ],
                        'mahasiswa' => ['name' => 'Mahasiswa', 'icon' => 'bi-user-group'],
                        'karyawan' => ['name' => 'Karyawan', 'icon' => 'bi-user'],
                        'dosen' => ['name' => "Dosen", 'icon' => 'bi-graduation', 'submenus' => [
                                'dosen_pa' => ['name' => 'Dosen PA', 'icon' => 'bi-graduation'],
                                'dosen_pjmk' => ['name' => 'Dosen PJMK', 'icon' => 'bi-graduation'],
                            ]
                        ],
                        'verifikasi' => ['name' => "Verifikasi", 'icon' => 'bi-checkmark', 'submenus' => [
                              'verifikasi_pmb' => ['name' => 'Verifikasi PMB', 'ignore' => true, 'icon' => 'bi-blog'],
                              'verifikasi_reg_ulang' => ['name' => 'Verifikasi Registrasi Ulang', 'ignore' => true, 'icon' => 'bi-user-id'],
                          ]
                        ],
                        'kwitansi' => ['name' => 'Kwitansi', 'icon' => 'bi-archive'],
                        'konfigurasi' => ['name' => 'Konfigurasi', 'icon' => 'bi-archive', 'submenus' => [
                                'mata_kuliah' => ['name' => 'Mata Kuliah', 'icon' => 'bi-bookmarks'],
                                'tahun_ajaran' => ['name' => 'Tahun Ajaran', 'icon' => 'bi-calendar'],
                                'nilai' => ['name' => 'Nilai', 'icon' => 'bi-checklist'],
                                'agama' => ['name' => 'Agama', 'icon' => 'bi-heart'],
                                'paket' => ['name' => 'Paket Pembayaran', 'icon' => 'bi-folder'],
                                'paket_register_ulang' => ['name' => 'Paket Register Ulang', 'icon' => 'bi-folder-open'],
                                'paket_register' => ['name' => 'Paket Register', 'ignore' => true, 'icon' => 'bi-inbox'],
                                'sequance' => ['name' => 'Sequance', 'icon' => 'bi-graph-bar'],
                            ]                    
                        ],
                        'rekap_semester' => ['name' => 'Rekap Semester', 'ignore' => true, 'icon' => 'bi-media-loop']
                    ];
                break;
            }

        }

        $response->getBody()->write(json_encode($menu));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
