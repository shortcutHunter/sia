<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\MahasiswaModel;
use App\Models\TahunAjaranModel;

final class PengajuanKsModel extends BaseModel
{
  protected $table_name = "pengajuan_ks";
  protected $relation = ['mahasiswa', 'tahun_ajaran'];

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'draft' => 'Draft',
      'proses' => 'Proses',
      'terima' => 'Terima',
      'tolak' => 'Tolak',
    ];
    $this->selection = $selection;
  }
}
