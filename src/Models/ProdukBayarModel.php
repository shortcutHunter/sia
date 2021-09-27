<?php

namespace App\Models;

use App\Models\BaseModel;

final class ProdukBayarModel extends BaseModel
{
  protected $table_name = "produk_bayar";
  protected $relation = ['paket_bayar'];
}
