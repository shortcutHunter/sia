<?php

namespace App\Objects;

use App\Objects\BaseModel;

class PengajuanKsDetail extends BaseModel
{
	protected $table = 'pengajuan_ks_detail';
	protected $with = ['mata_kuliah'];

	public $status_enum = [
		"terima" => "Terima",
		"tolak" => "Tolak",
	];

	public function mata_kuliah()
	{
		return $this->hasOne(MataKuliah::class, 'id', 'mata_kuliah_id');
	}

	public function pengajuan_ks()
	{
		return $this->belongsTo(PengajuanKs::class, 'pengajuan_ks_id', 'id');
	}

}