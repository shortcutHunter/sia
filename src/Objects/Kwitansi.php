<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Kwitansi extends BaseModel
{
	protected $table = 'kwitansi';
	protected $with = ['paket', 'orang'];
	public static $date_fields = ['tanggal'];
	
	public $selection_fields = ['status'];

	public static $relation = [
		['name' => 'paket', 'is_selection' => false, 'skip' => true]
	];

	public $status_enum = [
		"draft" => "Draft",
		"terverifikasi" => "Terverifikasi",
	];

	public function paket()
	{
		return $this->hasOne(Paket::class, 'id', 'paket_id');
	}

	public function orang()
	{
		return $this->belongsTo(Orang::class, 'orang_id', 'id');
	}
}