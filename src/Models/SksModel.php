<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\PengajuanKSModel;
use App\Models\MataKuliahModel;

final class SksModel extends BaseModel
{
  protected $table_name = "sks";
  protected $relation = ['ks', 'mata_kuliah'];

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'terima' => 'Terima',
      'tolak' => 'Tolak',
    ];
    $this->selection = $selection;
  }

  public function updateField(&$data)
  {
    parent::updateField($data);
    $lambang = ['4' => 'A', '3' => 'B', '2' => 'C', '1' => 'D', '0' => 'E'];
    $data->simbol = $lambang[$data->nilai_mutu];
  }
}
