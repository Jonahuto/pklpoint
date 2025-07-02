-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2025 at 10:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pklpoint`
--

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id_kegiatan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `minggu` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `status` varchar(50) DEFAULT 'Belum Dicek'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id_kegiatan`, `id_user`, `nama_kegiatan`, `minggu`, `file`, `tanggal`, `status`) VALUES
(5, 27, 'asd', 3, 'kegiatan_685cc2b49698c5.11860021.png', '2025-06-26', 'Belum Dicek');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_akhir`
--

CREATE TABLE `laporan_akhir` (
  `id_kegiatan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul_laporan_akhir` varchar(255) NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `komentar` text DEFAULT NULL,
  `tanggal_komentar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_akhir`
--

INSERT INTO `laporan_akhir` (`id_kegiatan`, `id_user`, `judul_laporan_akhir`, `file`, `tanggal`, `komentar`, `tanggal_komentar`) VALUES
(2, 11, 'aaa', 'Screenshot 2025-01-15 124549.png', '2025-06-26', 'abb', '2025-06-26 10:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `status` enum('Aktif','Tidak Aktif') DEFAULT 'Tidak Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_id`, `waktu_masuk`, `waktu_keluar`, `status`) VALUES
(1, 11, '2025-06-26 09:49:30', '2025-06-26 10:01:40', 'Tidak Aktif'),
(2, 27, '2025-06-26 05:11:21', '2025-06-26 05:47:28', 'Tidak Aktif'),
(3, 15, '2025-06-26 10:01:44', '2025-06-26 10:09:34', 'Tidak Aktif'),
(4, 28, '2025-06-26 10:09:56', '2025-06-26 10:14:02', 'Tidak Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `pkl_data`
--

CREATE TABLE `pkl_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `prodi` varchar(100) DEFAULT NULL,
  `semester` varchar(10) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_perusahaan` varchar(100) DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `no_telp_perusahaan` varchar(20) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pembimbing` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pkl_data`
--

INSERT INTO `pkl_data` (`id`, `user_id`, `nama_lengkap`, `nim`, `prodi`, `semester`, `no_telp`, `email`, `nama_perusahaan`, `alamat_lengkap`, `no_telp_perusahaan`, `tanggal_mulai`, `tanggal_selesai`, `created_at`, `updated_at`, `pembimbing`) VALUES
(7, 11, 'Ageng', '123', 'Informatika', '4', '2123', 'aass@gmail.com', 'sada', 'asf', '312324', '2025-07-03', '2025-07-20', '2025-06-25 15:16:34', '2025-06-26 07:42:49', 15),
(9, 27, 'Leilaro', '345678', 'Informatika', '3', '21231', 'leiro@gmail.com', 'adfsf', 'afsfs', '3132', '2025-06-29', '2025-08-03', '2025-06-26 03:46:51', '2025-06-26 05:46:27', 15);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `prodi` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','dosen','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `nama_lengkap`, `nim`, `email`, `prodi`, `no_telp`, `semester`, `password`, `role`, `created_at`, `updated_at`) VALUES
(10, 'Aang', '12345', '123123', NULL, NULL, NULL, '$2y$10$EQExw4IPGjzSHfBF.PmtJOUaffWCi0Adx9EPBeoka5fpD3IUDVozm', 'mahasiswa', '2025-06-18 11:20:08', '2025-06-26 08:17:45'),
(11, 'Ageng', '123', 'aass@gmail.com', 'Sistem Informasi', '000000002', 8, '$2y$10$XoxBOoQZlMD93KKOcm0kquDi9iIPypaiD5WMKLb1TJD/xRqoi.G3O', 'mahasiswa', '2025-06-18 11:21:23', '2025-06-26 08:33:39'),
(14, 'rgsd', '32155', 'oiuy@gmail.com', NULL, '00000', NULL, '$2y$10$.scLgupQq7JB5ICyQkvHp.NaXkcvsmHVcOitJhJ8I1lyGngJNMLWq', 'dosen', '2025-06-18 11:24:54', '2025-06-26 08:17:45'),
(15, 'Jonathan Ruliano', '19221442', 'jonathan66ruliano@gmail.com', NULL, '123', 2, '$2y$10$CpTVyQn8b1rAkT2Sgubki.i0N8z6vW6KfsUSwvCq8SsC2wzdxIsDG', 'dosen', '2025-06-23 12:58:11', '2025-06-26 12:03:39'),
(17, 'Fatra Nadia', '19220235', '19220235@bsi.ac.id', NULL, '33333', 3, '$2y$10$6z/FW11rqbKCeqV1VnrHpONRlOEQu0QxDUTa9anFn7UBjXm1oXS2G', 'mahasiswa', '2025-06-25 06:39:23', '2025-06-26 08:17:45'),
(26, 'Paimerito', '098765', 'paimer@gmail.com', NULL, NULL, NULL, '$2y$10$ONDUFilas9BWuZJ2l.IixeXOgk8/2ec66zFr.xUAisV0mWnpu035e', 'mahasiswa', '2025-06-26 03:08:27', '2025-06-26 10:08:27'),
(27, 'Leilaro', '345678', 'leiro@gmail.com', 'Sistem Informasi', '313212', NULL, '$2y$10$HR29sxZZvvfJjzNGnHjjzOO.HuzyJet1k26LNkXqdBdolCwOSxkyG', 'mahasiswa', '2025-06-26 03:11:16', '2025-06-26 10:47:22'),
(28, 'Arang Gilein', '000', '123@gmail.com', NULL, NULL, NULL, '$2y$10$kPo0DZ7z1aFd2GizZEz8ceUGBZjCIyl/Jkp9uknmGPtkG9jw7vf2e', 'mahasiswa', '2025-06-26 08:09:53', '2025-06-26 15:11:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id_kegiatan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  ADD PRIMARY KEY (`id_kegiatan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pkl_data`
--
ALTER TABLE `pkl_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pkl_data`
--
ALTER TABLE `pkl_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  ADD CONSTRAINT `laporan_akhir_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `pkl_data`
--
ALTER TABLE `pkl_data`
  ADD CONSTRAINT `pkl_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
