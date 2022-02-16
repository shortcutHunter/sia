<?php

use App\Objects\User;
use App\Objects\Orang;

class MainSeed {

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

        $tahun_ajaran = $tahun_ajaran_obj->create([
            "nama" => date("Y") . "/" . date('Y', strtotime('+1 year')),
            "tahun" => date("Y")
        ]);

        $konfigurasi = $konfigurasi_obj->first();
        $konfigurasi->update([
            "tahun_ajaran_id" => $tahun_ajaran->id,
            "semester_id" => 1
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

        $url = $faker->imageUrl($width = 640, $height = 480);
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
        ];

        return $orang_value;
    }

    function mahasiswaData($total)
    {
        $faker = Faker\Factory::create();

        $bukti_pembayaran = $this->fakeImages();

        for ($i=0; $i < $total; $i++) { 
            $pmb_obj = $this->getObject('pmb');
            $penerbitan_nim_obj = $this->getObject('penerbitan_nim');

            $bukti_pembayaran = $this->fakeImages();
            $pmb_value = [
                "orang" => $this->fakeDataOrang(),
                "bukti_pembayaran" => $bukti_pembayaran,
                "pernyataan" => 1,
                "tanggal_pendaftaran" => date("d/m/Y")
            ];

            $pmb = $pmb_obj->create($pmb_value);
            $pmb->update(["status" => "ujian"]);
            $pmb->update(["status" => "terima"]);

            $penerbitan_nim = $penerbitan_nim_obj->where('pmb_id', $pmb->id)->first();
            $penerbitan_nim->update([
                "status" => 'terbit'
            ]);
        }
    }

    function createKaryawan($jenis_karyawan)
    {
        $faker = Faker\Factory::create();
        $karyawan_obj = $this->getObject('karyawan');
        $karyawan_value = [
            "ni" => $faker->randomDigitNotNull,
            "jenis_karyawan" => $jenis_karyawan,
            "orang" => $this->fakeDataOrang()
        ];
        $karyawan = $karyawan_obj->create($karyawan_value);

        return $karyawan;
    }

    function createDosenPjmk($semester_id, $mata_kuliah_diampuh_ids, $jenis_karyawan)
    {
        $karyawan = $this->createKaryawan($jenis_karyawan);
        $dosen_pjmk_obj = $this->getObject('dosen_pjmk');
        $tahun_ajaran_obj = $this->getObject('tahun_ajaran');
        $nilai_obj = $this->getObject('nilai');

        $tahun_ajaran = $tahun_ajaran_obj->first();
        $nilai = $nilai_obj->all();

        $dosen_pjmk_value = [
            "karyawan_id" => $karyawan->id,
            "tahun_ajaran_id" => $tahun_ajaran->id,
            "semester_id" => $semester_id
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

    function createDosenPa($semester_id, $jenis_karyawan)
    {
        $dosen_pa_obj = $this->getObject('dosen_pa');
        $tahun_ajaran_obj = $this->getObject('tahun_ajaran');

        $karyawan = $this->createKaryawan($jenis_karyawan);
        $tahun_ajaran = $tahun_ajaran_obj->first();

        $dosen_pa_value = [
            "karyawan_id" => $karyawan->id,
            "tahun_ajaran_id" => $tahun_ajaran->id,
            "semester_id" => $semester_id
        ];

        $dosen_pa = $dosen_pa_obj->create($dosen_pa_value);

        return $dosen_pa;
    }

    function dosenData($jenis_karyawan)
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
                $dosen_pjmk = $this->createDosenPjmk($value->semester_id, $mata_kuliah_diampuh_ids, $jenis_karyawan);
                $mata_kuliah_diampuh_ids = [];
            }

            if ($is_last) {
                array_push($semester_ids, $value->semester_id);
            }
        }

        foreach($semester_ids as $semester_id) {
            $this->createDosenPa($semester_id, $jenis_karyawan);
        }
    }

    function karyawanData()
    {
        $faker = Faker\Factory::create();
        $karyawan_obj = $this->getObject('karyawan');

        foreach ($karyawan_obj->jenis_karyawan_enum as $key => $value) {
            
            if ($key == 'dosen') {
                $this->dosenData($key);
            }else{
                $this->createKaryawan($key);
            }
        }

    }

    function run()
    {        
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

        // Create super admin user
        echo "Create super admin user.... \r\n";
        $orang_id = $orang->create(['nama' => 'Admin'])->id;
        $user->create([
            'username' => 'admin',
            'password' => 'admin',
            'role' => 'admin',
            'orang_id' => $orang_id
        ]);

        // import data
        echo "Import data from excel.... \r\n";
        $this->importData('agama');
        $this->importData('nilai');
        $this->importData('semester');
        $this->importData('jurusan');
        $this->importData('mata_kuliah');
        $this->importData('konfigurasi');
        $this->importData('sequance');

        echo "Create fake Konfigurasi data.... \r\n";
        $this->konfigurasiData();

        echo "Create fake Paket data.... \r\n";
        $this->paketData();

        echo "Create fake PMB data using faker.... \r\n";
        $this->mahasiswaData(100);

        echo "Create fake Karyawan data using faker.... \r\n";
        $this->karyawanData();
    }
}