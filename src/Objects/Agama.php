<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Agama extends BaseModel
{
	protected $table = 'agama';

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public $selection_fields = ['status'];

	public $like_fields = ['nama'];
}