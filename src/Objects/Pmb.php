<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Pmb extends BaseModel
{
	protected $table = 'pmb';
	protected $with = ['orang', 'jurusan', 'bukti_pembayaran'];

	public $selection_fields = ['status'];

	public static $file_fields = ['bukti_pembayaran'];
	public static $date_fields = ['tanggal_pendaftaran'];

	public $status_enum = [
		"baru" => "Baru",
		"ujian" => "Ujian",
		"terima" => "Terima",
		"tolak" => "Tolak",
	];

	public static $relation = [
		['name' => 'orang', 'is_selection' => false, 'skip' => false],
		['name' => 'jurusan', 'is_selection' => true, 'skip' => true],
		['name' => 'penerbitan_nim', 'is_selection' => true, 'skip' => true]
	];

	protected $casts = [
	    'tanggal_pendaftaran' => 'datetime:d/m/Y',
	];

	public function orang()
	{
		return $this->belongsTo(Orang::class, 'orang_id', 'id');
	}

	public function jurusan()
	{
		return $this->hasOne(Jurusan::class, 'id', 'jurusan_id');
	}

	public function bukti_pembayaran()
	{
		return $this->hasOne(File::class, 'id', 'bukti_pembayaran_id');
	}

	public static function create(array $attributes = [])
	{
		$object_sequance = self::getModelByName('sequance');
		$object_sequance_next_number = $object_sequance->getnextCode('nomor_peserta');
		$attributes['nomor_peserta'] = $object_sequance_next_number;
		return parent::create($attributes);
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'terima') {
				$object_penerbitan_nim = self::getModelByName('penerbitan_nim');
				$penerbitan_nim = $object_penerbitan_nim->where('pmb_id', $this->id)->first();

				if (empty($penerbitan_nim)) {
					$object_penerbitan_nim->create(['pmb_id' => $this->id, 'tahun' => date('Y')]);
				}

			}
		}

		return parent::update($attributes, $options);
	}
}