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
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

	public function riwayat_belajar_detail()
	{
		return $this->hasMany(RiwayatBelajarDetail::class, 'riwayat_belajar_id', 'id');
	}

	public static function create(array $attributes = [])
	{
		$rdb_value = false;
		if (array_key_exists('riwayat_belajar_detail', $attributes)) {
			$rdb_value = $attributes['riwayat_belajar_detail'];
			unset($attributes['riwayat_belajar_detail']);
		}
		$riwayat_belajar = parent::create($attributes);

		if ($rdb_value) {
			$rdb_value['riwayat_belajar_id'] = $riwayat_belajar->id;
			$riwayat_belajar_detail_obj = self::getModelByName('riwayat_belajar_detail');
			$riwayat_belajar_detail = $riwayat_belajar_detail_obj->create($rdb_value);
		}

		return $riwayat_belajar;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('riwayat_belajar_detail', $attributes)) {
			$rdb_value = $attributes['riwayat_belajar_detail'];
			$rdb_value['riwayat_belajar_id'] = $this->id;
			$riwayat_belajar_detail_obj = self::getModelByName('riwayat_belajar_detail');
			$where_condition = [['riwayat_belajar_id', $this->id], ['mata_kuliah_id', $rdb_value['mata_kuliah_id']]];
			$riwayat_belajar_detail = $riwayat_belajar_detail_obj->where($where_condition);

			if ($riwayat_belajar_detail->count() == 0) {
				$riwayat_belajar_detail = $riwayat_belajar_detail_obj->create($rdb_value);
			}else{
				$riwayat_belajar_detail->first()->update($rdb_value);
			}
			unset($attributes['riwayat_belajar_detail']);
		}
		return parent::update($attributes, $options);
	}
}