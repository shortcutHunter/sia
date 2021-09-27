<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\SequanceModel;
use Psr\Container\ContainerInterface;

final class PaketModel extends BaseModel
{
  protected $table_name = "paket";

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->sequance = new SequanceModel($this->container);
  }

  public function create($value)
  {
    $value['kode'] = $this->sequance->next_code("Kode Paket Pembayaran", "P");
    parent::create($value);
  }
}
