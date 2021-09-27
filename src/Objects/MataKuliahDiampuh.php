<?php

namespace App\Objects;

use App\Objects\BaseModel;

class MataKuliahDiampuh extends BaseModel
{
	protected $table = 'mata_kuliah_diampuh';
	protected $with = ['mata_kuliah', 'konfigurasi_nilai'];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public function dosen_pjmk()
	{
		return $this->belongsTo(DosenPjmk::class, 'dosen_pjmk_id', 'id');
	}

	public function mata_kuliah()
	{
		return $this->hasOne(MataKuliah::class, 'id', 'mata_kuliah_id');
	}

	public function konfigurasi_nilai()
	{
		return $this->hasMany(KonfigurasiNilai::class, 'mata_kuliah_diampuh_id', 'id');
	}
	
}