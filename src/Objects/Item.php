<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Item extends BaseModel
{
	protected $table = 'item';

	public function paket()
	{
		return $this->belongsTo(Paket::class, 'paket_id', 'id');
	}
}