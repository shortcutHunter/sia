<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Konfigurasi extends BaseModel
{
	protected $table = 'konfigurasi';
	protected $with = ['semester'];

	public static $relation = [
		['name' => 'semester', 'is_selection' => true, 'skip' => false]
	];

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

}