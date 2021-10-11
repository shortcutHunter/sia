<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Item extends BaseModel
{
	protected $table = 'item';
	public $like_fields = ['nama'];

	public function paket()
	{
		return $this->belongsTo(Paket::class, 'paket_id', 'id');
	}

	public static function create(array $attributes = [])
	{
		$attributes['kode'] = self::nextCode('item_sequance');
		$item = parent::create($attributes);
		return $item;
	}
}