<?php

namespace App\Models;

use App\Models\BaseModel;

final class RelasiPaketModel extends BaseModel
{
  protected $table_name = "relasi_paket";
  protected $relation = ['produk', 'paket'];
}
