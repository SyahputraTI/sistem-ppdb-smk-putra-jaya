-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Okt 2026 pada 06.06
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ppdb_smkspp`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `doc_type` varchar(32) NOT NULL,
  `original_name` varchar(180) NOT NULL,
  `stored_name` varchar(180) NOT NULL,
  `mime` varchar(80) NOT NULL,
  `size_bytes` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `documents`
--

INSERT INTO `documents` (`id`, `registration_id`, `doc_type`, `original_name`, `stored_name`, `mime`, `size_bytes`, `created_at`) VALUES
(1, 1, 'foto', 'Cuplikan layar 2026-08-28 111345.png', 'foto-2a29d428766c.png', 'image/png', 10762, '2026-09-10 11:10:46'),
(2, 1, 'kk', 'Screenshot (3).png', 'kk-8131aa319676.png', 'image/png', 1276755, '2026-09-10 11:10:46'),
(3, 1, 'akta', 'Screenshot (1).png', 'akta-2f794a576634.png', 'image/png', 1282863, '2026-09-10 11:10:46'),
(4, 1, 'ijazah', 'Screenshot (4).png', 'ijazah-f8d613afee8e.png', 'image/png', 1240989, '2026-09-10 11:10:46'),
(5, 1, 'rapor', 'Screenshot (2).png', 'rapor-04fbd2d61a60.png', 'image/png', 1283296, '2026-09-10 11:10:46'),
(6, 1, 'ktp_ortu', 'Screenshot (5).png', 'ktp_ortu-231f57b997c6.png', 'image/png', 1283666, '2026-09-10 11:10:46'),
(7, 2, 'foto', 'Screenshot (5).png', 'foto-3810272a6610.png', 'image/png', 1283666, '2026-09-10 11:15:35'),
(8, 2, 'kk', 'Screenshot (2).png', 'kk-d7c718e932f5.png', 'image/png', 1283296, '2026-09-10 11:15:35'),
(9, 2, 'akta', 'Screenshot (4).png', 'akta-5a2820300ab4.png', 'image/png', 1240989, '2026-09-10 11:15:35'),
(10, 2, 'ijazah', 'Screenshot (1).png', 'ijazah-1619c7b96ecd.png', 'image/png', 1282863, '2026-09-10 11:15:35'),
(11, 2, 'rapor', 'Screenshot (3).png', 'rapor-53483749f553.png', 'image/png', 1276755, '2026-09-10 11:15:35'),
(12, 2, 'ktp_ortu', 'Cuplikan layar 2026-08-28 111345.png', 'ktp_ortu-7cc1a35445f9.png', 'image/png', 10762, '2026-09-10 11:15:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) DEFAULT NULL,
  `channel` varchar(20) NOT NULL,
  `target` varchar(40) NOT NULL,
  `title` varchar(160) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'queued',
  `provider_response` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `registration_id`, `channel`, `target`, `title`, `message`, `status`, `provider_response`, `created_at`, `sent_at`) VALUES
(1, 1, 'whatsapp', '6282274241080', 'Konfirmasi pendaftaran PJ20260001', 'Assalamu\'alaikum wr. wb.\n\nPendaftaran *SMKS-PP Putra Jaya Stabat* berhasil diterima.\n\nNomor pendaftaran: *PJ20260001*\nNama: M. Syahputra\nJurusan: Perkebunan\nStatus: Menunggu verifikasi\n\nSimpan nomor ini untuk cek status. Panitia akan meninjau berkas Anda.\nWassalamu\'alaikum wr. wb.', 'queued', 'WhatsApp API belum diaktifkan. Gunakan tautan wa.me.\nhttps://wa.me/6282274241080?text=Assalamu%27alaikum%20wr.%20wb.%0A%0APendaftaran%20%2ASMKS-PP%20Putra%20Jaya%20Stabat%2A%20berhasil%20diterima.%0A%0ANomor%20pendaftaran%3A%20%2APJ20260001%2A%0ANama%3A%20M.%20Syahputra%0AJurusan%3A%20Perkebunan%0AStatus%3A%20Menunggu%20verifikasi%0A%0ASimpan%20nomor%20ini%20untuk%20cek%20status.%20Panitia%20akan%20meninjau%20berkas%20Anda.%0AWassalamu%27alaikum%20wr.%20wb.', '2026-09-10 11:10:46', NULL),
(2, 1, 'whatsapp', '6282100000000', 'PPDB baru PJ20260001', 'PPDB baru masuk.\nNo: PJ20260001\nNama: M. Syahputra\nJurusan: Perkebunan\nWA: 082274241080', 'queued', 'WhatsApp API belum diaktifkan. Gunakan tautan wa.me.\nhttps://wa.me/6282100000000?text=PPDB%20baru%20masuk.%0ANo%3A%20PJ20260001%0ANama%3A%20M.%20Syahputra%0AJurusan%3A%20Perkebunan%0AWA%3A%20082274241080', '2026-09-10 11:10:47', NULL),
(3, 1, 'sistem', 'PJ20260001', 'Pendaftaran berhasil', 'Pendaftaran M. Syahputra tercatat dengan nomor PJ20260001. Status: Menunggu verifikasi.', 'sent', NULL, '2026-09-10 11:10:47', '2026-09-10 11:10:47'),
(4, 2, 'whatsapp', '6281326586713', 'Konfirmasi pendaftaran PJ20260002', 'Assalamu\'alaikum wr. wb.\n\nPendaftaran *SMKS-PP Putra Jaya Stabat* berhasil diterima.\n\nNomor pendaftaran: *PJ20260002*\nNama: Aisyah\nJurusan: Peternakan\nStatus: Menunggu verifikasi\n\nSimpan nomor ini untuk cek status. Panitia akan meninjau berkas Anda.\nWassalamu\'alaikum wr. wb.', 'queued', 'WhatsApp API belum diaktifkan. Gunakan tautan wa.me.\nhttps://wa.me/6281326586713?text=Assalamu%27alaikum%20wr.%20wb.%0A%0APendaftaran%20%2ASMKS-PP%20Putra%20Jaya%20Stabat%2A%20berhasil%20diterima.%0A%0ANomor%20pendaftaran%3A%20%2APJ20260002%2A%0ANama%3A%20Aisyah%0AJurusan%3A%20Peternakan%0AStatus%3A%20Menunggu%20verifikasi%0A%0ASimpan%20nomor%20ini%20untuk%20cek%20status.%20Panitia%20akan%20meninjau%20berkas%20Anda.%0AWassalamu%27alaikum%20wr.%20wb.', '2026-09-10 11:15:35', NULL),
(5, 2, 'whatsapp', '6282100000000', 'PPDB baru PJ20260002', 'PPDB baru masuk.\nNo: PJ20260002\nNama: Aisyah\nJurusan: Peternakan\nWA: 081326586713', 'queued', 'WhatsApp API belum diaktifkan. Gunakan tautan wa.me.\nhttps://wa.me/6282100000000?text=PPDB%20baru%20masuk.%0ANo%3A%20PJ20260002%0ANama%3A%20Aisyah%0AJurusan%3A%20Peternakan%0AWA%3A%20081326586713', '2026-09-10 11:15:35', NULL),
(6, 2, 'sistem', 'PJ20260002', 'Pendaftaran berhasil', 'Pendaftaran Aisyah tercatat dengan nomor PJ20260002. Status: Menunggu verifikasi.', 'sent', NULL, '2026-09-10 11:15:35', '2026-09-10 11:15:35'),
(7, 2, 'whatsapp', '6281326586713', 'Update status PJ20260002', 'Assalamu\'alaikum wr. wb.\n\nPembaruan status PPDB *SMKS-PP Putra Jaya Stabat*.\n\nNomor: *PJ20260002*\nNama: Aisyah\nStatus: *Diterima*\n\nWassalamu\'alaikum wr. wb.', 'queued', 'WhatsApp API belum diaktifkan. Gunakan tautan wa.me.\nhttps://wa.me/6281326586713?text=Assalamu%27alaikum%20wr.%20wb.%0A%0APembaruan%20status%20PPDB%20%2ASMKS-PP%20Putra%20Jaya%20Stabat%2A.%0A%0ANomor%3A%20%2APJ20260002%2A%0ANama%3A%20Aisyah%0AStatus%3A%20%2ADiterima%2A%0A%0AWassalamu%27alaikum%20wr.%20wb.', '2026-09-10 11:16:45', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `nomor` varchar(32) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `nama` varchar(120) NOT NULL,
  `nisn` varchar(10) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `tempat_lahir` varchar(80) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `agama` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `asal_sekolah` varchar(160) NOT NULL,
  `jurusan` varchar(160) NOT NULL,
  `nama_ayah` varchar(120) NOT NULL,
  `pekerjaan_ayah` varchar(80) NOT NULL,
  `nama_ibu` varchar(120) NOT NULL,
  `pekerjaan_ibu` varchar(80) NOT NULL,
  `no_hp_ortu` varchar(20) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'menunggu',
  `catatan_admin` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `registrations`
--

INSERT INTO `registrations` (`id`, `nomor`, `tahun_ajaran`, `nama`, `nisn`, `nik`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `agama`, `alamat`, `no_hp`, `email`, `asal_sekolah`, `jurusan`, `nama_ayah`, `pekerjaan_ayah`, `nama_ibu`, `pekerjaan_ibu`, `no_hp_ortu`, `status`, `catatan_admin`, `created_at`, `updated_at`) VALUES
(1, 'PJ20260001', '2026/2027', 'M. Syahputra', '1234567890', '1220672345714167', 'Seacnggag', '2004-11-30', 'L', 'Islam', 'Dsn JL. Masjid', '082274241080', 'syaputram288@gmail.com', 'SMP N 1 Secaggang', 'Perkebunan', 'M. Rasul', 'Nelayan', 'Nurmaiah', 'Irt', '082274241080', 'menunggu', NULL, '2026-09-10 11:10:46', '2026-09-10 11:10:46'),
(2, 'PJ20260002', '2026/2027', 'Aisyah', '1223478094', '1243568410801456', 'Tanjung Ibus', '2011-11-11', 'P', 'Islam', 'Dsn I Tanjung Ibus', '081326586713', 'aisyah341@gmail.com', 'SMP N 1 Hinai', 'Peternakan', 'Saiful', 'Petani', 'Nur Siti', 'Irt', '081326586713', 'diterima', '', '2026-09-10 11:15:35', '2026-09-10 11:16:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(64) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('info_singkat', 'SMKS-PP Putra Jaya Stabat membuka pendaftaran peserta didik baru. Lengkapi data, unggah berkas, lalu pantau status secara daring. Notifikasi juga dikirim ke WhatsApp.'),
('pendaftaran_buka', '1'),
('school_address', 'Stabat, Kabupaten Langkat, Sumatera Utara'),
('school_email', 'ppdb@smksppputrajayastabat.sch.id'),
('school_name', 'SMKS-PP Putra Jaya Stabat'),
('school_phone', '0852-1943-3787'),
('tahun_ajaran', '2026/2027'),
('wa_admin', '085219433787'),
('wa_enabled', '1'),
('wa_provider', 'fonnte'),
('wa_token', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `display_name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `display_name`) VALUES
(1, 'admin', '$2y$10$Ufam28rtWJYHdi877yAFY.D/Gd369wdDaDIqT9BcLtMOHTvd23EYq', 'Panitia PPDB');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_reg_type` (`registration_id`,`doc_type`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `fk_notif_reg` (`registration_id`);

--
-- Indeks untuk tabel `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor` (`nomor`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_jurusan` (`jurusan`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `fk_doc_reg` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_reg` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
