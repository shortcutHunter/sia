<?php

namespace App\Objects;

use App\Objects\BaseModel;

class PaketRegisterUlang extends BaseModel
{
	protected $table = 'paket_register_ulang';
	protected $with = ['semester', 'paket_register_ulang_item'];

	public static $relation = [
		['name' => 'semester', 'is_selection' => true, 'skip' => false]
	];

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id'); 
	}

	public function paket_register_ulang_item()
	{
		return $this->hasMany(PaketRegisterUlangItem::class, 'paket_register_ulang_id', 'id'); 
	}

	public static function create(array $attributes = [])
	{
		$paket_register_ulang_item = false;
		if (array_key_exists('paket_register_ulang_item', $attributes)) {
			$paket_register_ulang_item = $attributes['paket_register_ulang_item'];
			unset($attributes['paket_register_ulang_item']);
		}
		$paket_register_ulang = parent::create($attributes);

		if ($paket_register_ulang_item) {
			foreach ($paket_register_ulang_item as $key => $value) {
				$paket_register_ulang_item_obj = self::getModelByName('paket_register_ulang_item');
				$paket_register_ulang_item_value = [
					'paket_register_ulang_id' => $paket_register_ulang->id,
					'item_id' => $value['item_id'],

				];
				$paket_register_ulang_item_data = $paket_register_ulang_item_obj->create($paket_register_ulang_item_value);

			}
		}

		return $paket_register_ulang;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('paket_register_ulang_item', $attributes)) {
			$item_ids = [];
			$paket_register_ulang_item = $attributes['paket_register_ulang_item'];
			foreach ($paket_register_ulang_item as $key => $value) {
				$paket_register_ulang_item_obj = self::getModelByName('paket_register_ulang_item');
				$paket_register_ulang_item_value = [
					'paket_register_ulang_id' => $this->id,
					'item_id' => $value['item_id'],

				];
				if (array_key_exists('id', $value)) {
					$paket_register_ulang_item_data = $paket_register_ulang_item_obj->find($value['id']);
					$paket_register_ulang_item_data->update($paket_register_ulang_item_value);
				}else{
					$paket_register_ulang_item_data = $paket_register_ulang_item_obj->create($paket_register_ulang_item_value);
				}
				array_push($item_ids, $paket_register_ulang_item_data->id);
			}
			unset($attributes['paket_register_ulang_item']);

			foreach ($this->paket_register_ulang_item as $value) {
				if (!in_array($value->id, $item_ids)) {
					$value->delete();
				}
			}

		}		

		return parent::update($attributes, $options);
	}

}