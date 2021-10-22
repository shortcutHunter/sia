<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Tagihan extends BaseModel
{
	protected $table = 'tagihan';
	protected $with = ['orang', 'tagihan_item', 'tagihan_bukti_bayar'];
	public static $date_fields = ['tanggal'];

	protected $casts = [
	    'tanggal' => 'datetime:d/m/Y',
	];
	
	public $selection_fields = ['status'];

	public static $relation = [
		['name' => 'orang', 'is_selection' => false, 'skip' => true],
		['name' => 'tagihan_bukti_bayar', 'is_selection' => false, 'skip' => true],
	];

	public $status_enum = [
		"draft" => "Draft",
		"proses" => "Proses",
		"bayar" => "Lunas",
	];

	public function orang()
	{
		return $this->belongsTo(Orang::class, 'orang_id', 'id');
	}

	public function tagihan_item()
	{
		return $this->hasMany(TagihanItem::class, 'tagihan_id', 'id');
	}

	public function tagihan_bukti_bayar()
	{
		return $this->hasMany(TagihanBuktiBayar::class, 'tagihan_id', 'id');
	}

	public static function kodePem()
	{
		$kode = uniqid();
		return $kode;
	}

	public static function create(array $attributes = [])
	{
		$tagihan_item = false;
		if (array_key_exists('tagihan_item', $attributes)) {
			$tagihan_item = $attributes['tagihan_item'];
			unset($attributes['tagihan_item']);
		}

		$tagihan_bukti_bayar = false;
		if (array_key_exists('tagihan_bukti_bayar', $attributes)) {
			$tagihan_bukti_bayar = $attributes['tagihan_bukti_bayar'];
			unset($attributes['tagihan_bukti_bayar']);
		}

		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'bayar') {
				$attributes['kode_pembayaran'] = self::kodePem();
			}
		}

		$attributes['kode'] = self::nextCode('tagihan_sequance');
		$tagihan = parent::create($attributes);

		if ($tagihan_item) {
			foreach ($tagihan_item as $key => $value) {
				$tagihan_item_obj = self::getModelByName('tagihan_item');
				$tagihan_item_value = [
					'tagihan_id' => $tagihan->id,
					'nama' => $value['nama'],
					'kode' => $value['kode'],
					'nominal' => $value['nominal'],

				];
				$tagihan_item_data = $tagihan_item_obj->create($tagihan_item_value);

			}
		}

		if ($tagihan_bukti_bayar) {
			foreach ($tagihan_bukti_bayar as $key => $value) {
				$tagihan_bukti_bayar_obj = self::getModelByName('tagihan_bukti_bayar');
				$tagihan_bukti_bayar_value = [
					'tagihan_id' => $tagihan->id
				];
				$is_empty = false;

				if (array_key_exists('file', $value)) {
					$tagihan_bukti_bayar_value['file'] = $value['file'];
				}
				else if (array_key_exists('file_id', $value)) {
					$tagihan_bukti_bayar_value['file_id'] = $value['file_id'];
				}else {
					$is_empty = true;
				}
				if (!$is_empty) {
					$tagihan_bukti_bayar_data = $tagihan_bukti_bayar_obj->create($tagihan_bukti_bayar_value);
				}
			}
		}

		return $tagihan;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('tagihan_item', $attributes)) {
			$item_ids = [];
			$tagihan_item = $attributes['tagihan_item'];
			foreach ($tagihan_item as $key => $value) {
				$tagihan_item_obj = self::getModelByName('tagihan_item');
				$tagihan_item_value = [
					'tagihan_id' => $this->id,
					'nama' => $value['nama'],
					'kode' => $value['kode'],
					'nominal' => $value['nominal'],

				];
				if (array_key_exists('id', $value)) {
					$tagihan_item_data = $tagihan_item_obj->find($value['id']);
					$tagihan_item_data->update($tagihan_item_value);
				}else{
					$tagihan_item_data = $tagihan_item_obj->create($tagihan_item_value);
				}
				array_push($item_ids, $tagihan_item_data->id);
			}
			unset($attributes['tagihan_item']);

			foreach ($this->tagihan_item as $value) {
				if (!in_array($value->id, $item_ids)) {
					$value->delete();
				}
			}
		}

		if (array_key_exists('tagihan_bukti_bayar', $attributes)) {
			$bukti_bayar_ids = [];
			$tagihan_bukti_bayar = $attributes['tagihan_bukti_bayar'];

			foreach ($tagihan_bukti_bayar as $key => $value) {
				$tagihan_bukti_bayar_obj = self::getModelByName('tagihan_bukti_bayar');
				$tagihan_bukti_bayar_value = [
					'tagihan_id' => $this->id
				];
				$is_empty = false;

				if (array_key_exists('file', $value)) {
					$tagihan_bukti_bayar_value['file'] = $value['file'];
				}
				else if (array_key_exists('file_id', $value)) {
					$tagihan_bukti_bayar_value['file_id'] = $value['file_id'];
				}else {
					$is_empty = true;
				}
				if (!$is_empty) {
					if (array_key_exists('id', $value)) {
						$tagihan_bukti_bayar_data = $tagihan_bukti_bayar_obj->find($value['id']);
						$tagihan_bukti_bayar_data->update($tagihan_bukti_bayar_value);
					}else{
						$tagihan_bukti_bayar_data = $tagihan_bukti_bayar_obj->create($tagihan_bukti_bayar_value);
					}
					array_push($bukti_bayar_ids, $tagihan_bukti_bayar_data->id);
				}
			}
			unset($attributes['tagihan_bukti_bayar']);

			foreach ($this->tagihan_bukti_bayar as $value) {
				if (!in_array($value->id, $item_ids)) {
					$value->delete();
				}
			}
		}

		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'bayar') {
				$attributes['kode_pembayaran'] = self::kodePem();
			}
		}

		return parent::update($attributes, $options);
	}
}