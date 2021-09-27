<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Paket extends BaseModel
{
	protected $table = 'paket';
	protected $with = ['item'];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public function item()
	{
		return $this->hasMany(Item::class, 'paket_id', 'id');
	}
}