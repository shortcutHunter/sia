<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\MahasiswaModel;
use App\Models\TahunAjaranModel;
use Psr\Container\ContainerInterface;

final class PenerbitanNimModel extends BaseModel
{
  protected $table_name = "penerbitan_nim";
  protected $relation = ['pmb'];

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->mahasiswa = new MahasiswaModel($this->container);
    $this->tahun_ajaran = new TahunAjaranModel($this->container);
  }

  public function selection()
  {
    $selection = [];
    $selection['status'] = [
      'terbit' => 'Terbit',
      'pengajuan' => 'Pengajuan',
      'belum' => 'Belum Terbit',
    ];
    $this->selection = $selection;
  }

  public function getTahunAjaran()
  {
    $tahun_ajaran = $this->tahun_ajaran;
    $tahun_ajaran->get([['status', 'aktif']]);
    if (!$tahun_ajaran->data || $tahun_ajaran->data->isEmpty()) {
      $tahun_ajaran->create([
        'nama' => date("Y")."/".((int)date('Y') + 1),
        'tahun' => date("Y")
      ]);
      $tahun_ajaran = $tahun_ajaran->data;
    }else{
      $tahun_ajaran = $tahun_ajaran->data[0];
    }
    return $tahun_ajaran;
  }

  public function getNim($tahun_ajaran)
  {
    $nim = $tahun_ajaran->tahun."114069E".sprintf('%04d', $tahun_ajaran->code);
    $this->tahun_ajaran->update($tahun_ajaran->id, ['code' => ($tahun_ajaran->code+1)]);
    return $nim;
  }

  public function update($id, $value)
  {
    $this->read($id);

    if ($value['status'] != $this->data->status) {
      switch ($value['status']) {
        case 'terbit':
          $tahun_ajaran = $this->getTahunAjaran();
          $nim = $this->getNim($tahun_ajaran);

          $this->mahasiswa->create([
            'orang_id' => $this->data->pmb->orang_id,
            'nim' => $nim,
            'tahun_ajaran_id' => $tahun_ajaran->id,
            'jurusan_id' => $this->data->pmb->jurusan_id,
            'ajukan_sks' => true,
          ]);
        break;
      }
    }
    parent::update($id, $value);
  }
}
