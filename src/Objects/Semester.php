<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Semester extends BaseModel
{
	protected $table = 'semester';

	public $tipe_enum = [
		"pendek" => "Pendek",
		"genap" => "Genap",
		"ganjil" => "Ganjil",
	];

}