// liblary
import 'angularjs-datepicker';
import 'angular-base64-upload';

import './app.module';
import './blocks/exception/exception.module';
import './blocks/logger/logger.module';
import './blocks/router/router.module';
import './core/core.module';
import './widgets/widgets.module';
import './modals/modals.module';
import './layout/layout.module';
import './agama/agama.module';
import './pmb/pmb.module';
import './nilai/nilai.module';
import './mata_kuliah/mata_kuliah.module';
import './sequance/sequance.module';
import './tahun_ajaran/tahun_ajaran.module';
import './penerbitan_nim/penerbitan_nim.module';
import './mahasiswa/mahasiswa.module';
import './alumni/alumni.module';
import './karyawan/karyawan.module';
import './dosen_pa/dosen_pa.module';
import './dosen_pjmk/dosen_pjmk.module';
import './paket/paket.module';
import './kwitansi/kwitansi.module';
import './tagihan/tagihan.module';
import './pembiayaan/pembiayaan.module';
import './setup_paket/setup_paket.module';
import './konfigurasi/konfigurasi.module';
import './ganti_semester/ganti_semester.module';
import './pendaftaran/pendaftaran.module';
import './mahasiswa_baru/mahasiswa_baru.module';
import './panitia/panitia.module';
import './pembiayaan_lainnya/pembiayaan_lainnya.module';
import './tracking/tracking.module';
import './verifikasi_pmb/verifikasi_pmb.module';
import './role/role.module';

import './blocks/exception/exception-handler.provider';
import './blocks/exception/exception';
import './blocks/logger/logger';
import './blocks/router/router-helper.provider';

import './core/config';
import './core/constants';
import './core/core.route';
import './core/dataservice';

import './layout/ht-sidebar.directive';
import './layout/shell.controller';
import './layout/sidebar.controller';

import './widgets/widget-pagination.directive';
import './widgets/widget-submit.directive';
import './widgets/widget-back.directive';
import './widgets/widget-tr.directive';
import './widgets/widget-preview.directive';
import './widgets/widget-update-status.directive';
import './widgets/widget-cetak-peserta.directive';
import './widgets/widget-cetak-transkrip.directive';
import './widgets/widget-cetak-krs.directive';
import './widgets/widget-cetak-khs.directive';
import './widgets/widget-autocomplete.directive';
import './widgets/widget-next-prev.directive';
import './widgets/widget-date-picker.directive';
import './widgets/widget-delete.directive';
import './widgets/widget-number.directive';
import './widgets/widget-input-number.directive';

import './modals/modal-preview.directive';
import './modals/modal-mata-kuliah.directive';
import './modals/modal-konfigurasi-nilai.directive';
import './modals/modal-tambah-mahasiswa.directive';
import './modals/modal-pengajuan-krs.directive';
import './modals/modal-proses-pengajuan.directive';
import './modals/modal-tolak-pengajuan.directive';
import './modals/modal-isi-nilai.directive';
import './modals/modal-tambah-pembiayaan.directive';
import './modals/modal-pembiayaan-tagihan.directive';
import './modals/modal-atur-paket.directive';
import './modals/modal-buat-tagihan.directive';
import './modals/modal-tambah-paket.directive';
import './modals/modal-tagihan-semester.directive';
import './modals/modal-bayar-tagihan.directive';
import './modals/modal-detail-khs.directive';
import './modals/modal-lihat-khs.directive';
import './modals/modal-lihat-krs.directive';
import './modals/modal-input-kode.directive';
import './modals/modal-tagihan-ta.directive';
import './modals/modal-pilih-mahasiswa.directive';
import './modals/modal-isi-tanggal.directive';
import './modals/modal-migrasi-mahasiswa.directive';
import './modals/modal-migrasi-matkul.directive';
import './modals/modal-ganti-semester.directive';

import './agama/agama.controller';
import './agama/agama_form.controller';
import './agama/agama_detail.controller';
import './agama/agama.route';

import './pmb/pmb.controller';
import './pmb/pmb_form.controller';
import './pmb/pmb_detail.controller';
import './pmb/pmb.route';

import './nilai/nilai.controller';
import './nilai/nilai_form.controller';
import './nilai/nilai_detail.controller';
import './nilai/nilai.route';

import './tahun_ajaran/tahun_ajaran.controller';
import './tahun_ajaran/tahun_ajaran_form.controller';
import './tahun_ajaran/tahun_ajaran_detail.controller';
import './tahun_ajaran/tahun_ajaran.route';

