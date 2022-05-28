<?php

namespace App\Objects;

use App\Objects\BaseModel;
use App\Objects\MailModel;

class Pmb extends BaseModel
{
	protected $table = 'pmb';
	protected $with  = ['orang', 'jurusan', 'bukti_pembayaran'];

	public $selection_fields = ['status'];

	public static $file_fields = ['bukti_pembayaran'];
	public static $date_fields = [
		'tanggal_pendaftaran',
		'tanggal_verifikasi',
		'tanggal_lulus',
		'tanggal_kesehatan',
		'tanggal_wawancara',
		'test_tertulis',
		'test_kesehatan',
		'test_wawancara',
		'daftar_ulang',
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
	    'test_kesehatan' => 'datetime:d/m/Y',
	    'test_wawancara' => 'datetime:d/m/Y',
	    'daftar_ulang' => 'datetime:d/m/Y',
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

	public function pendaftaran()
	{
		return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id', 'id');
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

	public static function create(array $attributes = [])
	{
		$object_sequance = self::getModelByName('sequance');
		$konfigurasi_obj = self::getModelByName('konfigurasi');
		$pta_obj = self::getModelByName('pembiayaan_tahun_ajar');
		$pendaftaran_obj = self::getModelByName('pendaftaran');

		self::checkPendaftaran($attributes);

		$object_sequance_next_number = $object_sequance->getnextCode('nomor_peserta');
		$attributes['nomor_peserta'] = $object_sequance_next_number;

		$pmb = parent::create($attributes);

		if ($pmb->pendaftaran_id) {
			$pendaftaran = $pmb->pendaftaran;
		} else {
			$pendaftaran = $pendaftaran_obj->where('status', 'open')->first();
		}
		$tahun_ajaran_id = $pendaftaran->tahun_ajaran_id;

		$konfigurasi = $konfigurasi_obj->first();
		$pta = $pta_obj->where([
			['tahun_ajaran_id', $tahun_ajaran_id],
			['registrasi', true]
		])->first();

        $tagihan = $pta->createTagihan($pmb->orang_id);

        $object_user = self::getModelByName('user');
		$user = $object_user->create([
			'orang_id' => $pmb->orang_id,
			'role' => 'pmb',
			'username' => $pmb->orang->nik
		]);

		return $pmb;
	}

	public function update(array $attributes = [], array $options = [])
	{
		if (array_key_exists('status', $attributes)) {
			$status = $attributes['status'];

			switch ($status) {
				case 'terverifikasi':
					$transaksi_obj = self::getModelByName('transaksi');
					$tagihan_obj = self::getModelByName('tagihan');

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
					$attributes['test'] = true;
					$attributes['tanggal_lulus'] = date('d/m/Y');
					break;

				case 'kesehatan_lulus':
					$attributes['kesehatan'] = true;
					$attributes['tanggal_kesehatan'] = date('d/m/Y');
					break;
				
				case 'wawancara_lulus':
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