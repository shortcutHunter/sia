<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\SequanceModel;
use Psr\Container\ContainerInterface;

final class KwitansiModel extends BaseModel
{
  protected $table_name = "kwitansi";
  protected $relation = ['paket', 'orang'];

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->sequance = new SequanceModel($this->container);
  }

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'draft' => 'Draft',
      'terverifikasi' => 'Terverifikasi',
    ];
    $this->selection = $selection;
  }

  public function create($value)
  {
    $value['kode'] = $this->sequance->next_code("Kode Kwitansi", "KW");

    if (array_key_exists("tanggal", $value)){
      $tanggal = $value['tanggal'];
      $tanggal = \DateTime::createFromFormat("d/m/Y", $tanggal)->format("Y-m-d");
      $value['tanggal'] = $tanggal;
    }
    parent::create($value);
  }

  public function update($id, $value)
  {
    if (array_key_exists("tanggal", $value)){
      $tanggal = $value['tanggal'];
      $tanggal = \DateTime::createFromFormat("d/m/Y", $tanggal)->format("Y-m-d");
      $value['tanggal'] = $tanggal;
    }
    
    parent::update($id, $value);
  }

}
