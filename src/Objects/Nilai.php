<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Nilai extends BaseModel
{
	protected $table = 'nilai';

	public $like_fields = ['nama'];
}