CREATE TABLE `agama` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `orang` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nik` bigint,
  `nama` varchar(255),
  `tempat_lahir` varchar(255),
  `tanggal_lahir` date,
  `jenis_kelamin` ENUM ('l', 'p'),
  `alamat` varchar(255),
  `agama_id` int,
  `email` varchar(255),
  `no_hp` varchar(255),
  `nama_ayah` varchar(255),
  `pekerjaan_ayah` varchar(255),
  `nama_ibu` varchar(255),
  `pekerjaan_ibu` varchar(255),
  `penghasilan_ortu` int,
  `asal_sekolah` varchar(255),
  `jurusan` varchar(255),
  `tinggi_badan` int,
  `berat_badan` int,
  `pasfoto_id` int,
  `ijazah_id` int,
  `ktp_id` int,
  `surket_menikah_id` int,
  `pendidikan_terakhir` varchar(255),
  `status` ENUM ('aktif', 'inaktif', 'berhenti') DEFAULT "aktif"
);

CREATE TABLE `tahun_ajaran` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `tahun` int,
  `nomor` int DEFAULT 1,
  `paket_id` int,
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `semester` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `tipe` ENUM ('pendek', 'genap', 'ganjil')
);

CREATE TABLE `mahasiswa` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nim` varchar(255),
  `orang_id` int,
  `jurusan_id` int,
  `semester_id` int,
  `tahun_ajaran_id` int,
  `kode_validasi` boolean DEFAULT false,
  `reg_ulang` boolean DEFAULT false,
  `ajukan_sks` boolean DEFAULT true,
  `pengajuan` boolean DEFAULT false,
  `sudah_pengajuan` boolean DEFAULT false,
  `tahun_masuk` int,
  `tagihan_id` int,
  `status` ENUM ('mahasiswa', 'alumni', 'dropout') DEFAULT "mahasiswa"
);

CREATE TABLE `karyawan` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `orang_id` int,
  `ni` varchar(255),
  `jenis_karyawan` ENUM ('dosen', 'pegawai', 'akademik', 'keuangan', 'panitia'),
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `user` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `orang_id` int,
  `username` varchar(255),
  `password` varchar(255),
  `unenpass` varchar(255),
  `role` ENUM ('mahasiswa', 'dosen', 'keuangan', 'akademik', 'admin', 'panitia') DEFAULT "mahasiswa"
);

CREATE TABLE `pengajuan_ks` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `mahasiswa_id` int,
  `tahun_ajaran_id` int,
  `semester_id` int,
  `alasan` varchar(255),
  `status` ENUM ('proses', 'terima', 'tolak') DEFAULT "proses"
);

CREATE TABLE `pengajuan_ks_detail` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `pengajuan_ks_id` int,
  `mata_kuliah_id` int,
  `pesan` varchar(255),
  `status` ENUM ('terima', 'tolak') DEFAULT "terima"
);

CREATE TABLE `nilai` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255)
);

CREATE TABLE `khs` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `mahasiswa_id` int,
  `semester_id` int,
  `total_sks` int,
  `ips` float
);

CREATE TABLE `khs_detail` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `khs_id` int,
  `mata_kuliah_id` int,
  `nilai_absolut` int,
  `nilai_mutu` int,
  `nilai_bobot` varchar(255),
  `riwayat_belajar_detail_id` int
);

CREATE TABLE `riwayat_belajar` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `mahasiswa_id` int,
  `semester_id` int,
  `total_sks` int,
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `riwayat_belajar_detail` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `riwayat_belajar_id` int,
  `mata_kuliah_id` int,
  `nilai_absolut` int,
  `nilai_bobot` varchar(255),
  `nilai_mutu` int
);

CREATE TABLE `riwayat_belajar_nilai` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `riwayat_belajar_detail_id` int,
  `nilai_id` int,
  `nilai` float
);

CREATE TABLE `penerbitan_nim` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `pmb_id` int,
  `tahun` int,
  `mahasiswa_id` int,
  `status` ENUM ('terbit', 'pengajuan', 'belum') DEFAULT "belum"
);

CREATE TABLE `dosen_pa` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `karyawan_id` int,
  `tahun_ajaran_id` int,
  `semester_id` int,
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `mahasiswa_bimbingan` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `mahasiswa_id` int,
  `dosen_pa_id` int
);

CREATE TABLE `mata_kuliah` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `kode` varchar(255) UNIQUE,
  `nama` varchar(255),
  `nama_inggris` varchar(255),
  `sks` int,
  `t` boolean,
  `p` boolean,
  `k` boolean,
  `jurusan_id` int,
  `semester_id` int,
  `keterangan` text
);

