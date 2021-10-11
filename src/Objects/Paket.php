<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Paket extends BaseModel
{
	protected $table = 'paket';
	protected $with = ['item'];
	public $like_fields = ['nama'];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public function item()
	{
		return $this->hasMany(Item::class, 'paket_id', 'id');
	}

	public static function create(array $attributes = [])
	{
		$item = false;
		$attributes['kode'] = self::nextCode('paket_sequance');
		if (array_key_exists('item', $attributes)) {
			$item = $attributes['item'];
			unset($attributes['item']);
		}

		$paket = parent::create($attributes);

		if ($item) {
			foreach ($item as $key => $value) {
				$item_obj = self::getModelByName('item');
				$value['paket_id'] = $paket->id;
				$item_data = $item_obj->create($value);

			}
		}

		return $paket;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('item', $attributes)) {
			$item = $attributes['item'];
			foreach ($item as $key => $value) {
				$item_obj = self::getModelByName('item');
				$value['paket_id'] = $this->id;
				if (array_key_exists('id', $value)) {
					$item_data = $item_obj->find($value['id']);
					unset($value['id']);
					$item_data->update($value);
				}else{
					$item_data = $item_obj->create($value);
				}
			}
			unset($attributes['item']);
		}
		return parent::update($attributes, $options);
	}
}