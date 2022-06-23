<?php

use App\Objects\User;
use App\Objects\Orang;

class MainSeed {

    protected $total_mahasiswa = 5;

    function extract_csv_data($file_name)
    {
        $data_file = fopen(DATA_PATH.'/'.$file_name, "r");
        $counter = 0;
        $data = [];
        $header = [];
        while (! feof($data_file)) {
            $row = fgetcsv($data_file);

            if ($counter == 0) {
                $header = $row;
            }else{
                if ($row) {
                    $col_data = [];
                    foreach ($row as $key => $col) {
                        $col_data[$header[$key]] = $col;
                    }
                    array_push($data, $col_data);
                }
            }
            $counter++;
        }
        fclose($data_file);

        return $data;
    }

    function getObject($class_name)
    {
        $class_name = join("", array_map("ucfirst", explode("_", $class_name)));
        $class_name = "App\\Objects\\".$class_name;
        $model = new $class_name();
        return $model;
    }

    function importData($data_name)
    {
        $object = $this->getObject($data_name);
        $data = $this->extract_csv_data($data_name.'.csv');
        $object->insert($data);
    }

    function konfigurasiData()
    {
        $tahun_ajaran_obj = $this->getObject('tahun_ajaran');
        $konfigurasi_obj = $this->getObject('konfigurasi');
        $semester_obj = $this->getObject('semester');
        $pembiayaan_lainnya_obj = $this->getObject('pembiayaan_lainnya');
        $pendaftaran_obj = $this->getObject('pendaftaran');

        $pembiayaan_tahun_ajar_val = [];
        $semester = $semester_obj->whereNotNull('tipe')->get();
        $pembiayaan_lainnya = $pembiayaan_lainnya_obj->all();
        $price = 3000000;

        foreach ($semester as $key => $value) {
            $pta_val = [
                "nama" => "Pembiayaan Semester " . $value->nama,
                "biaya_lunas" => $price / 10,
                "total_biaya" => $price,
                "semester_id" => $value->id,
                "lainnya" => false,
                "registrasi" => false,
            ];
            array_push($pembiayaan_tahun_ajar_val, $pta_val);

            $price += 100000;
        }

        foreach ($pembiayaan_lainnya as $key => $value) {
            $pta_val = [
                "nama" => $value->nama,
                "biaya_lunas" => $price / 10,
                "total_biaya" => $price,
                "semester_id" => null,
                "lainnya" => true,
                "registrasi" => false,
            ];
            array_push($pembiayaan_tahun_ajar_val, $pta_val);

            $price += 100000;
        }

        $pta_val = [
            "nama" => "Biaya Pendaftaran",
            "biaya_lunas" => $price / 10,
            "total_biaya" => $price,
            "semester_id" => null,
            "lainnya" => false,
            "registrasi" => true,
        ];
        array_push($pembiayaan_tahun_ajar_val, $pta_val);

        $tahun_ajaran = $tahun_ajaran_obj->create([
            "nama" => date("Y") . "/" . date('Y', strtotime('+1 year')),
            "tahun" => date("Y"),
            'pembiayaan_tahun_ajar' => $pembiayaan_tahun_ajar_val
        ]);

        $konfigurasi = $konfigurasi_obj->first();
        $konfigurasi->update([
            "tahun_ajaran_id" => $tahun_ajaran->id,
            "semester_id" => 1
        ]);

        $pendaftaran = $pendaftaran_obj->create([
            "tanggal_mulai" => date('d/m/Y'),
            "tahun_ajaran_id" => $tahun_ajaran->id,
            "status" => 'open',
            "max_cicilan" => 10,
        ]);
    }

    function paketData()
    {
        $paket_obj = $this->getObject('paket');
        $item_obj = $this->getObject('item');
        $paket_register_ulang_obj = $this->getObject('paket_register_ulang');
        $semester_obj = $this->getObject('semester');

        $paket_value = [
            "nama" => "Paket 1",
            "nominal" => 100000
        ];
        $paket = $paket_obj->create($paket_value);


        $item_value = [
            "nama" => "Item 1",
            "nominal" => 100000,
            "paket_id" => $paket->id
        ];
        $item = $item_obj->create($item_value);

        $semesters = $semester_obj->all();

        foreach ($semesters as $semester) {
            $paket_register_ulang_value = [
                "semester_id" => $semester->id,
                "nominal"=> 100000,
                'paket_register_ulang_item' => [
                    ["item_id" =>  $item->id]
                ]
            ];
            $paket_register_ulang = $paket_register_ulang_obj->create($paket_register_ulang_value);
        }
    }

