<?php

namespace App\Objects;

use App\Objects\BaseModel;

class KhsDetail extends BaseModel
{
	protected $table = 'khs_detail';
	protected $with = ['mata_kuliah'];

	public function khs()
	{
		return $this->belongsTo(Khs::class, 'khs_id', 'id');
	}
	public function mata_kuliah()
	{
		return $this->hasOne(MataKuliah::class, 'id', 'mata_kuliah_id');
	}

	public function riwayat_belajar_detail()
	{
		return $this->hasOne(RiwayatBelajarDetail::class, 'id', 'riwayat_belajar_detail_id');
	}
}