CREATE TABLE `dosen_pjmk` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `karyawan_id` int,
  `tahun_ajaran_id` int,
  `semester_id` int,
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `mata_kuliah_diampuh` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `dosen_pjmk_id` int,
  `mata_kuliah_id` int,
  `terkonfigurasi` boolean DEFAULT false
);

CREATE TABLE `konfigurasi_nilai` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `mata_kuliah_diampuh_id` int,
  `nilai_id` int,
  `persentase` float,
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `item` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `kode` varchar(255),
  `nominal` float,
  `paket_id` int
);

CREATE TABLE `paket` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `kode` varchar(255),
  `nominal` float,
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `tagihan` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `kode` varchar(255),
  `tanggal` date,
  `nominal` float,
  `orang_id` int,
  `system` boolean,
  `kode_pembayaran` varchar(255),
  `register_ulang` boolean,
  `paket_register_ulang_id` int,
  `status` ENUM ('draft', 'proses', 'bayar') DEFAULT "draft"
);

CREATE TABLE `tagihan_item` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `tagihan_id` int,
  `nama` varchar(255),
  `kode` varchar(255),
  `nominal` float
);

CREATE TABLE `tagihan_bukti_bayar` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `tagihan_id` int,
  `file_id` int
);

CREATE TABLE `kwitansi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `kode` varchar(255),
  `tanggal` date,
  `paket_id` int,
  `orang_id` int,
  `nominal` float,
  `status` ENUM ('draft', 'terverifikasi') DEFAULT "draft"
);

CREATE TABLE `paket_register_ulang` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `semester_id` int,
  `nominal` float
);

CREATE TABLE `paket_register_ulang_item` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `paket_register_ulang_id` int,
  `item_id` int
);

CREATE TABLE `pmb` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nomor_peserta` varchar(255),
  `orang_id` int,
  `jurusan_id` int,
  `status` ENUM ('terima', 'tolak', 'ujian', 'terverifikasi', 'baru') DEFAULT "baru",
  `bukti_pembayaran_id` int,
  `pernyataan` boolean,
  `tanggal_pendaftaran` date DEFAULT (now())
);

CREATE TABLE `jurusan` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `status` ENUM ('aktif', 'nonaktif') DEFAULT "aktif"
);

CREATE TABLE `register_ulang` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `mahasiswa_id` int,
  `semester` int,
  `bukti` varchar(255),
  `kode_register` varchar(255),
  `status` ENUM ('proses', 'terverifikasi', 'tolak') DEFAULT "proses"
);

CREATE TABLE `template_email` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `model` varchar(255),
  `template` text
);

CREATE TABLE `email_keluar` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `dari` varchar(255),
  `ke` varchar(255),
  `isi` text,
  `smtp_server_id` int
);

CREATE TABLE `smtp_server` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `email` varchar(255),
  `server` varchar(255),
  `port` varchar(255),
  `username` varchar(255),
  `password` varchar(255)
);

CREATE TABLE `template_laporan` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `model` varchar(255),
  `template` text
);

CREATE TABLE `navigation` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `parent` int,
  `nama` varchar(255),
  `link` varchar(255)
);

CREATE TABLE `sequance` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(255),
  `kode` varchar(255),
  `nomor` int DEFAULT 1
);

CREATE TABLE `file` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `filename` varchar(255),
  `filetype` varchar(255),
  `base64` longtext
);

CREATE TABLE `konfigurasi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `kode_perusahaan` varchar(255),
  `semester_id` int,
  `tahun_ajaran_id` int
);

ALTER TABLE `mahasiswa` ADD FOREIGN KEY (`orang_id`) REFERENCES `orang` (`id`);

ALTER TABLE `karyawan` ADD FOREIGN KEY (`orang_id`) REFERENCES `orang` (`id`);

ALTER TABLE `user` ADD FOREIGN KEY (`orang_id`) REFERENCES `orang` (`id`);

ALTER TABLE `orang` ADD FOREIGN KEY (`agama_id`) REFERENCES `agama` (`id`);

ALTER TABLE `orang` ADD FOREIGN KEY (`ijazah_id`) REFERENCES `file` (`id`);

ALTER TABLE `orang` ADD FOREIGN KEY (`ktp_id`) REFERENCES `file` (`id`);

ALTER TABLE `orang` ADD FOREIGN KEY (`surket_menikah_id`) REFERENCES `file` (`id`);

ALTER TABLE `tahun_ajaran` ADD FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`);

ALTER TABLE `mahasiswa` ADD FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`);

ALTER TABLE `mahasiswa` ADD FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`);

ALTER TABLE `mahasiswa` ADD FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`);

ALTER TABLE `mahasiswa` ADD FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`);

