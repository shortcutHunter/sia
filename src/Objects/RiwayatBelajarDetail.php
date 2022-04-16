<?php

namespace App\Objects;

use App\Objects\BaseModel;

class RiwayatBelajarDetail extends BaseModel
{
	protected $table = 'riwayat_belajar_detail';
	protected $with = ['mata_kuliah', 'riwayat_belajar_nilai'];

	public function riwayat_belajar()
	{
		return $this->belongsTo(RiwayatBelajar::class, 'riwayat_belajar_id', 'id');
	}

	public function mata_kuliah()
	{
		return $this->hasOne(MataKuliah::class, 'id', 'mata_kuliah_id');
	}

	public function riwayat_belajar_nilai()
	{
		return $this->hasMany(RiwayatBelajarNilai::class, 'riwayat_belajar_detail_id', 'id');
	}

	public function getBobot($nilai)
    {
        $mutu = "E";
        if ($nilai >= 79) {
            $mutu = "A";
        }
        elseif ($nilai >= 68) {
            $mutu = "B";
        }
        elseif ($nilai >= 60) {
            $mutu = "C";
        }
        elseif ($nilai >= 41) {
            $mutu = "D";
        }else{
            $mutu = "E";
        }
        return $mutu;
    }

	public function updateNilai($riwayat_belajar_detail, $isUpdate=false)
	{
		$konfigurasi_nilai_obj = $this->getModelByName('konfigurasi_nilai');
        $nilai_absolut = 0;
        $mata_kuliah_id = $riwayat_belajar_detail->mata_kuliah_id;
        $semester_id = $riwayat_belajar_detail->riwayat_belajar->semester_id;

        foreach ($riwayat_belajar_detail->riwayat_belajar_nilai as $nilai_mahasiswa) {
            $nilai_id = $nilai_mahasiswa->nilai_id;
            $konfigurasi_nilai = $konfigurasi_nilai_obj
                ->where([['nilai_id', $nilai_id], ['status', 'aktif']])
                ->whereHas('mata_kuliah_diampuh', function($a) use ($mata_kuliah_id, $semester_id) {
                    $a->where('mata_kuliah_id', $mata_kuliah_id)
                    ->whereHas('dosen_pjmk', function($b) use ($semester_id) {
                        $b->where([['semester_id', $semester_id], ['status', 'aktif']]);
                    });
                })
                ->first();
            $nilai_persentase = $nilai_mahasiswa->nilai * $konfigurasi_nilai->persentase / 100;
            $nilai_absolut += $nilai_persentase;
        }

        $nilai_bobot = $this->getBobot($nilai_absolut);
        if ($isUpdate) {
        	return $nilai_bobot;
        }

        $riwayat_belajar_detail->update(['nilai_bobot' => $nilai_bobot]);
	}

	public static function create(array $attributes = [])
	{
		$riwayat_belajar_nilai = false;
		if (array_key_exists('riwayat_belajar_nilai', $attributes)) {
			$riwayat_belajar_nilai = $attributes['riwayat_belajar_nilai'];
			unset($attributes['riwayat_belajar_nilai']);
		}
		$riwayat_belajar_detail = parent::create($attributes);

		if ($riwayat_belajar_nilai) {
			foreach ($riwayat_belajar_nilai as $value) {
				$value['riwayat_belajar_detail_id'] = $riwayat_belajar_detail->id;
				$riwayat_belajar_nilai_obj = self::getModelByName('riwayat_belajar_nilai');
				$riwayat_belajar_nilai = $riwayat_belajar_nilai_obj->create($value);
			}
		}

		$this->updateNilai($riwayat_belajar_detail);

		return $riwayat_belajar_detail;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('riwayat_belajar_nilai', $attributes)) {
			$riwayat_belajar_nilai = $attributes['riwayat_belajar_nilai'];
			foreach ($riwayat_belajar_nilai as $value) {
				$value['riwayat_belajar_detail_id'] = $this->id;
				$riwayat_belajar_nilai_obj = self::getModelByName('riwayat_belajar_nilai');
				$where_condition = [['riwayat_belajar_detail_id', $this->id], ['nilai_id', $value['nilai_id']]];
				$riwayat_belajar_nilai = $riwayat_belajar_nilai_obj->where($where_condition);
				
				if ($riwayat_belajar_nilai->count() == 0) {
					$riwayat_belajar_nilai = $riwayat_belajar_nilai_obj->create($value);
				}else{
					$riwayat_belajar_nilai->update($value);
				}
			}
			unset($attributes['riwayat_belajar_nilai']);
		}

		if (!array_key_exists('nilai_bobot', $attributes)) {
			$attributes['nilai_bobot'] = $this->updateNilai($this, true);
		}

		return parent::update($attributes, $options);
	}

}