<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\OrangModel;
use App\Models\TahunAjaranModel;
use Psr\Container\ContainerInterface;

final class MahasiswaBimbinganModel extends BaseModel
{
  protected $table_name = "mahasiswa_bimbingan";
  protected $relation = ['mahasiswa'];
}
