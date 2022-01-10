<?php

namespace App\Objects;

use App\Objects\BaseModel;

class PenerbitanNim extends BaseModel
{
	protected $table = 'penerbitan_nim';
	protected $with = ['pmb'];

	public $status_enum = [
		"terbit" => "Terbit",
		"pengajuan" => "Pengajuan",
		"belum" => "Belum",
	];

	protected $appends = ['status_label'];
	
	public $like_fields = ['nama'];

	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	public function pmb()
	{
		return $this->hasOne(Pmb::class, 'id', 'pmb_id');
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'terbit') {
				$object_mahasiswa = self::getModelByName('mahasiswa');

				if (!$this->mahasiswa_id) {
					$konfigurasi_obj = self::getModelByName('konfigurasi');
					$konfigurasi = $konfigurasi_obj->first();


					$object_mahasiswa->create([
						'orang_id' => $this->pmb->orang_id, 
						'tahun_masuk' => date('Y'),
						'semester_id' => $konfigurasi->semester_id,
						'tahun_ajaran_id' => $konfigurasi->tahun_ajaran_id
					]);
				}
			}
		}

		return parent::update($attributes, $options);
	}
}