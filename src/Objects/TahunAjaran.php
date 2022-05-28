<?php

namespace App\Objects;

use App\Objects\BaseModel;

class TahunAjaran extends BaseModel
{
	protected $table = 'tahun_ajaran';
	protected $with = ['paket', 'pembiayaan_tahun_ajar'];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];
	
	public $like_fields = ['nama'];

	public static $relation = [
		['name' => 'paket', 'is_selection' => false, 'skip' => true]
	];
	
	public function paket()
	{
		return $this->hasOne(Paket::class, 'id', 'paket_id');
	}

	public function pembiayaan_tahun_ajar()
	{
		return $this->hasMany(PembiayaanTahunAjar::class, 'tahun_ajaran_id', 'id');
	}

	public function getnextCode()
	{
		$konfigurasi = self::getModelByName('konfigurasi')->first();

		$ta_digit = substr($this->tahun, (strlen($this->tahun) - 2));
		$nimSeq = $konfigurasi->getNextNIM();

		return $ta_digit.$konfigurasi->kode_perusahaan.sprintf('%03d', $nimSeq);
	}

	public static function create(array $attributes = [])
	{
		$pta_arr = [];

		if (array_key_exists('pembiayaan_tahun_ajar', $attributes)) {
			$pta_arr = $attributes['pembiayaan_tahun_ajar'];
			unset($attributes['pembiayaan_tahun_ajar']);
		}

		$model = parent::create($attributes);

		foreach ($pta_arr as $key => $value) {
			$object_pta = self::getModelByName('pembiayaan_tahun_ajar');
			$pta_value = [
				"nama" => $value['nama'],
				"biaya_lunas" => $value['biaya_lunas'],
				"total_biaya" => $value['total_biaya'],
				"semester_id" => $value['semester_id'],
				"lainnya" => $value['lainnya'],
				"registrasi" => $value['registrasi'],
				"tahun_ajaran_id" => $model->id
			];
			$pta = $object_pta->create($pta_value);
		}

		return $model;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('pembiayaan_tahun_ajar', $attributes)) {
			foreach ($attributes['pembiayaan_tahun_ajar'] as $key => $value) {
				$object_pta = self::getModelByName('pembiayaan_tahun_ajar');
				$pta_value = [
					"nama" => $value['nama'],
					"biaya_lunas" => $value['biaya_lunas'],
					"total_biaya" => $value['total_biaya'],
					"semester_id" => $value['semester_id'],
					"lainnya" => $value['lainnya'],
					"registrasi" => $value['registrasi'],
					"tahun_ajaran_id" => $this->id
				];
				
				if (array_key_exists('id', $value)) {
					$pta = $object_pta->find($value['id']);

					if (!empty($pta)) {
						$pta->update($pta_value);
					} else {
						$pta = $object_pta->create($pta_value);
					}
				}else {
					$pta = $object_pta->create($pta_value);
				}
			}

			unset($attributes['pembiayaan_tahun_ajar']);
		}

		$tahun_ajaran = parent::update($attributes);
		return $tahun_ajaran;
	}

}