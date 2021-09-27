<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\AgamaModel;
use Psr\Container\ContainerInterface;

final class KaryawanModel extends BaseModel
{
  protected $table_name = "karyawan";
  protected $relation = ['orang'];

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->orang = new OrangModel($this->container);
  }

  public function selection()
  {
    $selection = [];
    $selection['jenis_karyawan'] = [
      'dosen' => 'Dosen',
      'pegawai' => 'Pegawai Umum'
    ];
    $selection['status'] = [
      'aktif' => 'Aktif',
      'nonaktif' => 'Nonaktif'
    ];
    $this->selection = $selection;
  }

  public function get($query=[], $search=false)
  {
    if ($search) {
      $query = "orang_id IN (SELECT id FROM orang WHERE LOWER(nama) like '%".$search."%')";
      return parent::raw($query);
    }

    return parent::get($query, $search);
  }

  public function create($value)
  {
    if (array_key_exists("orang", $value))
    {
      if ($value['jenis_karyawan'] == 'dosen') {
        $value['orang']['role'] = 'dosen';
      }else {
        if ($value['role'] == 'keuangan') {
          $value['orang']['role'] = 'keuangan';
        }else{
          $value['orang']['role'] = 'akademik';
        }
      }
      $this->orang->create($value['orang']);
      $orang_id = $this->orang->data->id;
      $value['orang_id'] = $orang_id;
      unset($value['orang']);
      unset($value['role']);
    }

    parent::create($value);
  }

  public function update($id, $value)
  {
    $this->read($id);
    
    if (array_key_exists("orang", $value))
    {
      $this->orang->update($this->data->orang_id, $value['orang']);
      unset($value['orang']);
    }
    parent::update($id, $value);
  }

  public function delete($id)
  {
    $this->read($id);
    parent::delete($id);
    return $this->orang->delete($this->data->orang_id);
  }

}
