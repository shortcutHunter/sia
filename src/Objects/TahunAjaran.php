<?php

namespace App\Objects;

use App\Objects\BaseModel;

class TahunAjaran extends BaseModel
{
	protected $table = 'tahun_ajaran';

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public function getnextCode()
	{
		$konfigurasi = self::getModelByName('konfigurasi')->first();
		$tahun_ajaran = $this->where('status', 'aktif')->first();

		if (!empty($tahun_ajaran)) {
			$tahun_ajaran->update(['nomor' => $tahun_ajaran->nomor+1]);
		}else{
			$nama = date("Y")."/".((int)date('Y') + 1);
			$tahun_ajaran = $this->create(['nama' => $nama, 'tahun' => date("Y")]);
		}

		$tahun_ajaran = $tahun_ajaran->refresh();

		return $tahun_ajaran->tahun.$konfigurasi->kode_perusahaan.sprintf('%03d', $tahun_ajaran->nomor);
	}

}