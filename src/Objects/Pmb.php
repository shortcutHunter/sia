<?php

namespace App\Objects;

use App\Objects\BaseModel;
use App\Objects\MailModel;

class Pmb extends BaseModel
{
	protected $table = 'pmb';
	protected $with  = ['orang', 'jurusan', 'bukti_pembayaran', 'dokumen_kesehatan', 'panitia'];

	public $selection_fields = ['status'];

	public static $file_fields = ['bukti_pembayaran', 'dokumen_kesehatan'];
	public static $date_fields = [
		'tanggal_pendaftaran',
		'tanggal_verifikasi',
		'tanggal_lulus',
		'tanggal_kesehatan',
		'tanggal_wawancara',
		'test_tertulis',
		'test_tertulis_end',
		'test_kesehatan',
		'test_kesehatan_end',
		'test_wawancara',
		'test_wawancara_end',
		'daftar_ulang',
		'daftar_ulang_end',
	];

	protected $appends = ['status_label'];

	public $status_enum = [
		"baru" => "Baru",
		"pending" => "Pending",
		"terverifikasi" => "Terverifikasi",
		"test_lulus" => "Lulus Test",
		"test_gagal" => "Gagal Test",
		"kesehatan_lulus" => "Lulus Kesehatan",
		"kesehatan_gagal" => "Gagal Kesehatan",
		"wawancara_lulus" => "Lulus Wawancara",
		"wawancara_gagal" => "Gagal Wawancara"
	];

	public static $relation = [
		['name' => 'orang', 'is_selection' => false, 'skip' => false],
		['name' => 'jurusan', 'is_selection' => true, 'skip' => true],
		['name' => 'penerbitan_nim', 'is_selection' => true, 'skip' => true]
	];

	protected $casts = [
	    'tanggal_pendaftaran' => 'datetime:d/m/Y',
	    'tanggal_verifikasi' => 'datetime:d/m/Y',
	    'tanggal_lulus' => 'datetime:d/m/Y',
	    'tanggal_kesehatan' => 'datetime:d/m/Y',
	    'tanggal_wawancara' => 'datetime:d/m/Y',
	    'test_tertulis' => 'datetime:d/m/Y',
	    'test_tertulis_end' => 'datetime:d/m/Y',
	    'test_kesehatan' => 'datetime:d/m/Y',
	    'test_kesehatan_end' => 'datetime:d/m/Y',
	    'test_wawancara' => 'datetime:d/m/Y',
	    'test_wawancara_end' => 'datetime:d/m/Y',
	    'daftar_ulang' => 'datetime:d/m/Y',
	    'daftar_ulang_end' => 'datetime:d/m/Y',
	];

	public function orang()
	{
		return $this->belongsTo(Orang::class, 'orang_id', 'id');
	}

	public function jurusan()
	{
		return $this->hasOne(Jurusan::class, 'id', 'jurusan_id');
	}

	public function bukti_pembayaran()
	{
		return $this->hasOne(File::class, 'id', 'bukti_pembayaran_id');
	}

	public function dokumen_kesehatan()
	{
		return $this->hasOne(File::class, 'id', 'dokumen_kesehatan_id');
	}

	public function pendaftaran()
	{
		return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id', 'id');
	}

	public function panitia()
	{
		return $this->belongsTo(Panitia::class, 'panitia_id', 'id');
	}

	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	public function getPernyataanAttribute($value) {
		return $value ? true : false;
	}

	public static function checkPendaftaran($attributes) {
		$pendaftaran_obj = self::getModelByName('pendaftaran');
		$result = false;

		if (array_key_exists('pendaftaran_id', $attributes)) {
			$pendaftaran = $pendaftaran_obj->find($attributes['pendaftaran_id']);

			if (!empty($pendaftaran)) {
				if ($pendaftaran->status != 'open') {
					throw new \Exception("Harap buka status pendaftaran");
				}
			} else {
				throw new \Exception("Pendaftaran tidak ditemukan");
			}
		} else {
			$pendaftaran = $pendaftaran_obj->where('status', 'open')->first();
			if (empty($pendaftaran)) {
				throw new \Exception("Pendaftaran tidak ditemukan");
			} else {
				$result = true;
			}
		}

		return $result;
	}

	public function kartuPeserta() {
		$value = ['pmb' => $this];

		$pdf = self::renderPdf("reports/kartu_peserta.phtml", $value);
		return $pdf;
	}

	public function sendKartuPeserta() {
		$mailModel = new MailModel();
		$recipient = [
			"email" => $this->orang->email,
			"name" => $this->orang->nama
		];
		$subject = "Kartu Peserta Ujian";
		$content = self::renderHtml("reports/kartu_peserta_email.phtml", ['pmb' => $this]);
		$attachment = [
			"file" => $this->kartuPeserta(),
			"name" => "Kartu Peserta.pdf"
		];

		$mailModel->sendEmail($recipient, $subject, $content, $attachment);
	}

