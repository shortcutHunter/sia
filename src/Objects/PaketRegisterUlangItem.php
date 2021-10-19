<?php

namespace App\Objects;

use App\Objects\BaseModel;

class PaketRegisterUlangItem extends BaseModel
{
	protected $table = 'paket_register_ulang_item';
	protected $with = ['item'];

	public function item()
	{
		return $this->hasOne(Item::class, 'id', 'item_id');
	}

}