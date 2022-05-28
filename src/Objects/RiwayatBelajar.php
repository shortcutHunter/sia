<?php

namespace App\Objects;

use App\Objects\BaseModel;

class RiwayatBelajar extends BaseModel
{
	protected $table = 'riwayat_belajar';
	protected $with = ['semester', 'riwayat_belajar_detail'];

	public $status_enum = [
		"aktif" => "Aktif",
		"nonaktif" => "Nonaktif",
	];



	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

	public function riwayat_belajar_detail()
	{
		return $this->hasMany(RiwayatBelajarDetail::class, 'riwayat_belajar_id', 'id');
	}

	public static function create(array $attributes = [])
	{
		$rdb_value = false;
		$rdbs_value = false;
		if (array_key_exists('riwayat_belajar_detail', $attributes)) {
			$rdb_value = $attributes['riwayat_belajar_detail'];
			unset($attributes['riwayat_belajar_detail']);
		}
		if (array_key_exists('riwayat_belajar_details', $attributes)) {
			$rdbs_value = $attributes['riwayat_belajar_details'];
			unset($attributes['riwayat_belajar_details']);
		}
		$riwayat_belajar = parent::create($attributes);

		if ($rdb_value) {
			$rdb_value['riwayat_belajar_id'] = $riwayat_belajar->id;
			$riwayat_belajar_detail_obj = self::getModelByName('riwayat_belajar_detail');
			$riwayat_belajar_detail = $riwayat_belajar_detail_obj->create($rdb_value);
		}
		if ($rdbs_value) {
			foreach ($rdbs_value as $key => $value) {
				$value['riwayat_belajar_id'] = $riwayat_belajar->id;
				$riwayat_belajar_detail_obj = self::getModelByName('riwayat_belajar_detail');
				$riwayat_belajar_detail = $riwayat_belajar_detail_obj->create($value);
			}
		}

		return $riwayat_belajar;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('riwayat_belajar_detail', $attributes)) {
			$rdb_value = $attributes['riwayat_belajar_detail'];
			$rdb_value['riwayat_belajar_id'] = $this->id;
			$riwayat_belajar_detail_obj = self::getModelByName('riwayat_belajar_detail');
			$where_condition = [['riwayat_belajar_id', $this->id], ['mata_kuliah_id', $rdb_value['mata_kuliah_id']]];
			$riwayat_belajar_detail = $riwayat_belajar_detail_obj->where($where_condition);

			if ($riwayat_belajar_detail->count() == 0) {
				$riwayat_belajar_detail = $riwayat_belajar_detail_obj->create($rdb_value);
			}else{
				$riwayat_belajar_detail->first()->update($rdb_value);
			}
			unset($attributes['riwayat_belajar_detail']);
		}
		return parent::update($attributes, $options);
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

    public function getNilai($riwayat_belajar_detail, $semester_id, $mata_kuliah_id)
    {
        $konfigurasi_nilai_obj = self::getModelByName('konfigurasi_nilai');
        $nilai_akhir = 0;

        foreach ($riwayat_belajar_detail->riwayat_belajar_nilai as $nilai_mahasiswa) {
            $nilai_id = $nilai_mahasiswa->nilai_id;
            $konfigurasi_nilai = $konfigurasi_nilai_obj
                ->where([['nilai_id', $nilai_id], ['status', 'aktif']])
                ->whereHas('mata_kuliah_diampuh', function($a) use ($mata_kuliah_id, $semester_id) {
                    $a->where('mata_kuliah_id', $mata_kuliah_id)
                    ->whereHas('dosen_pjmk', function($b) use ($semester_id) {
                        $b->where('status', 'aktif')
                        	->whereHas('semester', function($c) use ($semester_id) {
                        		$c->where('semester_id', $semester_id);
                        	});
                    });
            	});

        	$konfigurasi_nilai = $konfigurasi_nilai->first();
            $nilai_persentase = $nilai_mahasiswa->nilai * $konfigurasi_nilai->persentase / 100;
            $nilai_akhir += $nilai_persentase;
        }
        return $nilai_akhir;
    }

    public function getNilaiMutu($nilai_bobot)
    {
        $nilai_mutu = 0;

        if ($nilai_bobot == "A") {
            $nilai_mutu = 4;
        }
        elseif ($nilai_bobot == "B") {
            $nilai_mutu = 3;
        }
        elseif ($nilai_bobot == "C") {
            $nilai_mutu = 2;
        }
        elseif ($nilai_bobot == "D") {
            $nilai_mutu = 1;
        }else{
            $nilai_mutu = 0;
        }

        return $nilai_mutu;
    }

	public function nilaiMahasiswa($semester_id)
    {
        $khs_obj = self::getModelByName('khs');
        $khs_detail_obj = self::getModelByName('khs_detail');
        $total_sks = 0;
        $ips = 0;

        $khs = $khs_obj->where([['mahasiswa_id', $this->mahasiswa_id], ['semester_id', $this->semester_id]])->first();
        $khs_value = [
            'mahasiswa_id' => $this->mahasiswa_id,
            'semester_id' => $this->semester_id
        ];

        if (empty($khs)) {
            $khs = $khs_obj->create($khs_value);
        }

        foreach ($this->riwayat_belajar_detail as $riwayat_belajar_detail) {
            $mata_kuliah_id = $riwayat_belajar_detail->mata_kuliah_id;

            $nilai_absolut = $this->getNilai($riwayat_belajar_detail, $this->semester_id, $mata_kuliah_id);
            $nilai_bobot = $this->getBobot($nilai_absolut);
            $nilai_mutu = $this->getNilaiMutu($nilai_bobot);

            // update nilai riwayat belajar
            $riwayat_belajar_detail->update([
                "nilai_absolut" => $nilai_absolut,
                "nilai_bobot"   => $nilai_bobot,
                "nilai_mutu"    => $nilai_mutu
            ]);

            // update / create KHS
            $khs_detail_value = [
                "khs_id"         => $khs->id,
                'mata_kuliah_id' => $mata_kuliah_id,
            ];
            $khs_detail = $khs_detail_obj->where($khs_detail_value)->first();

            $khs_detail_value['nilai_absolut']             = $nilai_absolut;
            $khs_detail_value['nilai_bobot']               = $nilai_bobot;
            $khs_detail_value['nilai_mutu']                = $nilai_mutu;
            $khs_detail_value['riwayat_belajar_detail_id'] = $riwayat_belajar_detail->id;

            if (empty($khs_detail)) {
                $khs_detail = $khs_detail_obj->create($khs_detail_value);
            }else{
                $khs_detail->update($khs_detail_value);
            }

            $sks = $riwayat_belajar_detail->mata_kuliah->sks;
            $total_sks += $sks;
            $ips += ($sks * $nilai_mutu);
        }

        $ips = $ips / $total_sks;
        $this->update(['total_sks' => $total_sks, 'status' => 'nonaktif']);
        $khs->update(['total_sks' => $total_sks, 'ips' => $ips]);
    }
}