<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Orang extends BaseModel
{
	protected $table = 'orang';
	protected $with = [
		'agama', 'kwitansi', 'user',
		'ijazah', 'ktp', 'surket_menikah', 'pasfoto', 'akte_lahir', 'kartu_keluarga', 'kartu_vaksin'
	];

	public $like_fields = ['nama'];

	protected $appends = ['jenis_kelamin_label'];

	public $selection_fields = ['status', 'jenis_kelamin'];
	public static $relation = [
		['name' => 'agama', 'is_selection' => true, 'skip' => false],
		['name' => 'kwitansi', 'is_selection' => false, 'skip' => true],
		['name' => 'role', 'is_selection' => true, 'skip' => true],
	];

	public static $file_fields = ['ijazah', 'ktp', 'surket_menikah', 'pasfoto', 'akte_lahir', 'kartu_keluarga', 'kartu_vaksin'];
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
	public function akte_lahir()
	{
		return $this->hasOne(File::class, 'id', 'akte_lahir_id');
	}
	public function kartu_keluarga()
	{
		return $this->hasOne(File::class, 'id', 'kartu_keluarga_id');
	}
	public function kartu_vaksin()
	{
		return $this->hasOne(File::class, 'id', 'kartu_vaksin_id');
	}

	public static function create(array $attributes = [])
	{
		$orang_obj = self::getModelByName('orang');

		if (array_key_exists('nik', $attributes)) {
			$nik = $attributes['nik'];
			$orang = $orang_obj->where('nik', $nik)->count();

			if ($orang > 0) {
				throw new \Exception("NIK sudah ada di dalam sistem");
			}
		}

		$orang = parent::create($attributes);

		return $orang;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('user', $attributes)) {
			$this->user->update($attributes['user']);
			unset($attributes['user']);
		}

		return parent::update($attributes, $options);
	}

	public function sendResetPass()
	{
		$token = $this->user->generateToken();
		$konfigurasi = self::getModelByName('konfigurasi')->first();
		$mailModel = new MailModel();

		$recipient = [
			"email" => $this->email,
			"name" => $this->nama
		];
		$subject = "Reset Password";
		$data = [
			'orang' => $this, 
			'base_url' => $konfigurasi->base_url,
			'token' => $token
		];
		$content = self::renderHtml("reports/reset_password_email.phtml", $data);

		$mailModel->sendEmail($recipient, $subject, $content);
	}
}