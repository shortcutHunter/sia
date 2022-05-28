<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Karyawan extends BaseModel
{
	protected $table = 'karyawan';
	protected $with = ['orang'];

	public $selection_fields = ['status', 'jenis_karyawan'];

	protected $appends = ['status_label', 'jenis_karyawan_label'];

	public $jenis_karyawan_enum = [
		"dosen" => "Dosen",
		"pegawai" => "Pegawai",
		"akademik" => "Akademik",
		"keuangan" => "Keuangan",
		"panitia" => "Panitia"
	];

	public $status_enum = [
		"aktif" => 'Aktif',
		"nonaktif" => 'Nonaktif',
	];

	public static $relation = [
		['name' => 'orang', 'is_selection' => false, 'skip' => false],
	];

	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	public function getJenisKaryawanLabelAttribute() {
		$jenis_karyawan_enum = $this->jenis_karyawan_enum;
		$label = null;

		if ($this->jenis_karyawan) {
			$label = $jenis_karyawan_enum[$this->jenis_karyawan];
		}

		return $label;
	}

	public static function create(array $attributes = [])
	{
		$model = parent::create($attributes);
		$object_user = self::getModelByName('user');
		$user = $object_user->create([
			'orang_id' => $model->orang_id,
			'role' => $model->jenis_karyawan,
			'username' => $model->orang->nik
		]);

		return $model;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('jenis_karyawan', $attributes)) {
			$this->orang->user->update(['role' => $attributes['jenis_karyawan']]);
		}

		return parent::update($attributes, $options);
	}

	public function orang()
	{
		return $this->hasOne(Orang::class, 'id', 'orang_id');
	}

	public function dosen_pa()
	{
		return $this->hasOne(DosenPa::class, 'karyawan_id', 'id');
	}
	public function dosen_pjmk()
	{
		return $this->hasOne(DosenPjmk::class, 'karyawan_id', 'id');
	}
}