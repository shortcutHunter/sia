<?php

namespace App\Models;

use App\Models\BaseModel;

final class AgamaModel extends BaseModel
{
  protected $table_name = "agama";

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
