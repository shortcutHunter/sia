<?php

namespace App\Models;

use App\Models\BaseModel;

final class PaketRegisterUlangModel extends BaseModel
{
  protected $table_name = "paket_register_ulang";
  protected $relation = ['paket', 'tahun_ajaran'];


}
