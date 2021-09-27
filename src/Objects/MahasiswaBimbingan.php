<?php

namespace App\Objects;

use App\Objects\BaseModel;

class MahasiswaBimbingan extends BaseModel
{
	protected $table = 'mahasiswa_bimbingan';
	protected $with = ['mahasiswa'];

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
	}

	public function dosen_pa()
	{
		return $this->belongsTo(DosenPa::class, 'dosen_pa_id', 'id');
	}

}