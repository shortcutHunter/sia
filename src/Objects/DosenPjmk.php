<?php

namespace App\Objects;

use App\Objects\BaseModel;

class DosenPjmk extends BaseModel
{
	protected $table = 'dosen_pjmk';
	protected $with = ['karyawan', 'tahun_ajaran', 'semester', 'mata_kuliah_diampuh'];

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

	public function mata_kuliah_diampuh()
	{
		return $this->hasMany(MataKuliahDiampuh::class, 'dosen_pjmk_id', 'id');
	}

}