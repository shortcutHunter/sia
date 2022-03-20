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

	protected $appends = ['status_label'];
	
	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	public static function create(array $attributes = [])
	{
		$object_dosen_pa = self::getModelByName('dosen_pa');
		$dosen_pa_data = $object_dosen_pa->where('status', 'aktif')->where('karyawan_id', $attributes['karyawan_id']);
		$isexist = $dosen_pa_data->count() > 0;
		if ($isexist) {
			throw new \Exception("1 Karyawan tidak dapat memiliki 2 record dosen pa.");
		}
		$dosen_pjmk = parent::create($attributes);
		return $dosen_pjmk;
	}

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