import './sequance/sequance.controller';
import './sequance/sequance_form.controller';
import './sequance/sequance_detail.controller';
import './sequance/sequance.route';

import './mata_kuliah/mata_kuliah.controller';
import './mata_kuliah/mata_kuliah_form.controller';
import './mata_kuliah/mata_kuliah_detail.controller';
import './mata_kuliah/mata_kuliah.route';

import './mahasiswa/mahasiswa.controller';
import './mahasiswa/mahasiswa_form.controller';
import './mahasiswa/mahasiswa_detail.controller';
import './mahasiswa/mahasiswa_riwayat_belajar.controller';
import './mahasiswa/mahasiswa_krs.controller';
import './mahasiswa/mahasiswa_khs.controller';
import './mahasiswa/mahasiswa_tagihan.controller';
import './mahasiswa/mahasiswa_detail_tagihan.controller';
import './mahasiswa/mahasiswa.route';

import './alumni/alumni.controller';
import './alumni/alumni.route';

import './karyawan/karyawan.controller';
import './karyawan/karyawan_form.controller';
import './karyawan/karyawan_detail.controller';
import './karyawan/karyawan.route';

import './dosen_pa/dosen_pa.controller';
import './dosen_pa/dosen_pa_form.controller';
import './dosen_pa/dosen_pa_detail.controller';
import './dosen_pa/dosen_pa_bimbingan.controller';
import './dosen_pa/dosen_pa.route';

import './dosen_pjmk/dosen_pjmk.controller';
import './dosen_pjmk/dosen_pjmk_form.controller';
import './dosen_pjmk/dosen_pjmk_detail.controller';
import './dosen_pjmk/dosen_pjmk_mata_kuliah.controller';
import './dosen_pjmk/dosen_pjmk.route';

import './penerbitan_nim/penerbitan_nim.controller';
import './penerbitan_nim/penerbitan_nim.route';

import './paket/paket.controller';
import './paket/paket_form.controller';
import './paket/paket_detail.controller';
import './paket/paket.route';

import './kwitansi/kwitansi.controller';
import './kwitansi/kwitansi_form.controller';
import './kwitansi/kwitansi_detail.controller';
import './kwitansi/kwitansi.route';

import './tagihan/tagihan.controller';
import './tagihan/tagihan_form.controller';
import './tagihan/tagihan_detail.controller';
import './tagihan/tagihan.route';

import './pembiayaan/pembiayaan.controller';
import './pembiayaan/pembiayaan_form.controller';
import './pembiayaan/pembiayaan_detail.controller';
import './pembiayaan/pembiayaan.route';

import './setup_paket/setup_paket.controller';
import './setup_paket/setup_paket_form.controller';
import './setup_paket/setup_paket_detail.controller';
import './setup_paket/setup_paket_tagihan_detail.controller';
import './setup_paket/setup_paket.route';

import './konfigurasi/konfigurasi_detail.controller';
import './konfigurasi/konfigurasi_form.controller';
import './konfigurasi/konfigurasi.route';

import './ganti_semester/ganti_semester.controller';
import './ganti_semester/ganti_semester_mahasiswa.controller';
import './ganti_semester/ganti_semester.route';

import './pendaftaran/pendaftaran.controller';
import './pendaftaran/pendaftaran_form.controller';
import './pendaftaran/pendaftaran_detail.controller';
import './pendaftaran/pendaftaran.route';

import './mahasiswa_baru/mahasiswa_baru_detail.controller';
import './mahasiswa_baru/mahasiswa_baru.route';

import './panitia/panitia.controller';
import './panitia/panitia_form.controller';
import './panitia/panitia_detail.controller';
import './panitia/panitia.route';

import './pembiayaan_lainnya/pembiayaan_lainnya.controller';
import './pembiayaan_lainnya/pembiayaan_lainnya_form.controller';
import './pembiayaan_lainnya/pembiayaan_lainnya_detail.controller';
import './pembiayaan_lainnya/pembiayaan_lainnya.route';

import './tracking/tracking.controller';
import './tracking/tracking.route';

import './verifikasi_pmb/verifikasi_pmb.controller';
import './verifikasi_pmb/verifikasi_pmb_detail.controller';
import './verifikasi_pmb/verifikasi_pmb_form.controller';
import './verifikasi_pmb/verifikasi_pmb.route';

import './role/role.controller';
import './role/role_form.controller';
import './role/role_detail.controller';
import './role/role.route';