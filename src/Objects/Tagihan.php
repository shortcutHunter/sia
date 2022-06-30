<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Tagihan extends BaseModel
{
	protected $table = 'tagihan';
	protected $with = ['orang', 'tagihan_item', 'transaksi'];
	public static $date_fields = ['tanggal'];

	protected $casts = [
	    'tanggal' => 'datetime:d/m/Y',
	];
	
	public $selection_fields = ['status'];
	protected $appends = ['status_label'];

	public static $relation = [
		['name' => 'orang', 'is_selection' => false, 'skip' => true],
		['name' => 'transaksi', 'is_selection' => false, 'skip' => true],
	];

	public $status_enum = [
		// "draft" => "Draft",
		"proses" => "Proses",
		"cicil" => "Cicil",
		"bayar" => "Lunas",
	];

	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	public function orang()
	{
		return $this->belongsTo(Orang::class, 'orang_id', 'id');
	}

	public function tagihan_item()
	{
		return $this->hasMany(TagihanItem::class, 'tagihan_id', 'id');
	}

	public function transaksi()
	{
		return $this->hasMany(Transaksi::class, 'tagihan_id', 'id');
	}

	public static function kodePem()
	{
		$kode = uniqid();
		return $kode;
	}

	public function bayarTagihan($nominal, $tagihan_bukti_bayar, $status = 'process') {
		$transaksi_obj = self::getModelByName('transaksi');

		$transaksi_val = [
        	'tanggal_bayar' => date('d/m/Y'),
        	'nominal' => $nominal,
        	'tagihan_id' => $this->id,
        	'tagihan_bukti_bayar' => $tagihan_bukti_bayar,
        	'status' => $status

        ];
        $transaksi = $transaksi_obj->create($transaksi_val);

        return $transaksi;
	}

	public static function create(array $attributes = [])
	{
		$tagihan_item = false;
		if (array_key_exists('tagihan_item', $attributes)) {
			$tagihan_item = $attributes['tagihan_item'];
			unset($attributes['tagihan_item']);
		}

		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'bayar') {
				$attributes['kode_pembayaran'] = self::kodePem();

				if (array_key_exists('register_ulang', $attributes) && $attributes['register_ulang']) {
					$mahasiswa_obj = self::getModelByName('mahasiswa');
					$mahasiswa = $mahasiswa_obj->where('orang_id', $attributes['orang_id'])->first();
					if (!empty($mahasiswa)) {
						$mahasiswa->update(['ajukan_sks' => true]);
					}
				}
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
					'nominal' => $value['nominal'],
					'biaya_lunas' => $value['biaya_lunas'],

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
					'nominal' => $value['nominal'],
					'biaya_lunas' => $value['biaya_lunas'],
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

		if (array_key_exists('transaksi', $attributes)) {
			$item_ids = [];
			$transaksi_obj = self::getModelByName('transaksi');
			$transaksi = $attributes['transaksi'];
			$transaksi['tagihan_id'] = $this->id;
			$transaksi['status'] = 'process';

			$transaksi = $transaksi_obj->create($transaksi);

			unset($attributes['transaksi']);
		}

		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'bayar' || $attributes['status'] == 'cicil') {
				$attributes['kode_pembayaran'] = self::kodePem();

				if ($this->register_ulang || (array_key_exists('register_ulang', $attributes) && $attributes['register_ulang'])) {
					$mahasiswa_obj = self::getModelByName('mahasiswa');
					$mahasiswa = $mahasiswa_obj->where('orang_id', $this->orang_id)->first();
					if (!empty($mahasiswa)) {
						$mahasiswa->update(['ajukan_sks' => true]);
					}
				}
			}
		}

		return parent::update($attributes, $options);
	}

	public function delete()
	{
		$object_tagihan_item = self::getModelByName('tagihan_item');
		$tagihan_item = $object_tagihan_item->where('tagihan_id', $this->id);
		$tagihan_item->delete();
		
		return parent::delete();
	}
}