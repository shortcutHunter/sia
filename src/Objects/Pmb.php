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

		$user = $object_user->create([
			'orang_id' => $pmb->orang_id,
			'role' => [['id' => $role->id]],
			'username' => $pmb->orang->nik
		]);

		if (!array_key_exists('biaya_pendaftaran', $attributes)) {
			$pmb->update(['biaya_pendaftaran' => $tagihan->nominal]);
		}

		$pmb = $pmb->refresh();

		$pmb->sendLoginDetail();

		return $pmb;
	}

	public function convertToDate($value)
	{
		$value = str_replace('/', '-', $value);
		return strtotime($value);
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('panitia', $attributes)) {
			unset($attributes['panitia']);
		}

		if (array_key_exists('status', $attributes)) {
			$status = $attributes['status'];

			switch ($status) {
				case 'terverifikasi':
					$transaksi_obj = self::getModelByName('transaksi');
					$tagihan_obj = self::getModelByName('tagihan');

					$test_tertulis = $this->convertToDate($attributes['test_tertulis']);

					if ($test_tertulis < strtotime($this->tanggal_pendaftaran->toDateTimeString())) {
						throw new \Exception("Tanggal test tertulis tidak dapat lebih kecil dari tanggal pendaftaran");
					}

	                $tagihan = $tagihan_obj->where('orang_id', $this->orang_id)->first();

	                $nominal = $tagihan->nominal;
	                if (array_key_exists('nominal', $attributes)) {
	                	$nominal = $attributes['nominal'];
	                }

	                $tagihan_bukti_bayar = [['file_id' => $this->bukti_pembayaran_id]];
	                $transaksi = $tagihan->bayarTagihan($nominal, $tagihan_bukti_bayar, 'verified');

	                $attributes['terverif'] = true;
	                $attributes['tanggal_verifikasi'] = date('d/m/Y');

					$this->sendKartuPeserta();
					break;
				
				case 'test_lulus':
					$test_kesehatan = $this->convertToDate($attributes['test_kesehatan']);
					if ($test_kesehatan < strtotime($this->test_tertulis_end->toDateTimeString())) {
						throw new \Exception("Tanggal test kesehatan tidak dapat lebih kecil dari tanggal test tertulis");
					}

					$attributes['test'] = true;
					$attributes['tanggal_lulus'] = date('d/m/Y');
					break;

				case 'kesehatan_lulus':
					$test_wawancara = $this->convertToDate($attributes['test_wawancara']);
					if ($test_wawancara < strtotime($this->test_kesehatan_end->toDateTimeString())) {
						throw new \Exception("Tanggal test wawancara tidak dapat lebih kecil dari tanggal test kesehatan");
					}

					$attributes['kesehatan'] = true;
					$attributes['tanggal_kesehatan'] = date('d/m/Y');
					break;
				
				case 'wawancara_lulus':
					$daftar_ulang = $this->convertToDate($attributes['daftar_ulang']);
					if ($daftar_ulang < strtotime($this->test_wawancara_end->toDateTimeString())) {
						throw new \Exception("Tanggal daftar ulang tidak dapat lebih kecil dari tanggal test wawancara");
					}

					$attributes['wawancara'] = true;
					$attributes['tanggal_wawancara'] = date('d/m/Y');

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