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
	
	public $selection_fields = ['status'];

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
		return $this->belongsToMany(Semester::class, 'dosen_pjmk_semester', 'dosen_pjmk_id', 'semester_id');
	}

	public function mata_kuliah_diampuh()
	{
		return $this->hasMany(MataKuliahDiampuh::class, 'dosen_pjmk_id', 'id');
	}

	public function createSemester($semesters)
	{
		$created_dps_ids = [];

		foreach ($semesters as $key => $value) {
			$dps_obj = self::getModelByName('dosen_pjmk_semester');
			$dps_value = [
				'semester_id' => $value['id'],
				'dosen_pjmk_id' => $this->id
			];

			$dps = $dps_obj->where([
				['semester_id', $value['id']],
				['dosen_pjmk_id', $this->id]
			])->first();

			if (empty($dps)) {
				$dps = $dps_obj->create($dps_value);	
			}

			array_push($created_dps_ids, $dps->id);
		}

		$dps_obj = self::getModelByName('dosen_pjmk_semester');
		$dps = $dps_obj->where('dosen_pjmk_id', $this->id)->whereNotIn('id', $created_dps_ids);
		$dps->delete();
	}

	public static function create(array $attributes = [])
	{
		$object_dosen_pjmk = self::getModelByName('dosen_pjmk');
		$dosen_pjmk_data = $object_dosen_pjmk->where('status', 'aktif')->where('karyawan_id', $attributes['karyawan_id']);
		$isexist = $dosen_pjmk_data->count() > 0;
		$semester = false;

		if (array_key_exists('karyawan', $attributes)) {
			unset($attributes['karyawan']);
		}
		if (array_key_exists('mata_kuliah_diampuh', $attributes)) {
			unset($attributes['mata_kuliah_diampuh']);
		}

		if ($isexist) {
			throw new \Exception("1 Karyawan tidak dapat memiliki 2 record dosen pjmk yang aktif.");
		}

		if (array_key_exists('semester', $attributes)) {
			$semester = $attributes['semester'];

			unset($attributes['semester']);
		}

		$dosen_pjmk = parent::create($attributes);

		if ($semester) {
			$dosen_pjmk->createSemester($semester);
		}

		return $dosen_pjmk;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('karyawan', $attributes)) {
			unset($attributes['karyawan']);
		}
		if (array_key_exists('mata_kuliah_diampuh', $attributes)) {
			unset($attributes['mata_kuliah_diampuh']);
		}
		
		if (array_key_exists('semester', $attributes)) {
			$semester = $attributes['semester'];
			$this->createSemester($semester);

			unset($attributes['semester']);
		}

		return parent::update($attributes, $options);
	}

	public function delete()
	{
		$object_mata_kuliah_diampuh = self::getModelByName('mata_kuliah_diampuh');
		$mata_kuliah_diampuh = $object_mata_kuliah_diampuh->where('dosen_pjmk_id', $this->id);

		$object_konfigurasi_nilai = self::getModelByName('konfigurasi_nilai');
		$konfigurasi_nilai = $object_konfigurasi_nilai->whereIn('mata_kuliah_diampuh_id', $mata_kuliah_diampuh->pluck('id')->toArray());
		$konfigurasi_nilai->delete();

		$dpa_obj = self::getModelByName('dosen_pjmk_semester');
		$dpa = $dpa_obj->where('dosen_pjmk_id', $this->id);
		$dpa->delete();
		
		$mata_kuliah_diampuh->delete();

		return parent::delete();
	}

}