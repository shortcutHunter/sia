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
		$tahun_ajaran_obj = self::getModelByName('tahun_ajaran');
		$role_obj = self::getModelByName('role');

		if (!array_key_exists('tahun_ajaran_id', $attributes)) {
			throw new \Exception("Harap masukkan tahun ajaran saat membuat mahasiswa");
		}

		$tahun_ajaran = $tahun_ajaran_obj->where('id', $attributes['tahun_ajaran_id'])->first();

		if (empty($tahun_ajaran)) {
			throw new \Exception("Harap masukkan tahun ajaran saat membuat mahasiswa");
		}

		if (array_key_exists('nim', $attributes)) {
			if (!$attributes['nim']) {
				$attributes['nim'] = $tahun_ajaran->getnextCode();
			}
		}else{
			$attributes['nim'] = $tahun_ajaran->getnextCode();
		}
		$mahasiswa = parent::create($attributes);

		$role = $role_obj->where('value', 'mahasiswa')->first();

		$mahasiswa->orang->user->update([
			'role' => [['id' => $role->id]],
			'username' => $mahasiswa->nim
		]);

		return $mahasiswa;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('status', $attributes)) {
			switch ($attributes['status']) {
				case 'alumni':
					$role_obj = self::getModelByName('role');
					$role = $role_obj->where('value', 'alumni')->first();

					$this->orang->user->update([
						'role' => [['id' => $role->id]]
					]);
					break;
				case 'dropout':
					$role_obj = self::getModelByName('role');
					$role = $role_obj->where('value', 'dropout')->first();

					$this->orang->user->update([
						'role' => [['id' => $role->id]]
					]);
					break;
			}
		}

		return parent::update($attributes, $options);
	}

	public function buatTagihanMahasiswa($pta_id) {
		$pta_obj = self::getModelByName('pembiayaan_tahun_ajar');
		$pta = $pta_obj->find($pta_id);

		$tagihan = $pta->createTagihan($this->orang_id);

		if ($pta->$semester_id != null) {
			$this->update([
	            "tagihan_id" => $tagihan->id
	        ]);
		}
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
		return $this->hasMany(RiwayatBelajar::class, 'mahasiswa_id', 'id');
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

	public function tagihan()
	{
		return $this->hasOne(Tagihan::class, 'id', 'tagihan_id');
	}


}