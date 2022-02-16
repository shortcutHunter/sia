<?php

namespace App\Objects;

use App\Objects\BaseModel;

class PengajuanKs extends BaseModel
{
	protected $table = 'pengajuan_ks';
	protected $with = ['semester', 'tahun_ajaran', 'pengajuan_ks_detail'];

	public $status_enum = [
		"proses" => "Proses",
		"terima" => "Terima",
		"tolak" => "Tolak",
	];

	protected $appends = ['status_label'];

	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	public static function create(array $attributes = [])
	{
		$mahasiswa = self::getModelByName('mahasiswa')->find($attributes['mahasiswa_id']);
		if (!array_key_exists('tahun_ajaran_id', $attributes)) {
			$attributes['tahun_ajaran_id'] = $mahasiswa->tahun_ajaran_id;
		}
		if (!array_key_exists('semester_id', $attributes)) {
			$attributes['semester_id'] = $mahasiswa->semester_id;
		}

		if (array_key_exists('pengajuan_ks_detail', $attributes)) {
			$pengajuan_ks_detail_raw_data = $attributes['pengajuan_ks_detail'];
			unset($attributes['pengajuan_ks_detail']);
		}else{
			$pengajuan_ks_detail_raw_data = false;
		}

		$model = parent::create($attributes);
		$mahasiswa->update(['pengajuan' => true, 'ajukan_sks' => false]);

		// one2many relation
		if ($pengajuan_ks_detail_raw_data) {
			foreach ($pengajuan_ks_detail_raw_data as $value) {
				$pengajuan_ks_detail_value = [
					'mata_kuliah_id' => $value['mata_kuliah_id'],
					'pengajuan_ks_id' => $model->id
				];
				$pengajuan_ks_detail = self::getModelByName('pengajuan_ks_detail')->create($pengajuan_ks_detail_value);
			}
		}
		

		return $model;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('status', $attributes)) {
			if ($attributes['status'] == 'terima') {
				$this->mahasiswa->update(['sudah_pengajuan' => true, 'pengajuan' => false, 'ajukan_sks' => false]);
			}

			if ($attributes['status'] == 'tolak') {
				$this->mahasiswa->update(['sudah_pengajuan' => false, 'pengajuan' => false, 'ajukan_sks' => true]);
			}
		}

		return parent::update($attributes, $options);
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

	public function tahun_ajaran()
	{
		return $this->hasOne(TahunAjaran::class, 'id', 'tahun_ajaran_id');
	}

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
	}


	public function pengajuan_ks_detail()
	{
		return $this->hasMany(PengajuanKsDetail::class, 'pengajuan_ks_id', 'id');
	}
}