<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\MahasiswaModel;
use App\Models\KwitansiModel;
use App\Models\PaketRegisterUlangModel;
use Psr\Container\ContainerInterface;

final class RegisterUlangModel extends BaseModel
{
  protected $table_name = "register_ulang";
  protected $relation = ['mahasiswa'];

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->mahasiswa = new MahasiswaModel($container);
    $this->kwitansi = new KwitansiModel($container);
    $this->paket_register_ulang = new PaketRegisterUlangModel($container);
  }

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'proses' => 'Proses',
      'terverifikasi' => 'Terverifikasi'
    ];
    $this->selection = $selection;
  }

  public function generate_kode_reg()
  {
    $result = bin2hex(random_bytes(5));
    return $result;
  }

  private function generateKwitansi()
  {
    $kwitansi = $this->kwitansi;
    $paket_register_ulang = $this->paket_register_ulang;

    $kwitansi_value = [
      "tanggal" => date("d/m/Y"),
      "orang_id" => $this->data->mahasiswa->orang_id,
      "status" => "terverifikasi"
    ];

    $paket_register_ulang->get([['tahun_ajaran_id', $this->data->mahasiswa->tahun_ajaran_id]]);
    $paket_register_ulang_data = $paket_register_ulang->data;

    if (!$paket_register_ulang_data->isEmpty()) {
      $paket_register_ulang_data = $paket_register_ulang_data[0];
      $kwitansi_value['paket_id'] = $paket_register_ulang_data->paket_id;
      $kwitansi_value['nominal'] = $paket_register_ulang_data->nominal;
    }

    $kwitansi->create($kwitansi_value);
  }

  public function update($id, $value)
  {
    $this->read($id);
    if ($value['status'] != $this->data->status) {
      switch ($value['status']) {
        case 'terverifikasi':
          $this->generateKwitansi();
          $value['kode_register'] = $this->generate_kode_reg();
          $this->mahasiswa->update($this->data->mahasiswa_id, ['ajukan_sks' => true, 'kode_validasi' => true]);
        break;

        case 'tolak':
          $this->mahasiswa->update($this->data->mahasiswa_id, ['ajukan_sks' => false, 'kode_validasi' => false, 'register_ulang' => true, 'pengajuan' => true]);
        break;
      }
    }
    parent::update($id, $value);
  }
}
