<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\PengajuanKSModel;
use App\Models\MataKuliahModel;

final class RelasiPengajuanKsModel extends BaseModel
{
  protected $table_name = "relasi_pengajuan_ks";
  protected $relation = ['pengajuan_ks', 'mata_kuliah'];

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'terima' => 'Terima',
      'tolak' => 'Tolak',
    ];
    $this->selection = $selection;
  }
}
