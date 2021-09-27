<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\OrangModel;
use App\Models\SequanceModel;
use App\Models\KonfigurasiModel;
use App\Models\KwitansiModel;
use App\Models\PenerbitanNimModel;
use Psr\Container\ContainerInterface;

final class PmbModel extends BaseModel
{
  protected $table_name = "pmb";
  protected $relation = ['orang', 'jurusan'];

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->orang = new OrangModel($this->container);
    $this->penerbitan_nim = new PenerbitanNimModel($this->container);
    $this->sequance = new SequanceModel($this->container);
    $this->konfigurasi = new KonfigurasiModel($this->container);
    $this->kwitansi = new KwitansiModel($this->container);
  }

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
		"terima" => "Terima",
		"tolak" => "Tolak",
		"ujian" => "Ujian",
		"terverifikasi" => "Terverifikasi",
		"baru" => "Baru",
    ];
    $this->selection = $selection;
  }

  public function get($query=[], $search=false)
  {
    if ($search) {
      $query = "orang_id IN (SELECT id FROM orang WHERE LOWER(nama) like '%".$search."%')";
      return parent::raw($query);
    }

    return parent::get($query, $search);
  }

  public function create($value)
  {
    if (array_key_exists("orang", $value))
    {
      $this->orang->create($value['orang']);
      $orang_id = $this->orang->data->id;
      $value['orang_id'] = $orang_id;
    }
    parent::create($value);
  }

  public function generate_nomor_peserta()
  {
    $orang = $this->data->orang;
    $sequance = $this->sequance;
    $sequance->get([['kode', 'nomor_pmb']]);
    if ($sequance->data->isEmpty()) {
      $value = ['kode' => 'nomor_pmb', 'nama' => 'Nomor Peserta PMB'];
      $sequance->create($value);
    }
    $sequance_data = $sequance->data[0];
    $nomor_peserta = $orang->nik."-".sprintf('%03d', $sequance_data->nomor);
    $sequance->update($sequance_data->id, ['nomor' => ($sequance_data->nomor+1)]);
    return $nomor_peserta;
  }

  private function generateKwitansi()
  {
    $konfigurasi = $this->konfigurasi;
    $kwitansi = $this->kwitansi;

    $konfigurasi->read(1);
    $kwitansi_value = [
      "tanggal" => date("Y-m-d"),
      "orang_id" => $this->data->orang_id,
      "status" => "terverifikasi"
    ];

    if ($konfigurasi->data) {
      $kwitansi_value['paket_id'] = $konfigurasi->data->paket_id;
      $kwitansi_value['nominal'] = $konfigurasi->data->nominal;
    }
    $kwitansi->create($kwitansi_value);
  }

  public function update($id, $value)
  {
    $this->read($id);
    if (array_key_exists("orang", $value))
    {
      $this->orang->update($this->data->orang_id, $value['orang']);
    }

    if ($value['status'] != $this->data->status) {
      switch ($value['status']) {
        case 'terverifikasi':
        $this->generateKwitansi();
          $value['nomor_peserta'] = $this->generate_nomor_peserta();
        break;
        case 'terima':
          $this->penerbitan_nim->create(['pmb_id' => $this->data->id, 'tahun' => date("Y")]);
        break;
      }
    }
    parent::update($id, $value);
  }

  public function delete($id)
  {
    $this->read($id);
    parent::delete($id);
    return $this->orang->delete($this->data->orang_id);
  }
}