ALTER TABLE `pengajuan_ks` ADD FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`);

ALTER TABLE `pengajuan_ks` ADD FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`);

ALTER TABLE `pengajuan_ks` ADD FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`);

ALTER TABLE `pengajuan_ks_detail` ADD FOREIGN KEY (`pengajuan_ks_id`) REFERENCES `pengajuan_ks` (`id`);

ALTER TABLE `pengajuan_ks_detail` ADD FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`);

ALTER TABLE `khs` ADD FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`);

ALTER TABLE `khs` ADD FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`);

ALTER TABLE `khs_detail` ADD FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`);

ALTER TABLE `khs_detail` ADD FOREIGN KEY (`riwayat_belajar_detail_id`) REFERENCES `riwayat_belajar_detail` (`id`);

ALTER TABLE `riwayat_belajar` ADD FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`);

ALTER TABLE `riwayat_belajar` ADD FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`);

ALTER TABLE `riwayat_belajar_detail` ADD FOREIGN KEY (`riwayat_belajar_id`) REFERENCES `riwayat_belajar` (`id`);

ALTER TABLE `riwayat_belajar_detail` ADD FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`);

ALTER TABLE `riwayat_belajar_nilai` ADD FOREIGN KEY (`riwayat_belajar_detail_id`) REFERENCES `riwayat_belajar_detail` (`id`);

ALTER TABLE `riwayat_belajar_nilai` ADD FOREIGN KEY (`nilai_id`) REFERENCES `nilai` (`id`);

ALTER TABLE `penerbitan_nim` ADD FOREIGN KEY (`pmb_id`) REFERENCES `pmb` (`id`);

ALTER TABLE `dosen_pa` ADD FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`);

ALTER TABLE `dosen_pa` ADD FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`);

ALTER TABLE `dosen_pa` ADD FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`);

ALTER TABLE `mahasiswa_bimbingan` ADD FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`);

ALTER TABLE `mahasiswa_bimbingan` ADD FOREIGN KEY (`dosen_pa_id`) REFERENCES `dosen_pa` (`id`);

ALTER TABLE `mata_kuliah` ADD FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`);

ALTER TABLE `dosen_pjmk` ADD FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`);

ALTER TABLE `dosen_pjmk` ADD FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`);

ALTER TABLE `dosen_pjmk` ADD FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`);

ALTER TABLE `mata_kuliah_diampuh` ADD FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`);

ALTER TABLE `mata_kuliah_diampuh` ADD FOREIGN KEY (`dosen_pjmk_id`) REFERENCES `dosen_pjmk` (`id`);

ALTER TABLE `konfigurasi_nilai` ADD FOREIGN KEY (`nilai_id`) REFERENCES `nilai` (`id`);

ALTER TABLE `konfigurasi_nilai` ADD FOREIGN KEY (`mata_kuliah_diampuh_id`) REFERENCES `mata_kuliah_diampuh` (`id`);

ALTER TABLE `item` ADD FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`);

ALTER TABLE `tagihan` ADD FOREIGN KEY (`orang_id`) REFERENCES `orang` (`id`);

ALTER TABLE `tagihan` ADD FOREIGN KEY (`paket_register_ulang_id`) REFERENCES `paket_register_ulang` (`id`);

ALTER TABLE `tagihan_item` ADD FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`);

ALTER TABLE `tagihan_bukti_bayar` ADD FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`);

ALTER TABLE `tagihan_bukti_bayar` ADD FOREIGN KEY (`file_id`) REFERENCES `file` (`id`);

ALTER TABLE `kwitansi` ADD FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`);

ALTER TABLE `kwitansi` ADD FOREIGN KEY (`orang_id`) REFERENCES `orang` (`id`);

ALTER TABLE `paket_register_ulang` ADD FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`);

ALTER TABLE `paket_register_ulang_item` ADD FOREIGN KEY (`paket_register_ulang_id`) REFERENCES `paket_register_ulang` (`id`);

ALTER TABLE `paket_register_ulang_item` ADD FOREIGN KEY (`item_id`) REFERENCES `item` (`id`);

ALTER TABLE `pmb` ADD FOREIGN KEY (`orang_id`) REFERENCES `orang` (`id`);

ALTER TABLE `pmb` ADD FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`);

ALTER TABLE `register_ulang` ADD FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`);

ALTER TABLE `email_keluar` ADD FOREIGN KEY (`smtp_server_id`) REFERENCES `smtp_server` (`id`);

ALTER TABLE `navigation` ADD FOREIGN KEY (`parent`) REFERENCES `navigation` (`id`);

ALTER TABLE `konfigurasi` ADD FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`);

ALTER TABLE `konfigurasi` ADD FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`);
