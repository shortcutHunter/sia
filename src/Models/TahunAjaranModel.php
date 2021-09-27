<?php

namespace App\Models;

use App\Models\BaseModel;

final class TahunAjaranModel extends BaseModel
{
  protected $table_name = "tahun_ajaran";

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'aktif' => 'Aktif',
      'nonaktif' => 'Nonaktif'
    ];
    $this->selection = $selection;
  }
}