	public function sendLoginDetail() {
		$konfigurasi = self::getModelByName('konfigurasi')->first();
		$mailModel = new MailModel();
		$recipient = [
			"email" => $this->orang->email,
			"name" => $this->orang->nama
		];
		$subject = "Login Detail";
		$content = self::renderHtml("reports/login_detail_email.phtml", ['pmb' => $this, 'base_url' => $konfigurasi->base_url]);

		$mailModel->sendEmail($recipient, $subject, $content);
	}

	public static function cTDate($attributes, $string) {
		if (array_key_exists($string, $attributes)) {
			return strtotime(str_replace('/', '-', $attributes[$string]));
		}

		return null;
	}

	public static function convertToDate($value)
	{
		if ($value) {
			$value = str_replace('/', '-', $value);
			return strtotime($value);
		}

		return null;
	}

	public static function validasiTanggal($attributes, $pmb) {
		$tanggal_pendaftaran = self::cTDate($attributes, 'tanggal_pendaftaran');
		$test_tertulis = self::cTDate($attributes, 'test_tertulis');
		$test_tertulis_end = self::cTDate($attributes, 'test_tertulis_end');
		$test_kesehatan = self::cTDate($attributes, 'test_kesehatan');
		$test_kesehatan_end = self::cTDate($attributes, 'test_kesehatan_end');
		$test_wawancara = self::cTDate($attributes, 'test_wawancara');
		$test_wawancara_end = self::cTDate($attributes, 'test_wawancara_end');
		$daftar_ulang = self::cTDate($attributes, 'daftar_ulang');
		$daftar_ulang_end = self::cTDate($attributes, 'daftar_ulang_end');

		$tanggal_lulus = self::cTDate($attributes, 'tanggal_lulus');
		$tanggal_verifikasi = self::cTDate($attributes, 'tanggal_verifikasi');
		$tanggal_kesehatan = self::cTDate($attributes, 'tanggal_kesehatan');
		$tanggal_wawancara = self::cTDate($attributes, 'tanggal_wawancara');

		if ($pmb != null) {
			$tanggal_pendaftaran = self::convertToDate($pmb->tanggal_pendaftaran) ?: $tanggal_pendaftaran;
			$test_tertulis = self::convertToDate($pmb->test_tertulis) ?: $test_tertulis;
			$test_tertulis_end = self::convertToDate($pmb->test_tertulis_end) ?: $test_tertulis_end;
			$test_kesehatan = self::convertToDate($pmb->test_kesehatan) ?: $test_kesehatan;
			$test_kesehatan_end = self::convertToDate($pmb->test_kesehatan_end) ?: $test_kesehatan_end;
			$test_wawancara = self::convertToDate($pmb->test_wawancara) ?: $test_wawancara;
			$test_wawancara_end = self::convertToDate($pmb->test_wawancara_end) ?: $test_wawancara_end;
			$daftar_ulang = self::convertToDate($pmb->daftar_ulang) ?: $daftar_ulang;
			$daftar_ulang_end = self::convertToDate($pmb->daftar_ulang_end) ?: $daftar_ulang_end;
			
			$tanggal_lulus = self::convertToDate($pmb->tanggal_lulus) ?: $tanggal_lulus;
			$tanggal_verifikasi = self::convertToDate($pmb->tanggal_verifikasi) ?: $tanggal_verifikasi;
			$tanggal_kesehatan = self::convertToDate($pmb->tanggal_kesehatan) ?: $tanggal_kesehatan;
			$tanggal_wawancara = self::convertToDate($pmb->tanggal_wawancara) ?: $tanggal_wawancara;
		}

		if ($tanggal_pendaftaran == null) {
			$tanggal_pendaftaran = date('d/m/Y');
			$attributes['tanggal_pendaftaran'] = $tanggal_pendaftaran;
			$tanggal_pendaftaran = self::convertToDate($tanggal_pendaftaran);
		}

		if ($test_tertulis != null) {
			if ($test_tertulis > $test_tertulis_end) {
				throw new \Exception("Tanggal test tertulis mulai tidak dapat lebih besar dari test tertulis akhir");
			}

			if ($test_tertulis < $tanggal_pendaftaran) {
				throw new \Exception("Tanggal test tertulis tidak dapat lebih kecil dari tanggal pendaftaran");
			}

			$attributes['terverif'] = true;
        	if ($tanggal_verifikasi == null) {
        		$attributes['tanggal_verifikasi'] = date('d/m/Y');
        		$attributes['status'] = 'terverifikasi';
        	}
		}

		if ($test_kesehatan != null) {
			if ($test_kesehatan > $test_kesehatan_end) {
				throw new \Exception("Tanggal test kesehatan mulai tidak dapat lebih besar dari test kesehatan akhir");
			}

			if ($test_kesehatan < $tanggal_pendaftaran) {
				throw new \Exception("Tanggal test kesehatan tidak dapat lebih kecil dari tanggal pendaftaran");
			}

			$attributes['test'] = true;
			if ($tanggal_lulus == null) {
				$attributes['tanggal_lulus'] = date('d/m/Y');
        		$attributes['status'] = 'test_lulus';
			}
		}

		if ($test_wawancara != null) {
			if ($test_wawancara > $test_wawancara_end) {
				throw new \Exception("Tanggal test wawancara mulai tidak dapat lebih besar dari test wawancara akhir");
			}

			if ($test_wawancara < $tanggal_pendaftaran) {
				throw new \Exception("Tanggal test wawancara tidak dapat lebih kecil dari tanggal pendaftaran");
			}

			$attributes['kesehatan'] = true;
			if ($tanggal_kesehatan == null) {
				$attributes['tanggal_kesehatan'] = date('d/m/Y');
        		$attributes['status'] = 'kesehatan_lulus';
			}
		}

		if ($daftar_ulang != null) {
			if ($daftar_ulang > $daftar_ulang_end) {
				throw new \Exception("Tanggal daftar ulang mulai tidak dapat lebih besar dari daftar ulang akhir");
			}

			if ($daftar_ulang < $tanggal_pendaftaran) {
				throw new \Exception("Tanggal daftar ulang tidak dapat lebih kecil dari tanggal pendaftaran");
			}
			$attributes['wawancara'] = true;
			if ($tanggal_wawancara == null) {
				$attributes['tanggal_wawancara'] = date('d/m/Y');
	    		$attributes['status'] = 'wawancara_lulus';
	    	}
		}

		return $attributes;
	}

