<?php

namespace App\Objects;

use App\Objects\BaseModel;

class DosenPa extends BaseModel
{
	protected $table = 'dosen_pa';
	protected $with = ['karyawan', 'tahun_ajaran', 'semester', 'mahasiswa_bimbingan'];

	public static $relation = [
		['name' => 'semester', 'is_selection' => true, 'skip' => true],
		['name' => 'tahun_ajaran', 'is_selection' => true, 'skip' => true],
	];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public function karyawan()
	{
		return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
	}

	public function tahun_ajaran()
	{
		return $this->hasOne(TahunAjaran::class, 'id', 'tahun_ajaran_id');
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}


	public function mahasiswa_bimbingan()
	{
		return $this->hasMany(MahasiswaBimbingan::class, 'dosen_pa_id', 'id');
	}
}