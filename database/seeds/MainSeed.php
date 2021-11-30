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

    function run()
    {        
        $settings = require __DIR__ . '/../../config/settings.php';
        $db_name = $settings['db']['database'];

        $user = new User;
        $orang = new Orang;

        // Drop and re-import database
        exec('..\mysql\bin\mysql -u root -e "DROP DATABASE IF EXISTS '.$db_name.'; create database '.$db_name.'"');
        exec('..\mysql\bin\mysql -u root '.$db_name.' < database/SIA.sql');

        // create super admin user
        $orang_id = $orang->create(['nama' => 'Admin'])->id;
        $user->create([
            'username' => 'admin',
            'password' => 'admin',
            'role' => 'admin',
            'orang_id' => $orang_id
        ]);

        // import data
        $this->importData('agama');
        $this->importData('nilai');
        $this->importData('semester');
        $this->importData('jurusan');
        $this->importData('mata_kuliah');
        $this->importData('konfigurasi');
        $this->importData('sequance');
    }
}