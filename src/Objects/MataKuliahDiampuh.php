<?php

namespace App\Objects;

use App\Objects\BaseModel;

class MataKuliahDiampuh extends BaseModel
{
	protected $table = 'mata_kuliah_diampuh';
	protected $with = ['mata_kuliah', 'konfigurasi_nilai'];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];

	public static function create(array $attributes = [])
	{
		$mata_kuliah_diampuh_obj = self::getModelByName('mata_kuliah_diampuh');
		$mata_kuliah_diampuh = $mata_kuliah_diampuh_obj
			->where('mata_kuliah_id', $attributes['mata_kuliah_id'])
			->whereHas("dosen_pjmk", function($q) use ($attributes) {
				$q->where('status', 'aktif');
			});

		if ($mata_kuliah_diampuh->count() > 0) {
			throw new \Exception("Mata kuliah telah diampuh");
		}

		$data = parent::create($attributes);

		if (array_key_exists('konfigurasi_nilai', $attributes)) {
			$object_konfigurasi_nilai = self::getModelByName('konfigurasi_nilai');

			foreach ($attributes['konfigurasi_nilai'] as $value) {
				$value['mata_kuliah_diampuh_id'] = $data->id;
				$object_konfigurasi_nilai->create($value);
			}
			unset($attributes['konfigurasi_nilai']);
		}

		return $data;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('konfigurasi_nilai', $attributes)) {
			foreach ($attributes['konfigurasi_nilai'] as $value) {
				$object_konfigurasi_nilai = self::getModelByName('konfigurasi_nilai');
				$exist_konfigurasi = $object_konfigurasi_nilai
										->where('mata_kuliah_diampuh_id', $this->id)
										->where('nilai_id', $value['nilai_id']);

				if (empty($exist_konfigurasi->first())) {
					$konfigurasi_nilai = $object_konfigurasi_nilai->create($value);
				}else{
					$konfigurasi_nilai = $exist_konfigurasi->update($value);
				}
			}

			$this->updateNilai();
			unset($attributes['konfigurasi_nilai']);
		}

		return parent::update($attributes, $options);
	}

	public function updateNilai()
	{
        $mahasiswa_obj = self::getModelByName('mahasiswa');
        $mata_kuliah_diampuh = $this;
		$mahasiswa = $mahasiswa_obj->where('status', 'mahasiswa');

        $mahasiswa = $mahasiswa->whereHas(
            'riwayat_belajar',
            function($q) use ($mata_kuliah_diampuh) {
                $q->where('terisi', $mata_kuliah_diampuh->terisi)
                  ->whereHas(
                    'riwayat_belajar_detail', 
                    function($q) use ($mata_kuliah_diampuh) {
                        $q->where('mata_kuliah_id', $mata_kuliah_diampuh->mata_kuliah_id);
                  });
            }
        );
	}

	public function dosen_pjmk()
	{
		return $this->belongsTo(DosenPjmk::class, 'dosen_pjmk_id', 'id');
	}

	public function mata_kuliah()
	{
		return $this->hasOne(MataKuliah::class, 'id', 'mata_kuliah_id');
	}

	public function konfigurasi_nilai()
	{
		return $this->hasMany(KonfigurasiNilai::class, 'mata_kuliah_diampuh_id', 'id');
	}
	
}