<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\PengajuanKSModel;
use App\Models\MataKuliahModel;

final class KsModel extends BaseModel
{
  protected $table_name = "ks";
  protected $relation = ['mahasiswa', 'mata_kuliah'];

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'aktif' => 'Aktif',
      'nonaktif' => 'Nonaktif',
    ];
    $this->selection = $selection;
  }
}
