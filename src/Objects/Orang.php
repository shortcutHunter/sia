<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Orang extends BaseModel
{
	protected $table = 'orang';
	protected $with = ['agama', 'ijazah', 'ktp', 'surket_menikah', 'kwitansi', 'pasfoto', 'user'];

	public $like_fields = ['nama'];

	protected $appends = ['jenis_kelamin_label'];

	public $selection_fields = ['status', 'jenis_kelamin'];
	public static $relation = [
		['name' => 'agama', 'is_selection' => true, 'skip' => false],
		['name' => 'kwitansi', 'is_selection' => false, 'skip' => true]
	];

	public static $file_fields = ['ijazah', 'ktp', 'surket_menikah', 'pasfoto'];
	public static $date_fields = ['tanggal_lahir'];

	public $status_enum = [
		"aktif" => "Aktif",
		"inaktif" => "Inaktif",
		"berhenti" => "Berhenti",
	];

	public $jenis_kelamin_enum = [
		"l" => "Laki-laki",
		"p" => "Perempuan"
	];

	protected $casts = [
	    'tanggal_lahir' => 'datetime:d/m/Y',
	];

	public function getJenisKelaminLabelAttribute() {
		$jenis_kelamin_enum = $this->jenis_kelamin_enum;
		$label = null;

		if ($this->jenis_kelamin) {
			$label = $jenis_kelamin_enum[$this->jenis_kelamin];
		}

		return $label;
	}

	public function agama()
	{
		return $this->hasOne(Agama::class, 'id', 'agama_id');
	}

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'id', 'orang_id');
	}
	public function karyawan()
	{
		return $this->belongsTo(Karyawan::class, 'id', 'orang_id');
	}
	public function user()
	{
		return $this->belongsTo(User::class, 'id', 'orang_id');
	}

	public function kwitansi()
	{
		return $this->hasMany(Kwitansi::class, 'id', 'orang_id');
	}

	public function pmb()
	{
		return $this->hasOne(Pmb::class, 'orang_id', 'id');
	}

	// File
	public function ijazah()
	{
		return $this->hasOne(File::class, 'id', 'ijazah_id');
	}
	public function ktp()
	{
		return $this->hasOne(File::class, 'id', 'ktp_id');
	}
	public function surket_menikah()
	{
		return $this->hasOne(File::class, 'id', 'surket_menikah_id');
	}
	public function pasfoto()
	{
		return $this->hasOne(File::class, 'id', 'pasfoto_id');
	}


	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('user', $attributes)) {
			unset($attributes['user']);
		}

		return parent::update($attributes, $options);
	}
}