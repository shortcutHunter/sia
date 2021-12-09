<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Pmb extends BaseModel
{
	protected $table = 'pmb';
	protected $with  = ['orang', 'jurusan', 'bukti_pembayaran'];

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
			$status = $attributes['status'];
			if ($status == 'ujian') {
				$tagihan_obj = self::getModelByName('tagihan');
				$konfigurasi_obj = self::getModelByName('konfigurasi');
				$setup_paket_obj = self::getModelByName('paket_register_ulang');

				$konfigurasi = $konfigurasi_obj->first();
				$setup_paket = $setup_paket_obj->where('semester_id', $konfigurasi->semester_id)->first();

				$tagihan_item_value = $setup_paket->paket_register_ulang_item->map(function($data) {
		            return $data->item->only(['nama', 'kode', 'nominal']);
		        })->toArray();

				$tagihan_bukti_bayar_value = [
					[
						'file_id' => $this->bukti_pembayaran_id
					]
				];

				$tagihan_value = [
					'tanggal' => date('d/m/Y'),
					'nominal' => $setup_paket->nominal,
					'orang_id' => $this->orang_id,
					'system' => true,
					'paket_register_ulang_id' => $setup_paket->id,
					'tagihan_item' => $tagihan_item_value,
					'tagihan_bukti_bayar' => $tagihan_bukti_bayar_value,
					'status' => 'bayar'
				];
				$tagihan = $tagihan_obj->create($tagihan_value);
			}
			if ($status == 'terima') {
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