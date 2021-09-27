<?php

namespace App\Objects;

use App\Objects\BaseModel;

class RiwayatBelajar extends BaseModel
{
	protected $table = 'riwayat_belajar';
	protected $with = ['semester', 'riwayat_belajar_detail', 'mahasiswa'];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'semester_id', 'id');
	}

	public function riwayat_belajar_detail()
	{
		return $this->hasMany(RiwayatBelajarDetail::class, 'riwayat_belajar_id', 'id');
	}

}