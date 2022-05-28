<?php

namespace App\Objects;

use App\Objects\BaseModel;

class PembiayaanTahunAjar extends BaseModel
{
	protected $table = 'pembiayaan_tahun_ajar';
	protected $with = ['semester'];

	public function semester()
	{
		return $this->belongsTo(Semester::class, 'semester_id', 'id');
	}

	public function createTagihan($orang_id)
	{
		$tagihan_obj = self::getModelByName('tagihan');

		$tagihan_item_value = [[
            "nama" => $this->nama,
            "nominal" => $this->total_biaya,
            'biaya_lunas' => $this->biaya_lunas
        ]];
        $tagihan_value = [
            'tanggal' => date('d/m/Y'),
            'nominal' => $this->total_biaya,
            'biaya_lunas' => $this->biaya_lunas,
            'sisa_hutang' => $this->total_biaya,
            'orang_id' => $orang_id,
            'system' => 1,
            'register_ulang' => $this->semester_id != null ? 1 : 0,
            'status' => 'proses',
            'tagihan_item' => $tagihan_item_value
        ];
        $tagihan = $tagihan_obj->create($tagihan_value);

        return $tagihan;
	}
}