<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\AgamaModel;
use App\Models\UserModel;
use Psr\Container\ContainerInterface;

final class OrangModel extends BaseModel
{
  protected $table_name = "orang";
  protected $relation = ['agama'];

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->user = new UserModel($container);
  }
  
  public function selection()
  {
    $jenis_kelamin = [
      "l" => "Laki Laki",
      "p" => "Perempuan"
    ];

    $status = [
      "aktif" => "Aktif",
      "inaktif" => "  Inaktif",
      "berhenti" => "  Berhenti"
    ];

    $this->selection = [
      "jenis_kelamin" => $jenis_kelamin,
      "status" => $status
    ];
  }

  public function create($value)
  {
    $no_create = false;
    $role = false;
    if (array_key_exists("no_create_user", $value)){
      unset($value['no_create_user']);
      $no_create = true;
    }

    if (array_key_exists("role", $value)){
      $role = $value['role'];
      unset($value['role']);
    }

    if (array_key_exists("tanggal_lahir", $value)){
      $tanggal_lahir = $value['tanggal_lahir'];
      $tanggal_lahir = \DateTime::createFromFormat("d/m/Y", $tanggal_lahir)->format("Y-m-d");
      $value['tanggal_lahir'] = $tanggal_lahir;
    }

    parent::create($value);
    if (!$no_create) {
      if ($role) {
        $this->user->create(['orang_id' => $this->data->id, 'role' => $role]);
      }else{
        $this->user->create(['orang_id' => $this->data->id]);
      }
    }
  }

  public function update($id, $value)
  {
    if (array_key_exists("tanggal_lahir", $value)){
      $tanggal_lahir = $value['tanggal_lahir'];
      $tanggal_lahir = \DateTime::createFromFormat("d/m/Y", $tanggal_lahir)->format("Y-m-d");
      $value['tanggal_lahir'] = $tanggal_lahir;
    }
    
    parent::update($id, $value);
  }

}
