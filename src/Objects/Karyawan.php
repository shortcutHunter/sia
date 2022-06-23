<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Karyawan extends BaseModel
{
	protected $table = 'karyawan';
	protected $with = ['orang'];

	public $selection_fields = ['status'];

	protected $appends = ['status_label'];

	// public $jenis_karyawan_enum = [
	// 	"dosen" => "Dosen",
	// 	"pegawai" => "Pegawai",
	// 	"akademik" => "Akademik",
	// 	"keuangan" => "Keuangan",
	// 	"panitia" => "Panitia"
	// ];

	public $status_enum = [
		"aktif" => 'Aktif',
		"nonaktif" => 'Nonaktif',
	];

	public static $relation = [
		['name' => 'orang', 'is_selection' => false, 'skip' => false]
	];

	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	// public function getJenisKaryawanLabelAttribute() {
	// 	$jenis_karyawan_enum = $this->jenis_karyawan_enum;
	// 	$label = null;

	// 	if ($this->jenis_karyawan) {
	// 		$label = $jenis_karyawan_enum[$this->jenis_karyawan];
	// 	}

	// 	return $label;
	// }

	public static function create(array $attributes = [])
	{
		$user_value = [];

		if (array_key_exists('user', $attributes)) {
			$user_value = $attributes['user'];
			unset($attributes['user']);
		}

		$model = parent::create($attributes);
		$object_user = self::getModelByName('user');

		$user_value['orang_id'] = $model->orang_id;
		$user_value['username'] = $model->orang->nik;

		$user = $object_user->create($user_value);

		return $model;
	}

	public function update(array $attributes = [], array $options = [])
	{
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