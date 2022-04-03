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
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

	public function mata_kuliah_diampuh()
	{
		return $this->hasMany(MataKuliahDiampuh::class, 'dosen_pjmk_id', 'id');
	}

	public static function create(array $attributes = [])
	{
		$object_dosen_pjmk = self::getModelByName('dosen_pjmk');
		$dosen_pjmk_data = $object_dosen_pjmk->where('status', 'aktif')->where('karyawan_id', $attributes['karyawan_id']);
		$isexist = $dosen_pjmk_data->count() > 0;
		if ($isexist) {
			throw new \Exception("1 Karyawan tidak dapat memiliki 2 record dosen pjmk yang aktif.");
		}
		$dosen_pjmk = parent::create($attributes);
		return $dosen_pjmk;
	}

	public function delete()
	{
		$object_mata_kuliah_diampuh = self::getModelByName('mata_kuliah_diampuh');
		$mata_kuliah_diampuh = $object_mata_kuliah_diampuh->where('dosen_pjmk_id', $this->id);

		$object_konfigurasi_nilai = self::getModelByName('konfigurasi_nilai');
		$konfigurasi_nilai = $object_konfigurasi_nilai->whereIn('mata_kuliah_diampuh_id', $mata_kuliah_diampuh->pluck('id')->toArray());
		$konfigurasi_nilai->delete();

		$mata_kuliah_diampuh->delete();
		
		return parent::delete();
	}

}