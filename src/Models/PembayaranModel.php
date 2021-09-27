<?php

namespace App\Models;

use App\Models\BaseModel;

final class PembayaranModel extends BaseModel
{
  protected $table_name = "pembayaran";
  protected $relation = ['paket_bayar', 'orang'];

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'draft' => 'Draft',
      'verifikasi' => 'Terverifikasi',
      'tidak' => 'Tidak Valid',
      'batal' => 'Batal',
    ];
    $this->selection = $selection;
  }
}
