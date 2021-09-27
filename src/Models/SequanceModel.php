<?php

namespace App\Models;

use App\Models\BaseModel;

final class SequanceModel extends BaseModel
{
  protected $table_name = "sequance";

  public function next_code($nama, $prefix='K')
  {
    $this->get([['nama', $nama]]);
    $data = $this->data;

    if ($data->isEmpty()) {
      $value = ['kode' => $prefix, 'nama' => $nama];
      $this->create($value);
    }
    $sequance_data = $this->data[0];

    $next_code = $sequance_data->kode."-".sprintf('%03d', $sequance_data->nomor);
    $this->update($sequance_data->id, ['nomor' => ($sequance_data->nomor+1)]);
    return $next_code;
  }
}
