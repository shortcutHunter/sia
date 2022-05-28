<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Panitia extends BaseModel
{
	protected $table = 'panitia';

	public $like_fields = ['nama'];
}