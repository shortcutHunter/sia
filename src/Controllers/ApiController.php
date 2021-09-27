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
        }else{
            $object = $object->get();
        }


        $data = $object->toJson();

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
