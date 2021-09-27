<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Kwitansi extends BaseModel
{
	protected $table = 'kwitansi';
	protected $with = ['paket', 'orang'];

	public $status_enum = [
		"draft" => "Draft",
		"terverifikasi" => "Terverifikasi",
	];

	public function paket()
	{
		return $this->hasOne(Paket::class, 'paket_id', 'id');
	}

	public function orang()
	{
		return $this->belongsTo(Orang::class, 'orang_id', 'id');
	}
}