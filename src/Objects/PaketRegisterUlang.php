<?php

namespace App\Objects;

use App\Objects\BaseModel;

class PaketRegisterUlang extends BaseModel
{
	protected $table = 'paket_register_ulang';
	protected $with = ['tahun_ajaran', 'paket'];

	public function paket()
	{
		return $this->hasOne(Paket::class, 'paket_id', 'id');
	}

	public function tahun_ajaran()
	{
		return $this->hasOne(TahunAjaran::class, 'tahun_ajaran_id', 'id');
	}

}