<?php

namespace App\Controllers;

use App\Controllers\BaseController;

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
        $mahasiswa = $mahasiswa_obj->find($mahasiswa_id);
        $value = ['mahasiswa' => $mahasiswa];

        $pdfContent = $this->container->get('renderPDF')("reports/transkrip.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
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

        $mahasiswa = $mahasiswa_obj->where('semester_id', $mata_kuliah_diampuh->dosen_pjmk->semester_id)->whereHas(
            'pengajuan_ks', 
            function($q) use ($mata_kuliah_diampuh) {
                $q
                    ->where('status', 'terima')
                    ->where('semester_id', $mata_kuliah_diampuh->dosen_pjmk->semester_id)
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

        $mahasiswa = $mahasiswa_obj->where('semester_id', $mata_kuliah_diampuh->dosen_pjmk->semester_id)->whereHas(
            'pengajuan_ks', 
            function($q) use ($mata_kuliah_diampuh) {
                $q
                    ->where('status', 'terima')
                    ->where('semester_id', $mata_kuliah_diampuh->dosen_pjmk->semester_id)
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

}
