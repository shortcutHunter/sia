<?php

namespace App\Objects;

use App\Objects\BaseModel;

class TagihanBuktiBayar extends BaseModel
{
	protected $table = 'tagihan_bukti_bayar';
	protected $with = ['file'];

	public function file()
	{
		return $this->hasOne(File::class, 'id', 'file_id');
	}

	public static function create(array $attributes = [])
	{
		if (array_key_exists('file', $attributes)) {
			$file = $attributes['file'];
			$file_obj = self::getModelByName('file');

			unset($attributes['file']);

			$file_data_value = [
				'name' => 'Bukti Pembayaran',
				'filename' => $file['filename'],
				'filetype' => $file['filetype'],
				'base64' => $file['base64']
			];
			$file = $file_obj->create($file_data_value);

			$tagihan_value = [
				'tagihan_id' => $attributes['tagihan_id'],
				'file_id' => $file->id
			];
			$tagihan = parent::create($tagihan_value);
		}else{
			$tagihan = parent::create($attributes);
		}

		return $tagihan;
	}

}