    function get_real_filename($headers)
    {
        $faker = Faker\Factory::create();

        if (isset($headers)) {
            foreach($headers as $header)
            {
                if (strpos(strtolower($header),'content-disposition') !== false)
                {
                    $tmp_name = explode('=', $header);
                    if ($tmp_name[1]) return trim($tmp_name[1],'";\'');
                }

                if (strpos(strtolower($header),'location') !== false)
                {
                    $tmp_name = explode('/', $header);
                    return $tmp_name[count($tmp_name) - 1];
                }
            }
        }

        return $faker->name . ".jpg";
    }

    function fakeImages()
    {
        $faker = Faker\Factory::create();
        $faker->addProvider(new \Xvladqt\Faker\LoremFlickrProvider($faker));

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        ); 

        $url = $faker->imageUrl($width = 320, $height = 240);
        $image = file_get_contents($url, false, stream_context_create($arrContextOptions));
        $base64 = base64_encode($image);

        $file = [
            "filename" => $this->get_real_filename($http_response_header),
            "filetype" => "image/jpg",
            "base64" => $base64
        ];

        return $file;
    }


    function fakeDataOrang()
    {
        $faker = Faker\Factory::create();

        $jenis_kelamin = $faker->randomElement($array = array ('l','p'));
        $pendidikan_terakhir = $faker->randomElement($array = array ('SMA','SMK'));
        $no_hp = $faker->phoneNumber;
        $agama_id = $faker->numberBetween($min=1, $max=6);
        $nik = $faker->numberBetween($min=1000000000, $max=9999999999);
        $tanggal_lahir = $faker->date($format = 'd/m/Y', $max = 'now');
        $alamat = $faker->address;
        $rt_rw = $faker->name();
        $kel_desa = $faker->name();
        $kab_kota = $faker->name();
        $provinsi = $faker->name();
        $email = $faker->email;
        $nama_ayah = $faker->name($gender="male");
        $pekerjaan_ayah = $faker->word;
        $nama_ibu = $faker->name($gender="female");
        $pekerjaan_ibu = $faker->word;
        $penghasilan_ortu = $faker->randomDigitNotNull;
        $asal_sekolah = $faker->company;
        $jurusan = $faker->word;
        $tinggi_badan = $faker->numberBetween($min=100, $max=200);
        $berat_badan = $faker->numberBetween($min=40, $max=100);

        if ($jenis_kelamin == 'l') {
            $nama = $faker->name($gender="male");
        }else{
            $nama = $faker->name($gender="female");
        }

        $pasfoto = $this->fakeImages();
        $ijazah = $this->fakeImages();
        $ktp = $this->fakeImages();
        $surket = $this->fakeImages();
        $akte_lahir = $this->fakeImages();
        $kartu_keluarga = $this->fakeImages();
        $kartu_vaksin = $this->fakeImages();

        $orang_value = [
            "nik" => $nik,
            "nama" => $nama,
            "tempat_lahir" => $alamat,
            "tanggal_lahir" => $tanggal_lahir,
            "jenis_kelamin" => $jenis_kelamin,
            "alamat" => $alamat,
            "agama_id" => $agama_id,
            "email" => $email,
            "no_hp" => $no_hp,
            "nama_ayah" => $nama_ayah,
            "pekerjaan_ayah" => $pekerjaan_ayah,
            "nama_ibu" => $nama_ibu,
            "pekerjaan_ibu" => $pekerjaan_ibu,
            "penghasilan_ortu" => $penghasilan_ortu,
            "asal_sekolah" => $asal_sekolah,
            "jurusan" => $jurusan,
            "tinggi_badan" => $tinggi_badan,
            "berat_badan" => $berat_badan,
            "pendidikan_terakhir" => $pendidikan_terakhir,
            "pasfoto" => $pasfoto,
            "ijazah" => $ijazah,
            "ktp" => $ktp,
            "surket_menikah" => $surket,
            "akte_lahir" => $akte_lahir,
            "kartu_keluarga" => $kartu_keluarga,
            "kartu_vaksin" => $kartu_vaksin,
        ];

        return $orang_value;
    }

    function mahasiswaData($total)
    {
        $faker = Faker\Factory::create();

        $pendaftaran_obj = $this->getObject('pendaftaran');
        $pendaftaran = $pendaftaran_obj->first();

        for ($i=0; $i < $total; $i++) {
            $pmb_obj = $this->getObject('pmb');
            $penerbitan_nim_obj = $this->getObject('penerbitan_nim');

            $bukti_pembayaran = $this->fakeImages();
            $pmb_value = [
                "orang" => $this->fakeDataOrang(),
                "bukti_pembayaran" => $bukti_pembayaran,
                "pernyataan" => 1,
                "tanggal_pendaftaran" => date("d/m/Y"),
                'pendaftaran_id' => $pendaftaran->id
            ];

            $pmb = $pmb_obj->create($pmb_value);
            $pmb->update(["status" => "terverifikasi", "test_tertulis" => date('d/m/Y'), "test_tertulis_end" => date('d/m/Y')]);
            $pmb->update(["status" => "test_lulus", "test_kesehatan" => date('d/m/Y'), "test_kesehatan_end" => date('d/m/Y')]);
            $pmb->update(["status" => "kesehatan_lulus", "test_wawancara" => date('d/m/Y'), "test_wawancara_end" => date('d/m/Y')]);
            $pmb->update(["status" => "wawancara_lulus", "daftar_ulang" => date('d/m/Y'), "daftar_ulang_end" => date('d/m/Y')]);
        }

        $pendaftaran->update(['status' => 'closed']);
        $pendaftaran->refresh();
        $pendaftaran->terbitkanNIM();
    }

    function createKaryawan($role)
    {
        $faker = Faker\Factory::create();
        $karyawan_obj = $this->getObject('karyawan');
        $karyawan_value = [
            "ni" => $faker->randomDigitNotNull,
            "user" => ['role' => [['id'=> $role]]],
            "orang" => $this->fakeDataOrang()
        ];
        $karyawan = $karyawan_obj->create($karyawan_value);

        return $karyawan;
    }

    function createDosenPjmk($semester_id, $mata_kuliah_diampuh_ids, $role)
    {
        $karyawan = $this->createKaryawan($role);
        $dosen_pjmk_obj = $this->getObject('dosen_pjmk');
        $tahun_ajaran_obj = $this->getObject('tahun_ajaran');
        $nilai_obj = $this->getObject('nilai');

        $tahun_ajaran = $tahun_ajaran_obj->first();
        $nilai = $nilai_obj->all();

        $dosen_pjmk_value = [
            "karyawan_id" => $karyawan->id,
            "tahun_ajaran_id" => $tahun_ajaran->id,
            'semester' => [['id' => $semester_id]]
        ];
        $dosen_pjmk = $dosen_pjmk_obj->create($dosen_pjmk_value);

        foreach ($mata_kuliah_diampuh_ids as $mata_kuliah_id) {
            $mata_kuliah_diampuh_obj = $this->getObject('mata_kuliah_diampuh');
            $konfigurasi_nilai = [];
            $mata_kuliah_diampuh_value = [
                "dosen_pjmk_id" => $dosen_pjmk->id,
                "mata_kuliah_id" => $mata_kuliah_id,
                "terkonfigurasi" => true
            ];
            $mata_kuliah_diampuh = $mata_kuliah_diampuh_obj->create($mata_kuliah_diampuh_value);

            foreach ($nilai as $key => $value) {
                $konfigurasi_nilai_value = [
                    "nilai_id" => $value->id,
                    "persentase" => 25,
                    "mata_kuliah_diampuh_id" => $mata_kuliah_diampuh->id
                ];
                array_push($konfigurasi_nilai, $konfigurasi_nilai_value);
            }
            $mata_kuliah_diampuh->update(['konfigurasi_nilai' => $konfigurasi_nilai]);
        }
    }

    function createDosenPa($semester_id, $role)
    {
        $dosen_pa_obj = $this->getObject('dosen_pa');
        $tahun_ajaran_obj = $this->getObject('tahun_ajaran');

        $karyawan = $this->createKaryawan($role);
        $tahun_ajaran = $tahun_ajaran_obj->first();

        $dosen_pa_value = [
            "karyawan_id" => $karyawan->id,
            "tahun_ajaran_id" => $tahun_ajaran->id
        ];

        $dosen_pa = $dosen_pa_obj->create($dosen_pa_value);

        return $dosen_pa;
    }

    function createPanitia($total)
    {
        $faker = Faker\Factory::create();

        for ($i=0; $i < $total; $i++) {
            $panitia_obj = $this->getObject('panitia');

            $panitia_value = [
                "nama" => $faker->name(),
                "nohp" => $faker->phoneNumber,
                "norek" => $faker->numberBetween($min=1000000000, $max=9999999999),
                "nama_bank" => $faker->name(),
            ];

            $panitia = $panitia_obj->create($panitia_value);
        }
    }

    function dosenData($role)
    {
        $faker = Faker\Factory::create();
        $mata_kuliah_obj = $this->getObject('mata_kuliah');
        $mata_kuliah = $mata_kuliah_obj->all();

        $mata_kuliah_diampuh_ids = [];
        $semester_ids = [];

        foreach ($mata_kuliah as $key => $value) {
            array_push($mata_kuliah_diampuh_ids, $value->id);

            $is_even = ($key % 2 != 0);
            $is_last = ( (!isset($mata_kuliah[$key+1])) || $value->semester_id != $mata_kuliah[$key+1]->semester_id);

            if ($key != 0 && count($mata_kuliah_diampuh_ids) > 0 && ($is_even || $is_last) ) {
                $dosen_pjmk = $this->createDosenPjmk($value->semester_id, $mata_kuliah_diampuh_ids, $role);
                $mata_kuliah_diampuh_ids = [];
            }

            if ($is_last) {
                array_push($semester_ids, $value->semester_id);
            }
        }

        foreach($semester_ids as $semester_id) {
            $this->createDosenPa($semester_id, $role);
        }
    }

    function karyawanData()
    {
        $faker = Faker\Factory::create();
        $role_obj = $this->getObject('role');
        $role = $role_obj->whereNotIn('value', ['admin', 'pmb', 'mahasiswa'])->get();

        foreach ($role as $key => $value) {
            
            if ($value->value == 'dosen') {
                $this->dosenData($value->id);
            }else{
                $this->createKaryawan($value->id);
            }
        }

    }

    function importKaryawan()
    {
        $data_file = fopen(DATA_PATH.'/pegawai.csv', "r");
        $counter = 0;
        $data = [];
        $header = [];
        while (! feof($data_file)) {
            $pegawai_obj = $this->getObject('karyawan');
            $role_obj = $this->getObject('role');
            $row = fgetcsv($data_file);

            if ($counter == 0) {
                $header = $row;
            }else{
                if ($row) {

                    $role = $role_obj->where('value', $row[9])->first();;

                    $col_data = [
                        'orang' => [
                            'nik' => $row[0],
                            'nama' => $row[1],
                            'tanggal_lahir' => $row[2],
                            'tempat_lahir' => $row[3],
                            'jenis_kelamin' => $row[4],
                            'agama_id' => $row[5],
                            'alamat' => $row[6],
                            'email' => $row[7],
                            'no_hp' => $row[8],
                            'npwp' => $row[10],
                            'bpjstk' => $row[11],
                        ],
                        'ni' => $row[13],
                        'user' => ['role' => [['id' => $role->id]]],
                    ];
                    array_push($data, $col_data);
                    $pegawai = $pegawai_obj->create($col_data);
                }
            }
            $counter++;
        }
        fclose($data_file);
    }

    function run()
    {
        // ini_set('memory_limit', '-1');

        $settings = require __DIR__ . '/../../config/settings.php';
        $db_name = $settings['db']['database'];

        $user = new User;
        $orang = new Orang;


        // Drop if exist then create database
        echo "Drop if exist then create sia database.... \r\n";
        exec('..\mysql\bin\mysql -u root -e "DROP DATABASE IF EXISTS '.$db_name.'; create database '.$db_name.'"');
        
        // Run prepared script to create table and the relation
        echo "Run prepared script to create table and the relation.... \r\n";
        exec('..\mysql\bin\mysql -u root '.$db_name.' < database/SIA.sql');

        // import data
        echo "Import data from excel.... \r\n";
        $this->importData('agama');
        $this->importData('nilai');
        $this->importData('semester');
        $this->importData('jurusan');
        $this->importData('mata_kuliah');
        $this->importData('konfigurasi');
        $this->importData('sequance');
        $this->importData('pembiayaan_lainnya');
        $this->importData('role');

        // Create super admin user
        echo "Create super admin user.... \r\n";
        $orang_id = $orang->create(['nama' => 'Admin'])->id;
        $user->create([
            'username' => 'admin',
            'password' => 'admin',
            'role' => [['id' => 5]],
            'orang_id' => $orang_id
        ]);

        echo "Create fake Konfigurasi data.... \r\n";
        $this->konfigurasiData();

        echo "Create fake Paket data.... \r\n";
        $this->paketData();

        echo "import data karyawan.... \r\n";
        $this->importKaryawan();

        echo "Create fake Panitia data using faker.... \r\n";
        $this->createPanitia(2);

        echo "Create fake PMB data using faker.... \r\n";
        $this->mahasiswaData($this->total_mahasiswa);

        echo "Create fake Karyawan data using faker.... \r\n";
        $this->karyawanData();

    }
}