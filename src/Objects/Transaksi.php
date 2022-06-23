<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Transaksi extends BaseModel
{
	protected $table = 'transaksi';
	protected $with = ['tagihan_bukti_bayar'];

	public static $date_fields = ['tanggal_bayar'];

	public $status_enum = [
		"process" => "Process",
		"verified" => "Verified",
		"tolak" => "Tolak",
	];
	
	public $selection_fields = ['status'];
	protected $appends = ['status_label'];

	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	public function tagihan()
	{
		return $this->belongsTo(Tagihan::class, 'tagihan_id', 'id');
	}

	public function tagihan_bukti_bayar()
	{
		return $this->hasMany(TagihanBuktiBayar::class, 'transaksi_id', 'id');
	}

	public static function create(array $attributes = [])
	{
		$tagihan_bukti_bayar = [];
		if (array_key_exists('tagihan_bukti_bayar', $attributes)) {
			$tagihan_bukti_bayar = $attributes['tagihan_bukti_bayar'];
			unset($attributes['tagihan_bukti_bayar']);
		}

		if (!array_key_exists('tanggal_bayar', $attributes)) {
			$attributes['tanggal_bayar'] = date('d/m/Y');
		}

		if (array_key_exists('nominal', $attributes) && array_key_exists('tagihan_id', $attributes)) {
			$object_tagihan = self::getModelByName('tagihan');
			$tagihan = $object_tagihan->find($attributes['tagihan_id']);
			if (!empty($tagihan)) {

				if ($tagihan->sisa_hutang == 0) {
					throw new \Exception("Tidak dapat membayar tagihan ini, tagihan ini tidak memiliki sisa utang");
				}

				if ($attributes['nominal'] == 0) {
					throw new \Exception("Nominal yang dibayarkan tidak dapat kosong");
				}

				if ($tagihan->sisa_hutang >= $attributes['nominal']) {
					$transaksi = parent::create($attributes);
				} else {
					throw new \Exception("Nominal yang dibayarkan lebih besar dari sisa utang tagihan");
				}
			} else {
				throw new \Exception("Tagihan tidak ditemukan");
			}
		} else {
			throw new \Exception("Field belum lengkap");
		}

		foreach ($tagihan_bukti_bayar as $key => $value) {
			$tagihan_bukti_bayar_obj = self::getModelByName('tagihan_bukti_bayar');
			$tagihan_bukti_bayar_value = [
				'transaksi_id' => $transaksi->id
			];
			$is_empty = false;

			if (array_key_exists('file', $value)) {
				$tagihan_bukti_bayar_value['file'] = $value['file'];
			}
			else if (array_key_exists('file_id', $value)) {
				$tagihan_bukti_bayar_value['file_id'] = $value['file_id'];
			}else {
				$is_empty = true;
			}
			if (!$is_empty) {
				$tagihan_bukti_bayar_data = $tagihan_bukti_bayar_obj->create($tagihan_bukti_bayar_value);
			}
		}

		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'verified') {
				$sisa_hutang = $transaksi->tagihan->sisa_hutang - $attributes['nominal'];
				$transaksi->tagihan->update([
					'sisa_hutang' => $sisa_hutang,
					'status' => $sisa_hutang == 0 ? 'bayar' : 'cicil'
				]);
			}
		}

		return $transaksi;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'verified') {
				$sisa_hutang = $this->tagihan->sisa_hutang - $this->nominal;
				$this->tagihan->update([
					'sisa_hutang' => $sisa_hutang,
					'status' => $sisa_hutang == 0 ? 'bayar' : 'cicil'
				]);
			}
		}

		return parent::update($attributes, $options);
	}
}