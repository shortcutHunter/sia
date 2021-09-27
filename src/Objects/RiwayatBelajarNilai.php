<?php

namespace App\Objects;

use App\Objects\BaseModel;

class RiwayatBelajarNilai extends BaseModel
{
	protected $table = 'riwayat_belajar_nilai';
	protected $with = ['nilai'];

	public function riwayat_belajar_detail()
	{
		return $this->belongsTo(RiwayatBelajarDetail::class, 'riwayat_belajar_detail_id', 'id');
	}

	public function nilai()
	{
		return $this->hasOne(Nilai::class, 'nilai_id', 'id');
	}

}