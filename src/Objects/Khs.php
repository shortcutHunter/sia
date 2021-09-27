<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Khs extends BaseModel
{
	protected $table = 'khs';
	protected $with = ['mata_kuliah', 'semester'];

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
	}

	public function mata_kuliah()
	{
		return $this->hasOne(MataKuliah::class, 'mata_kuliah_id', 'id');
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'semester_id', 'id');
	}
}