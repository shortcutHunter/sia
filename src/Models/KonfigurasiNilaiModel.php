<?php

namespace App\Models;

use App\Models\BaseModel;

final class KonfigurasiNilaiModel extends BaseModel
{
  protected $table_name = "konfigurasi_nilai";
  protected $relation = ['nilai', 'mata_kuliah_diampuh'];
}