	public static function create(array $attributes = [])
	{
		$object_sequance = self::getModelByName('sequance');
		$konfigurasi_obj = self::getModelByName('konfigurasi');
		$pta_obj = self::getModelByName('pembiayaan_tahun_ajar');
		$pendaftaran_obj = self::getModelByName('pendaftaran');

		self::checkPendaftaran($attributes);

		$object_sequance_next_number = $object_sequance->getnextCode('nomor_peserta');
		$attributes['nomor_peserta'] = $object_sequance_next_number;
		
		if (!array_key_exists('panitia_id', $attributes)) {
			$konfigurasi = $konfigurasi_obj->first();
			$attributes['panitia_id'] = $konfigurasi->getNextPanitia();	
		}

		if (!array_key_exists('pendaftaran_id', $attributes)) {
			$pendaftaran = $pendaftaran_obj->where('status', 'open')->first();
			$attributes['pendaftaran_id'] = $pendaftaran->id;
		}

		$attributes = self::validasiTanggal($attributes, null);

		$pmb = parent::create($attributes);

		if ($pmb->pendaftaran_id) {
			$pendaftaran = $pmb->pendaftaran;
		} else {
			$pendaftaran = $pendaftaran_obj->where('status', 'open')->first();
		}
		$tahun_ajaran_id = $pendaftaran->tahun_ajaran_id;

		$pta = $pta_obj->where([
			['tahun_ajaran_id', $tahun_ajaran_id],
			['registrasi', true]
		])->first();

        $tagihan = $pta->createTagihan($pmb->orang_id);

        $object_user = self::getModelByName('user');
        $role_obj = self::getModelByName('role');

        $role = $role_obj->where('value', 'pmb')->first();

        $user_value = [
			'orang_id' => $pmb->orang_id,
			'role' => [['id' => $role->id]],
			'username' => $pmb->orang->nik
		];

		if ($pmb->orang->tanggal_lahir) {
			$user_value['password'] = date('dmY', strtotime($pmb->orang->tanggal_lahir));
		}
		$user = $object_user->create($user_value);

		if (!array_key_exists('biaya_pendaftaran', $attributes)) {
			$pmb->update(['biaya_pendaftaran' => $tagihan->nominal]);
		}

		$pmb = $pmb->refresh();

		$pmb->sendLoginDetail();

		if (array_key_exists('status', $attributes)) {
			$status = $attributes['status'];

			switch ($status) {
				case 'terverifikasi':
					$pmb->sendKartuPeserta();
					break;				
				case 'wawancara_lulus':
					$object_penerbitan_nim = self::getModelByName('penerbitan_nim');
					$penerbitan_nim = $object_penerbitan_nim->where('pmb_id', $pmb->id)->first();

					if (empty($penerbitan_nim)) {
						$object_penerbitan_nim->create(['pmb_id' => $pmb->id, 'tahun' => date('Y')]);
					}
					break;
			}
		}

		return $pmb;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('panitia', $attributes)) {
			unset($attributes['panitia']);
		}
		
		$attributes = self::validasiTanggal($attributes, $this);

		if (array_key_exists('status', $attributes)) {
			$status = $attributes['status'];


			switch ($status) {
				case 'terverifikasi':
					$this->sendKartuPeserta();
					break;				
				case 'wawancara_lulus':
					$object_penerbitan_nim = self::getModelByName('penerbitan_nim');
					$penerbitan_nim = $object_penerbitan_nim->where('pmb_id', $this->id)->first();

					if (empty($penerbitan_nim)) {
						$object_penerbitan_nim->create(['pmb_id' => $this->id, 'tahun' => date('Y')]);
					}
					break;
			}
		}

		return parent::update($attributes, $options);
	}
}