<?php

namespace App\Objects;

use App\Objects\BaseModel;

class KonfigurasiNilai extends BaseModel
{
	protected $table = 'konfigurasi_nilai';
	protected $with = ['nilai'];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public function mata_kuliah_diampuh()
	{
		return $this->belongsTo(MataKuliahDiampuh::class, 'mata_kuliah_diampuh_id', 'id');
	}

	public function nilai()
	{
		return $this->hasOne(Nilai::class, 'id', 'nilai_id');
	}
	
}