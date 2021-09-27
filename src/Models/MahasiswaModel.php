<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\OrangModel;
use App\Models\TahunAjaranModel;
use Psr\Container\ContainerInterface;

final class MahasiswaModel extends BaseModel
{
  protected $table_name = "mahasiswa";
  protected $relation = ['orang', 'tahun_ajaran'];

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->orang = new OrangModel($this->container);
    $this->tahun_ajaran = new TahunAjaranModel($this->container);
  }

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'mahasiswa' => 'Mahasiswa',
      'alumni' => 'Alumni',
      'dropout' => 'Drop Out'
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
      $this->orang->create($value['orang']);
      $orang_id = $this->orang->data->id;
      $value['orang_id'] = $orang_id;
      unset($value['orang']);
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
