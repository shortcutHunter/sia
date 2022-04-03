<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\objects\Session;
use \Illuminate\Pagination\Paginator;
use Illuminate\Database\Capsule\Manager as DB;

final class ApiController extends BaseController
{

    public function get($request, $response, $args)
    {   
        $getData = $request->getQueryParams();
        $object_name = $args['object'];
        $object = $this->get_object($object_name);
        $like_fields = $object->like_fields;
        $domain = [];
        $is_orang = false;
        $orang_value = false;
        $is_karyawan = false;
        $karyawan_value = false;

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

            if ($key == 'orang') {
                $is_orang = true;
                $orang_value = $value;
                continue;
            }

            if ($key == 'karyawan') {
                $is_karyawan = true;
                $karyawan_value = $value;
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

        if ($is_orang) {
            $object = $object->whereHas('orang', function($q) use ($orang_value) {
                $q->where('nama', 'like', '%'.$orang_value.'%');
            });
        }

        if ($is_karyawan) {
            $object = $object->whereHas('karyawan', function($q) use ($karyawan_value) {
                $q->whereHas('orang', function($w) use ($karyawan_value) {
                    $w->where('nama', 'like', '%'.$karyawan_value.'%');
                });
            });
        }

        if ($current_page) {
            $object = $object->orderBy('id', 'desc')->paginate($this->settings['row_per_page']);
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
        });

        if (array_key_exists('semester', $getData)) {
            $data->where('semester_id', $getData['semester']);
        }

        if (array_key_exists('jenis_karyawan', $getData)) {
            $data->where('jenis_karyawan', $getData['jenis_karyawan']);
        }

        $data = $data->get();

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

        $data = $mahasiswa->where([['status', 'mahasiswa'], ['semester_id', $mata_kuliah_diampuh->dosen_pjmk->semester_id]])->whereHas(
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
            $mahasiswa = $mahasiswa_obj->find($mahasiswa_id);
            $riwayat_belajar_condition = [['mahasiswa_id', $mahasiswa_id]];
            $riwayat_belajar = $riwayat_belajar_obj->where($riwayat_belajar_condition);
            $riwayat_belajar_value = [
                'mahasiswa_id' => $mahasiswa_id,
                'semester_id' => $mahasiswa->semester_id,
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

    public function logout($request, $response)
    {
        $user = $this->get_object('user');
        $user->logout();
        $data = ['status' => 'sukses'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
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

    public function rekapSemester($request, $response, $args)
    {
        $container = $this->container;

        $semester = $this->get_object('semester')->withCount([
            'mahasiswa',
            'mahasiswa as mahasiswa_count' => function($q) {
                $q->where('status', 'mahasiswa');
            }
        ])->get();

        foreach ($semester as $s) {
            $semester_id = $s->id;
            $query = "
                SELECT COUNT(*) AS total FROM `pengajuan_ks_detail` 
                INNER JOIN `pengajuan_ks` ON `pengajuan_ks`.`id` = `pengajuan_ks_detail`.`pengajuan_ks_id`
                INNER JOIN `mahasiswa` ON `mahasiswa`.`id` = `pengajuan_ks`.`mahasiswa_id`
                WHERE `pengajuan_ks`.`semester_id` = $semester_id AND
                `mahasiswa`.`semester_id` = $semester_id AND
                `mahasiswa`.`status` = 'mahasiswa' AND
                NOT EXISTS(
                    SELECT * FROM `mahasiswa`
                    WHERE `mahasiswa`.`id` = `pengajuan_ks`.`mahasiswa_id` AND
                    `semester_id` = $semester_id AND
                    NOT EXISTS(
                        SELECT * FROM `riwayat_belajar`
                        WHERE `mahasiswa`.`id` = `riwayat_belajar`.`mahasiswa_id` AND
                        `semester_id` = $semester_id AND
                        NOT EXISTS(
                            SELECT * FROM `riwayat_belajar_detail`
                            WHERE `riwayat_belajar`.`id` = `riwayat_belajar_detail`.`riwayat_belajar_id` AND
                            `riwayat_belajar_detail`.`mata_kuliah_id` = `pengajuan_ks_detail`.`mata_kuliah_id`
                        )
                    )
                )
            ";
            $result = DB::select(DB::raw($query));
            $total = $result[0]->total;
            $s->nilai_belum_terisi = $total;
        }

        $data = ['data' => $semester];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function rekapSemesterDetail($request, $response, $args)
    {
        $container = $this->container;

        $semester = $this->get_object('semester')->with('mahasiswa')->where('id', $args['semester_id'])->whereHas('mahasiswa', function($q){
            $q->where('status', 'mahasiswa');
        })->first();
        $data = ['data' => $semester];
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

    public function getBobot($nilai)
    {
        $mutu = "E";
        if ($nilai >= 79) {
            $mutu = "A";
        }
        elseif ($nilai >= 68) {
            $mutu = "B";
        }
        elseif ($nilai >= 60) {
            $mutu = "C";
        }
        elseif ($nilai >= 41) {
            $mutu = "D";
        }else{
            $mutu = "E";
        }
        return $mutu;
    }

    public function getNilai($riwayat_belajar_detail, $semester_id, $mata_kuliah_id)
    {
        $konfigurasi_nilai_obj = $this->get_object('konfigurasi_nilai');
        $nilai_akhir = 0;

        foreach ($riwayat_belajar_detail->riwayat_belajar_nilai as $nilai_mahasiswa) {
            $nilai_id = $nilai_mahasiswa->nilai_id;
            $konfigurasi_nilai = $konfigurasi_nilai_obj
                ->where([['nilai_id', $nilai_id], ['status', 'aktif']])
                ->whereHas('mata_kuliah_diampuh', function($a) use ($mata_kuliah_id, $semester_id) {
                    $a->where('mata_kuliah_id', $mata_kuliah_id)
                    ->whereHas('dosen_pjmk', function($b) use ($semester_id) {
                        $b->where([['semester_id', $semester_id], ['status', 'aktif']]);
                    });
                })
                ->first();
            $nilai_persentase = $nilai_mahasiswa->nilai * $konfigurasi_nilai->persentase / 100;
            $nilai_akhir += $nilai_persentase;
        }
        return $nilai_akhir;
    }

    public function getNilaiMutu($nilai_bobot)
    {
        $nilai_mutu = 0;

        if ($nilai_bobot == "A") {
            $nilai_mutu = 4;
        }
        elseif ($nilai_bobot == "B") {
            $nilai_mutu = 3;
        }
        elseif ($nilai_bobot == "C") {
            $nilai_mutu = 2;
        }
        elseif ($nilai_bobot == "D") {
            $nilai_mutu = 1;
        }else{
            $nilai_mutu = 0;
        }

        return $nilai_mutu;
    }

    public function nilaiMahasiswa($riwayat_belajar, $semester_id, $mahasiswa_id)
    {
        $khs_obj = $this->get_object('khs');
        $khs_detail_obj = $this->get_object('khs_detail');
        $total_sks = 0;
        $ips = 0;

        $khs = $khs_obj->where([['mahasiswa_id', $mahasiswa_id], ['semester_id', $semester_id]])->first();
        echo $khs;
        $khs_value = [
            'mahasiswa_id' => $mahasiswa_id,
            'semester_id' => $semester_id
        ];

        if (empty($khs)) {
            $khs = $khs_obj->create($khs_value);
        }

        foreach ($riwayat_belajar->riwayat_belajar_detail as $riwayat_belajar_detail) {
            $mata_kuliah_id = $riwayat_belajar_detail->mata_kuliah_id;

            $nilai_absolut = $this->getNilai($riwayat_belajar_detail, $semester_id, $mata_kuliah_id);
            $nilai_bobot = $this->getBobot($nilai_absolut);
            $nilai_mutu = $this->getNilaiMutu($nilai_bobot);

            // update nilai riwayat belajar
            $riwayat_belajar_detail->update([
                "nilai_absolut" => $nilai_absolut,
                "nilai_bobot"   => $nilai_bobot,
                "nilai_mutu"    => $nilai_mutu
            ]);

            // update / create KHS
            $khs_detail_value = [
                "khs_id"         => $khs->id,
                'mata_kuliah_id' => $mata_kuliah_id,
            ];
            $khs_detail = $khs_detail_obj->where($khs_detail_value)->first();

            $khs_detail_value['nilai_absolut']             = $nilai_absolut;
            $khs_detail_value['nilai_bobot']               = $nilai_bobot;
            $khs_detail_value['nilai_mutu']                = $nilai_mutu;
            $khs_detail_value['riwayat_belajar_detail_id'] = $riwayat_belajar_detail->id;

            if (empty($khs_detail)) {
                $khs_detail = $khs_detail_obj->create($khs_detail_value);
            }else{
                $khs_detail->update($khs_detail_value);
            }

            $sks = $riwayat_belajar_detail->mata_kuliah->sks;
            $total_sks += $sks;
            $ips += ($sks * $nilai_mutu);
        }

        $ips = $ips / $total_sks;
        $riwayat_belajar->update(['total_sks' => $total_sks, 'status' => 'nonaktif']);
        $khs->update(['total_sks' => $total_sks, 'ips' => $ips]);
    }

    public function buatTagihanMahasiswa($item_ids, $semester_id, $mahasiswa)
    {
        $setup_paket_obj = $this->get_object('paket_register_ulang');
        $item_obj = $this->get_object('item');
        $tagihan_obj = $this->get_object('tagihan');

        $setup_paket = $setup_paket_obj->where('semester_id', $semester_id)->first();
        $item = $item_obj->select('nama', 'kode', 'nominal')->whereIn('id', $item_ids);
        $total_nominal = $item->sum('nominal');
        $item_value = $item->get()->toArray();

        $tagihan_value = [
            'tanggal' => date('d/m/Y'),
            'nominal' => $total_nominal,
            'orang_id' => $mahasiswa->orang_id,
            'system' => true,
            'paket_register_ulang_id' => $setup_paket->id,
            'tagihan_item' => $item_value,
            'register_ulang' => true,
            'status' => 'proses'
        ];
        $tagihan = $tagihan_obj->create($tagihan_value);
        $mahasiswa->update([
            "tagihan_id" => $tagihan->id
        ]);
    }

    public function updateMahasiswa($mahasiswa, $semester_id)
    {
        $mahasiswa->update([
            'semester_id' => $semester_id,
            'reg_ulang' => true,
            'ajukan_sks' => false,
            'pengajuan' => false,
            'sudah_pengajuan' => false
        ]);
    }

    public function setDosenPjmk($semester_id)
    {
        $dosen_pjmk_obj = $this->get_object('dosen_pjmk');
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $konfigurasi_nilai_obj = $this->get_object('konfigurasi_nilai');

        $dosen_pjmks = $dosen_pjmk_obj->where([['semester_id', $semester_id], ['status', 'aktif']]);
        $mahasiswa = $mahasiswa_obj->where([['semester_id', $semester_id], ['status', 'mahasiswa']])->count();

        if ($mahasiswa != 0) {
            return false;
        }

        // set konfigurasi nilai to nonaktif
        foreach ($dosen_pjmks->get() as $dosen_pjmk) {
            $mata_kuliah_diampuh_ids = $dosen_pjmk->mata_kuliah_diampuh->pluck('id')->toArray();
            $konfigurasi_nilai_obj->whereIn('mata_kuliah_diampuh_id', $mata_kuliah_diampuh_ids)->update(['status' => 'nonaktif']);
        }

        // set dosen pjmk to nonaktif
        $dosen_pjmks->update(['status' => 'nonaktif']);
    }

    public function setDosenPa($semester_id)
    {
        $dosen_pa_obj = $this->get_object('dosen_pa');
        $mahasiswa_obj = $this->get_object('mahasiswa');

        $mahasiswa = $mahasiswa_obj->where([['semester_id', $semester_id], ['status', 'mahasiswa']])->count();

        if ($mahasiswa == 0) {
            $dosen_pa_obj->where([['semester_id', $semester_id], ['status', 'aktif']])->update(['status' => 'nonaktif']);
        }
    }

    public function rekapSemesterMahasiswa($request, $response, $args)
    {
        // DB::beginTransaction();

        $container             = $this->container;
        $mahasiswa_obj         = $this->get_object('mahasiswa');
        $riwayat_belajar_obj   = $this->get_object('riwayat_belajar');
        $postData              = $request->getParsedBody();

        $mahasiswa_ids     = $postData['mahasiswa'];
        $tagihan_item_ids  = $postData['tagihan_item'];
        $semester_id       = $postData['semester_id'];
        $semester_ganti_id = $postData['semester_ganti_id'];

        $mahasiswas = $mahasiswa_obj->whereIn('id', $mahasiswa_ids)->get();

        foreach ($mahasiswas as $mahasiswa) {
            $mahasiswa_id = $mahasiswa->id;
            $riwayat_belajar = $riwayat_belajar_obj
                ->where([
                    ['semester_id', $semester_id], 
                    ['status', 'aktif'], 
                    ['mahasiswa_id', $mahasiswa_id]
                ])
                ->first();

            if (!empty($riwayat_belajar)) {
                $this->nilaiMahasiswa($riwayat_belajar, $semester_id, $mahasiswa_id);
            }

            $this->buatTagihanMahasiswa($tagihan_item_ids, $semester_id, $mahasiswa);
            $this->updateMahasiswa($mahasiswa, $semester_ganti_id);
        }

        $this->setDosenPjmk($semester_id);
        $this->setDosenPa($semester_id);

        // DB::rollback();

        $data = ['status' => 'sukses'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function mahasiswaLulus($request, $response, $args)
    {
        $container             = $this->container;
        $mahasiswa_obj         = $this->get_object('mahasiswa');
        $riwayat_belajar_obj   = $this->get_object('riwayat_belajar');
        $postData              = $request->getParsedBody();

        $mahasiswa_ids     = $postData['mahasiswa'];
        $semester_id       = $postData['semester_id'];

        $mahasiswas = $mahasiswa_obj->whereIn('id', $mahasiswa_ids)->get();

        foreach ($mahasiswas as $mahasiswa) {
            $mahasiswa_id = $mahasiswa->id;
            $riwayat_belajar = $riwayat_belajar_obj
                ->where([
                    ['semester_id', $semester_id], 
                    ['status', 'aktif'], 
                    ['mahasiswa_id', $mahasiswa_id]
                ])
                ->first();

            if (!empty($riwayat_belajar)) {
                $this->nilaiMahasiswa($riwayat_belajar, $semester_id, $mahasiswa_id);
            }

            $mahasiswa->update(['status' => 'alumni']);
        }

        $this->setDosenPjmk($semester_id);
        $this->setDosenPa($semester_id);

        $data = ['status' => 'sukses'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function mahasiswaBerhenti($request, $response, $args)
    {
        $container             = $this->container;
        $mahasiswa_obj         = $this->get_object('mahasiswa');
        $riwayat_belajar_obj   = $this->get_object('riwayat_belajar');
        $postData              = $request->getParsedBody();

        $mahasiswa_ids     = $postData['mahasiswa'];
        $semester_id       = $postData['semester_id'];

        $mahasiswas = $mahasiswa_obj->whereIn('id', $mahasiswa_ids)->get();

        foreach ($mahasiswas as $mahasiswa) {
            $mahasiswa_id = $mahasiswa->id;
            $riwayat_belajar = $riwayat_belajar_obj
                ->where([
                    ['semester_id', $semester_id], 
                    ['status', 'aktif'], 
                    ['mahasiswa_id', $mahasiswa_id]
                ])
                ->first();

            if (!empty($riwayat_belajar)) {
                $this->nilaiMahasiswa($riwayat_belajar, $semester_id, $mahasiswa_id);
            }

            $mahasiswa->update(['status' => 'dropout']);
        }

        $this->setDosenPjmk($semester_id);
        $this->setDosenPa($semester_id);

        $data = ['status' => 'sukses'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
