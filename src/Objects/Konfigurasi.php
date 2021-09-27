<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Konfigurasi extends BaseModel
{
	protected $table = 'konfigurasi';
	protected $with = ['paket'];

	public function paket()
	{
		return $this->hasOne(Paket::class, 'id', 'paket_id');
	}

}