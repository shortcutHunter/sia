<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\SequanceModel;
use Psr\Container\ContainerInterface;

final class ItemModel extends BaseModel
{
  protected $table_name = "item";

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->sequance = new SequanceModel($this->container);
  }

  public function create($value)
  {
    $value['kode'] = $this->sequance->next_code("Kode Item Paket", "I");
    parent::create($value);
  }
}
