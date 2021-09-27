<?php

namespace App\Models;

use App\Models\BaseModel;

final class PemberhentianModel extends BaseModel
{
  protected $table_name = "pemberhentian";
  protected $relation = ['mahasiswa'];
}
