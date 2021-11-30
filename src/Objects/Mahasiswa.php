<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Mahasiswa extends BaseModel
{
	protected $table = 'mahasiswa';
	protected $with = [
		'orang', 
		'jurusan', 
		'semester',
		'tahun_ajaran',
		'pengajuan_ks',
		'riwayat_belajar',
		'mahasiswa_bimbingan',
		'register_ulang',
		'khs'
	];

	public $selection_fields = ['status'];
	public static $relation = [
		['name' => 'orang', 'is_selection' => false, 'skip' => false],
		['name' => 'jurusan', 'is_selection' => true, 'skip' => true],
		['name' => 'semester', 'is_selection' => true, 'skip' => true],
		['name' => 'tahun_ajaran', 'is_selection' => true, 'skip' => true],
		['name' => 'pengajuan_ks', 'is_selection' => false, 'skip' => true],
		['name' => 'riwayat_belajar', 'is_selection' => false, 'skip' => true],
		['name' => 'register_ulang', 'is_selection' => false, 'skip' => true]
	];

	public $status_enum = [
		"mahasiswa" => "Mahasiswa",
		"alumni"    => "Alumni",
		"dropout"   => "Dropout",
	];

	public static function create(array $attributes = [])
	{
		if (array_key_exists('nim', $attributes)) {
			if (!$attributes['nim']) {
				$object_tahun_ajaran = self::getModelByName('tahun_ajaran');
				$attributes['nim'] = $object_tahun_ajaran->getnextCode();
			}
		}else{
			$object_tahun_ajaran = self::getModelByName('tahun_ajaran');
			$attributes['nim'] = $object_tahun_ajaran->getnextCode();
		}
		$mahasiswa = parent::create($attributes);

		$object_user = self::getModelByName('user');
		$user = $object_user->create([
			'orang_id' => $mahasiswa->orang_id,
			'role' => 'mahasiswa',
			'username' => $attributes['nim']
		]);

		return $mahasiswa;
	}

	public function orang()
	{
		return $this->hasOne(Orang::class, 'id', 'orang_id');
	}

	public function jurusan()
	{
		return $this->hasOne(Jurusan::class, 'id', 'jurusan_id');
	}

	public function semester()
	{
		return $this->hasOne(Semester::class, 'id', 'semester_id');
	}

	public function tahun_ajaran()
	{
		return $this->hasOne(TahunAjaran::class, 'id', 'tahun_ajaran_id');
	}


	public function pengajuan_ks()
	{
		return $this->hasMany(PengajuanKs::class, 'mahasiswa_id', 'id');
	}

	public function riwayat_belajar()
	{
		return $this->hasMany(RiwayatBelajar::class, 'id', 'mahasiswa_id');
	}

	public function mahasiswa_bimbingan()
	{
		return $this->hasMany(MahasiswaBimbingan::class, 'id', 'mahasiswa_id');
	}

	public function register_ulang()
	{
		return $this->hasMany(RegisterUlang::class, 'id', 'mahasiswa_id');
	}

	public function khs()
	{
		return $this->hasMany(Khs::class, 'mahasiswa_id', 'id');
	}


}