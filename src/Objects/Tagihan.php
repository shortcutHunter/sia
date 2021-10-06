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
		"bayar" => "Bayar",
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

	public static function create(array $attributes = [])
	{
		$tagihan_item = false;
		if (array_key_exists('tagihan_item', $attributes)) {
			$tagihan_item = $attributes['tagihan_item'];
			unset($attributes['tagihan_item']);
		}
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

		

		return parent::update($attributes, $options);
	}
}