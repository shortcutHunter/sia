<?php

namespace App\Models;

use App\Models\BaseModel;

final class JurusanModel extends BaseModel
{
  protected $table_name = "jurusan";

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
