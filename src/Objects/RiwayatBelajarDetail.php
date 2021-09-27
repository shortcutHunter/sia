<?php

namespace App\Objects;

use App\Objects\BaseModel;

class RiwayatBelajarDetail extends BaseModel
{
	protected $table = 'riwayat_belajar_detail';
	protected $with = ['mata_kuliah', 'riwayat_belajar_nilai'];

	public function riwayat_belajar()
	{
		return $this->belongsTo(RiwayatBelajar::class, 'riwayat_belajar_id', 'id');
	}

	public function mata_kuliah()
	{
		return $this->hasOne(MataKuliah::class, 'mata_kuliah_id', 'id');
	}

	public function riwayat_belajar_nilai()
	{
		return $this->belongsTo(RiwayatBelajarNilai::class, 'riwayat_belajar_detail_id', 'id');
	}

}