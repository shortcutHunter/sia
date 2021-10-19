<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Konfigurasi extends BaseModel
{
	protected $table = 'konfigurasi';
	protected $with = ['semester', 'tahun_ajaran'];

	public static $relation = [
		['name' => 'semester', 'is_selection' => true, 'skip' => false],
		['name' => 'tahun_ajaran', 'is_selection' => true, 'skip' => false]
	];

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

	public function tahun_ajaran()
	{
		return $this->hasOne(TahunAjaran::class, 'id', 'tahun_ajaran_id');
	}

}