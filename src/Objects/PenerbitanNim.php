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

	public $selection_fields = ['status'];
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
				$tagihan_obj = self::getModelByName('tagihan');

				$tagihan = $tagihan_obj->where('orang_id', $this->pmb->orang_id)->first();

				if (!$this->mahasiswa_id) {
					$konfigurasi_obj = self::getModelByName('konfigurasi');
					$konfigurasi = $konfigurasi_obj->first();


					$mahasiswa = $object_mahasiswa->create([
						'orang_id' => $this->pmb->orang_id, 
						'tahun_masuk' => date('Y'),
						'semester_id' => $konfigurasi->semester_id,
						'tahun_ajaran_id' => $konfigurasi->tahun_ajaran_id,
						'tagihan_id' => $tagihan->id
					]);

					$attributes['mahasiswa_id'] = $mahasiswa->id;
				}
			}
		}

		return parent::update($attributes, $options);
	}
}