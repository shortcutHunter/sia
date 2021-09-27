<?php

namespace App\Objects;

use App\Objects\BaseModel;

class MataKuliah extends BaseModel
{
	protected $table = 'mata_kuliah';
	protected $with = ['jurusan', 'semester'];

	public $like_fields = ['nama'];

	public function jurusan()
	{
		return $this->hasOne(Jurusan::class, 'id', 'jurusan_id');
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

}