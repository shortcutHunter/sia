<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Illuminate\Database\Capsule\Manager as DB;

final class ReportController extends BaseController
{

    public function kartu_peserta($request, $response, $args)
   {
        $container = $this->container;
        $pmb_id = $args['pmb_id'];
        $object = $this->get_object('pmb');
        $pmb = $object->find($pmb_id);
        $tgl_lahir = $pmb->orang->tanggal_lahir;

        if ($tgl_lahir) {
            $tgl_lahir = date("d-m-Y", strtotime($tgl_lahir));
        }

        $value = ['pmb' => $pmb, 'tgl_lahir' => $tgl_lahir];

        $pdfContent = $this->container->get('renderPDF')("reports/kartu_peserta.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        // return $response
        //     ->withHeader('Content-Type', 'application/pdf')
        //     ->withStatus(201);
   }

    public function transkrip($request, $response, $args)
   {
        $container = $this->container;
        $mahasiswa_id = $args['mahasiswa_id'];
        $mahasiswa_obj = $this->get_object('mahasiswa');
        // $riwayat_belajar_obj = $this->get_object('riwayat_belajar');
        $riwayat_belajar_detail_obj = $this->get_object('riwayat_belajar_detail');
        $mahasiswa = $mahasiswa_obj->find($mahasiswa_id);
        // $riwayat_belajar = $riwayat_belajar_obj->where('mahasiswa_id', $mahasiswa_id)->get();

        $riwayat_belajar_detail = $riwayat_belajar_detail_obj
                ->select(
                    'mata_kuliah_id', 
                    'nilai_bobot', 
                    'nilai_mutu', 
                    DB::raw('MAX(nilai_absolut) as nilai_absolut'),
                    DB::raw('mata_kuliah.sks * nilai_mutu as nilai_point')
                )
                ->whereHas('riwayat_belajar', function($q) use ($mahasiswa_id) {
                    $q->where('mahasiswa_id', $mahasiswa_id);
                })
                ->join('mata_kuliah', 'mata_kuliah.id', '=', 'riwayat_belajar_detail.mata_kuliah_id')
                ->groupBy('mata_kuliah_id')
                ->orderBy('mata_kuliah_id')
                ->orderBy('mata_kuliah.semester_id')
                ->get();

        $longest_table = 0;
        $jumlah_sks = $riwayat_belajar_detail->sum('mata_kuliah.sks');
        $jumlah_point = $riwayat_belajar_detail->sum('nilai_point');
        $ipk = ceil($jumlah_point / $jumlah_sks);
        $predikat = "";
        $predikat_eng = "";

        if ($ipk >= 3.51) {
            $predikat = "Dengan Pujian / Cum Laude";
            $predikat_eng = "Excellent";
        }
        else if ($ipk >= 2.76) {
            $predikat = "Sangat Memuaskan";
            $predikat_eng = "Highly Satisfactory";
        }
        else {
            $predikat = "Memuaskan";
            $predikat_eng = "Satisfactory";
        }

        $riwayat_belajar_detail_chunk = $riwayat_belajar_detail->chunk(22);

        foreach ($riwayat_belajar_detail_chunk as $value) {
            if (count($value) > $longest_table) {
                $longest_table = count($value);
            }
        }

        $value = [
            'mahasiswa' => $mahasiswa,
            'riwayat_belajar_detail_chunk' => $riwayat_belajar_detail_chunk,
            'longest_table' => $longest_table,
            'jumlah_sks' => $jumlah_sks,
            'jumlah_point' => $jumlah_point,
            'predikat' => $predikat,
            'predikat_eng' => $predikat_eng,
            'ipk' => $ipk,
        ];

        $pdfContent = $this->container->get('renderPDF')("reports/transkrip.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        // $rendered_value = $container->get('renderer')->fetch('reports/transkrip.phtml', $value);
        // $response->getBody()->write($rendered_value);
        // return $response;
   }

    public function krs($request, $response, $args)
   {
        $container = $this->container;
        $mahasiswa_id = $args['mahasiswa_id'];
        $mahasiswa_obj = $this->get_object('mahasiswa');
        $pengajuan_ks_obj = $this->get_object('pengajuan_ks');
        $mahasiswa = $mahasiswa_obj->find($mahasiswa_id);
        $pengajuan_ks = $pengajuan_ks_obj->where([['mahasiswa_id', $mahasiswa->id], ['semester_id', $mahasiswa->semester_id]])->first();

        if (!empty($pengajuan_ks)) {
            $value = ['mahasiswa' => $mahasiswa, 'sks' => $pengajuan_ks->pengajuan_ks_detail];
            $pdfContent = $this->container->get('renderPDF')("reports/krs.phtml", $value, true);
            $pdfContent = ['content' => base64_encode($pdfContent)];
        }else {
            $pdfContent = ['content' => false, 'error' => "Belum ada KRS yang dapat dicetak. harap melakukan pengajuan KRS terlebih dahulu"];
        }

        

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
   }

    public function khs($request, $response, $args)
   {
        $container = $this->container;
        $khs_id    = $args['khs_id'];
        $khs_obj   = $this->get_object('khs');

        $khs = $khs_obj->where('id', $khs_id)->first();


        $value = [
          'mahasiswa' => $khs->mahasiswa, 
          'khs' => $khs,
          'semester' => $khs->semester
        ];
        $pdfContent = $this->container->get('renderPDF')("reports/khs.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
   }

    public function mahasiswa($request, $response, $args)
   {
        $container               = $this->container;
        $mata_kuliah_diampuh_id  = $args['mata_kuliah_diampuh_id'];
        $mahasiswa_obj           = $this->get_object('mahasiswa');
        $mata_kuliah_diampuh_obj = $this->get_object('mata_kuliah_diampuh');

        $mata_kuliah_diampuh = $mata_kuliah_diampuh_obj->find($mata_kuliah_diampuh_id);

        $mahasiswa = $mahasiswa_obj->whereHas(
            'pengajuan_ks', 
            function($q) use ($mata_kuliah_diampuh) {
                $q
                    ->where('status', 'terima')
                    ->where('tahun_ajaran_id', $mata_kuliah_diampuh->dosen_pjmk->tahun_ajaran_id)
                    ->whereHas(
                        'pengajuan_ks_detail', 
                        function($q) use ($mata_kuliah_diampuh) {
                            $q->where('mata_kuliah_id', $mata_kuliah_diampuh->mata_kuliah_id);
                    });
        })->get();

        $value = [
          'mahasiswa' => $mahasiswa,
          'mata_kuliah_diampuh' => $mata_kuliah_diampuh
        ];

        if ($mahasiswa->count() < 1) {
            throw new \Exception("Belum ada mahasiswa yang diajar");
        }

        $pdfContent = $this->container->get('renderPDF')("reports/list_mahasiswa.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
   }

    public function nilai($request, $response, $args)
   {
        $mata_kuliah_diampuh_id  = $args['mata_kuliah_diampuh_id'];
        $mahasiswa_obj           = $this->get_object('mahasiswa');
        $mata_kuliah_diampuh_obj = $this->get_object('mata_kuliah_diampuh');
        $nilai_obj               = $this->get_object('nilai');

        $mata_kuliah_diampuh = $mata_kuliah_diampuh_obj->find($mata_kuliah_diampuh_id);
        $nilai = $nilai_obj->all();

        $mahasiswa = $mahasiswa_obj->whereHas(
            'pengajuan_ks', 
            function($q) use ($mata_kuliah_diampuh) {
                $q
                    ->where('status', 'terima')
                    ->where('tahun_ajaran_id', $mata_kuliah_diampuh->dosen_pjmk->tahun_ajaran_id)
                    ->whereHas(
                        'pengajuan_ks_detail', 
                        function($q) use ($mata_kuliah_diampuh) {
                            $q->where('mata_kuliah_id', $mata_kuliah_diampuh->mata_kuliah_id);
                    });
        })->get();

        foreach ($mahasiswa as $key => $value) {
            $riwayat_belajar_detail_obj = $this->get_object('riwayat_belajar_detail');
            $riwayat_belajar_detail = $riwayat_belajar_detail_obj->with('riwayat_belajar_nilai')
                ->where('mata_kuliah_id', $mata_kuliah_diampuh->mata_kuliah_id)
                ->whereHas(
                    'riwayat_belajar', 
                    function($q) use ($mata_kuliah_diampuh, $value) {
                        $q
                            ->where('semester_id', $mata_kuliah_diampuh->dosen_pjmk->semester_id)
                            ->where('mahasiswa_id', $value->id);
                    }
                )->first();
            $value->{'nilai'} = $riwayat_belajar_detail;
        }

        $value = [
          'mahasiswa' => $mahasiswa,
          'mata_kuliah_diampuh' => $mata_kuliah_diampuh,
          'nilai' => $nilai
        ];

        if ($mahasiswa->count() < 1) {
            throw new \Exception("Belum ada mahasiswa yang diajar");
        }

        $pdfContent = $this->container->get('renderPDF')("reports/rekap_nilai.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
   }

    public function mahasiswaBimbingan($request, $response, $args)
   {
        $dosen_pa_id   = $args['dosen_pa_id'];
        $dosen_pa_obj  = $this->get_object('dosen_pa');

        $dosen_pa = $dosen_pa_obj->find($dosen_pa_id);

        $value = [
          'dosen_pa' => $dosen_pa
        ];

        if ($dosen_pa->mahasiswa_bimbingan->count() < 1) {
            throw new \Exception("Belum ada mahasiswa yang dibimbing");
        }

        $pdfContent = $this->container->get('renderPDF')("reports/mahasiswa_bimbingan.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
   }

    public function dosenPJMK($request, $response, $args)
   {
        $dosen_pjmk_obj  = $this->get_object('dosen_pjmk');

        $dosen_pjmk = $dosen_pjmk_obj->where('status', 'aktif')->get();

        foreach ($dosen_pjmk as $key => $value) {
            $semester_ids = $value->semester->pluck('id')->toArray();

            foreach ($value->mata_kuliah_diampuh as $k => $mata_kuliah_diampuh) {
                $mahasiswa_obj = $this->get_object('mahasiswa');

                $mahasiswa = $mahasiswa_obj->whereHas(
                    'riwayat_belajar',
                    function($q) use ($mata_kuliah_diampuh, $semester_ids) {
                        $q->where('terisi', $mata_kuliah_diampuh->terisi)
                          ->whereIn('semester_id', $semester_ids)
                          ->whereHas(
                            'riwayat_belajar_detail', 
                            function($q) use ($mata_kuliah_diampuh) {
                                $q->where('mata_kuliah_id', $mata_kuliah_diampuh->mata_kuliah_id);
                        });
                    }
                )->count();

                $mata_kuliah_diampuh->{'total_mahasiswa'} = $mahasiswa;
            }
        }

        $value = [
          'dosen_pjmk' => $dosen_pjmk
        ];

        if ($dosen_pjmk->count() < 1) {
            throw new \Exception("Belum ada dosen PJMK yang aktif");
        }

        $pdfContent = $this->container->get('renderPDF')("reports/dosen_pjmk.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
   }

    public function tagihanMahasiswa($request, $response, $args)
   {
        $tagihan_obj = $this->get_object('tagihan');
        $mahasiswa_obj = $this->get_object('mahasiswa');

        $orang = $this->container->get('session')->get('orang');
        $tagihan = $tagihan_obj->where('orang_id', $orang->id)->get();
        $mahasiswa = $mahasiswa_obj->where('orang_id', $orang->id)->first();

        $value = [
          'mahasiswa' => $mahasiswa,
          'tagihan' => $tagihan
        ];

        if ($tagihan->count() < 1) {
            throw new \Exception("Belum ada tagihan");
        }

        $pdfContent = $this->container->get('renderPDF')("reports/tagihan.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
   }

    public function tagihanMahasiswaId($request, $response, $args)
   {
        $tagihan_obj = $this->get_object('tagihan');
        $mahasiswa_obj = $this->get_object('mahasiswa');

        $mahasiswa = $mahasiswa_obj->find($args['mahasiswa_id']);
        $tagihan = $tagihan_obj->where('orang_id', $mahasiswa->orang->id)->get();

        $value = [
          'mahasiswa' => $mahasiswa,
          'tagihan' => $tagihan
        ];

        if ($tagihan->count() < 1) {
            throw new \Exception("Belum ada tagihan");
        }

        $pdfContent = $this->container->get('renderPDF')("reports/tagihan.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
   }

    public function loginDetail($request, $response, $args)
   {
        $pmb_obj = $this->get_object('pmb');

        $pmb = $pmb_obj->find(6);

        $pmb->sendLoginDetail();
   }

}
