<?php

namespace App\Models;

use App\Models\BaseModel;

final class NilaiMahasiswaModel extends BaseModel
{
  protected $table_name = "nilai_mahasiswa";
  protected $relation = ['sks'];
}
