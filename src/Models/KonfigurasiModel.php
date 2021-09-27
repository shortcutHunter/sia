<?php

namespace App\Models;

use App\Models\BaseModel;

final class KonfigurasiModel extends BaseModel
{
  protected $table_name = "konfigurasi";
  protected $relation = ['paket'];
}
