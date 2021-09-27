<?php

namespace App\Models;

use App\Models\BaseModel;

final class DosenPaModel extends BaseModel
{
  protected $table_name = "dosen_pa";
  protected $relation = ['karyawan', 'tahun_ajaran'];

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'aktif' => 'Aktif',
      'nonaktif' => 'Nonaktif',
    ];
    $this->selection = $selection;
  }

  public function get($query=[], $search=false)
  {
    if ($search) {
      $query = [['tahun_ajaran_id', $search]];
      $search = false;
    }

    return parent::get($query, $search);
  }
}
