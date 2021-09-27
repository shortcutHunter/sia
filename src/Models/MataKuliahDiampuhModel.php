<?php

namespace App\Models;

use App\Models\BaseModel;

final class MataKuliahDiampuhModel extends BaseModel
{
  protected $table_name = "mata_kuliah_diampuh";
  protected $relation = ['mata_kuliah'];
}
