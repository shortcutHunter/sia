<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Konfigurasi extends BaseModel
{
	protected $table = 'konfigurasi';
	protected $with = ['semester', 'tahun_ajaran'];

	public static $relation = [
		['name' => 'semester', 'is_selection' => true, 'skip' => false],
		['name' => 'tahun_ajaran', 'is_selection' => true, 'skip' => false]
	];

	public static $file_fields = ['pernyataan'];

	public function getStatusSmtpAttribute($value) {
		return $value ? true : false;
	}

	public function getRegistrasiAttribute($value) {
		return $value ? true : false;
	}

	public function getNextNIM()
	{
		$konfigurasi = self::getModelByName('konfigurasi')->first();
		$sequance_nim = $konfigurasi->sequance_nim + 1;
		$konfigurasi->update(['sequance_nim' => $sequance_nim]);

		return $sequance_nim;
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

	public function tahun_ajaran()
	{
		return $this->hasOne(TahunAjaran::class, 'id', 'tahun_ajaran_id');
	}

}