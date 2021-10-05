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

}