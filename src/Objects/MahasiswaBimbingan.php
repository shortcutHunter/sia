<?php

namespace App\Objects;

use App\Objects\BaseModel;

class MahasiswaBimbingan extends BaseModel
{
	protected $table = 'mahasiswa_bimbingan';
	protected $with = ['mahasiswa'];

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
	}

	public function dosen_pa()
	{
		return $this->belongsTo(DosenPa::class, 'dosen_pa_id', 'id');
	}

	public static function create(array $attributes = [])
	{
		$mahasiswa_bimbingan_obj = self::getModelByName('mahasiswa_bimbingan');
		$mahasiswa_bimbingan = $mahasiswa_bimbingan_obj
			->where('mahasiswa_id', $attributes['mahasiswa_id'])
			->whereHas("dosen_pa", function($q) use ($attributes) {
				$q->where('status', 'aktif');
			});

		if ($mahasiswa_bimbingan->count() > 0) {
			throw new \Exception("Mahasiswa telah di bimbing");
		}

		$data = parent::create($attributes);
		return $data;
	}

}