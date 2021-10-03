<?php

namespace App\Objects;

use App\Objects\BaseModel;

class RiwayatBelajarDetail extends BaseModel
{
	protected $table = 'riwayat_belajar_detail';
	protected $with = ['mata_kuliah', 'riwayat_belajar_nilai'];

	public function riwayat_belajar()
	{
		return $this->belongsTo(RiwayatBelajar::class, 'riwayat_belajar_id', 'id');
	}

	public function mata_kuliah()
	{
		return $this->hasOne(MataKuliah::class, 'id', 'mata_kuliah_id');
	}

	public function riwayat_belajar_nilai()
	{
		return $this->hasMany(RiwayatBelajarNilai::class, 'riwayat_belajar_detail_id', 'id');
	}

	public static function create(array $attributes = [])
	{
		$riwayat_belajar_nilai = false;
		if (array_key_exists('riwayat_belajar_nilai', $attributes)) {
			$riwayat_belajar_nilai = $attributes['riwayat_belajar_nilai'];
			unset($attributes['riwayat_belajar_nilai']);
		}
		$riwayat_belajar_detail = parent::create($attributes);

		if ($riwayat_belajar_nilai) {
			foreach ($riwayat_belajar_nilai as $value) {
				$value['riwayat_belajar_detail_id'] = $riwayat_belajar_detail->id;
				$riwayat_belajar_nilai_obj = self::getModelByName('riwayat_belajar_nilai');
				$riwayat_belajar_nilai = $riwayat_belajar_nilai_obj->create($value);
			}
		}

		return $riwayat_belajar_detail;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('riwayat_belajar_nilai', $attributes)) {
			$riwayat_belajar_nilai = $attributes['riwayat_belajar_nilai'];
			foreach ($riwayat_belajar_nilai as $value) {
				$value['riwayat_belajar_detail_id'] = $this->id;
				$riwayat_belajar_nilai_obj = self::getModelByName('riwayat_belajar_nilai');
				$where_condition = [['riwayat_belajar_detail_id', $this->id], ['nilai_id', $value['nilai_id']]];
				$riwayat_belajar_nilai = $riwayat_belajar_nilai_obj->where($where_condition);
				
				if ($riwayat_belajar_nilai->count() == 0) {
					$riwayat_belajar_nilai = $riwayat_belajar_nilai_obj->create($value);
				}else{
					$riwayat_belajar_nilai->update($value);
				}
			}
			unset($attributes['riwayat_belajar_nilai']);
		}
		return parent::update($attributes, $options);
	}

}