<?php

namespace App\Objects;

use App\Objects\BaseModel;

class RegisterUlang extends BaseModel
{
	protected $table = 'register_ulang';
	protected $with = ['mahasiswa'];

	public $status_enum = [
		"proses" => "Proses",
		"terverifikasi" => "Terverifikasi",
		"tolak" => "Tolak",
	];

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
	}
}