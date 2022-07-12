<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Alumni extends BaseModel
{
	protected $table = 'alumni';

	public static $date_fields = ['tanggal_kerja'];

	protected $casts = [
	    'tanggal_kerja' => 'datetime:d/m/Y',
	];
}