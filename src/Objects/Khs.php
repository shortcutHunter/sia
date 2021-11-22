<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Khs extends BaseModel
{
	protected $table = 'khs';
	protected $with = ['khs_detail', 'semester'];

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
	}

	public function khs_detail()
	{
		return $this->hasMany(KhsDetail::class, 'khs_id', 'id');
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}
}