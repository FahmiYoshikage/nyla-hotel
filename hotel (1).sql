-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 09:07 AM
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
-- Database: `hotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkin_checkout`
--

CREATE TABLE `checkin_checkout` (
  `ID_CHECKIN_OUT` int(11) NOT NULL,
  `ID_PEMESANAN` int(11) DEFAULT NULL,
  `ID_PELANGGAN` int(11) DEFAULT NULL,
  `ID_KAMAR` int(11) DEFAULT NULL,
  `ID_JENIS_KAMAR` int(11) DEFAULT NULL,
  `TGL_CHECK_IN` date DEFAULT NULL,
  `JAM_CHECK_IN` time DEFAULT NULL,
  `LAMA_INAP` varchar(255) DEFAULT NULL,
  `TGL_CHECK_OUT` date DEFAULT NULL,
  `STATUS_PEMBAYARAN` varchar(125) NOT NULL,
  `STATUS_CHECKINOUT` varchar(125) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkin_checkout`
--

INSERT INTO `checkin_checkout` (`ID_CHECKIN_OUT`, `ID_PEMESANAN`, `ID_PELANGGAN`, `ID_KAMAR`, `ID_JENIS_KAMAR`, `TGL_CHECK_IN`, `JAM_CHECK_IN`, `LAMA_INAP`, `TGL_CHECK_OUT`, `STATUS_PEMBAYARAN`, `STATUS_CHECKINOUT`) VALUES
(4, 1, 1, 1, 1, '2025-11-10', '15:19:00', '3', '2025-11-10', '', ''),
(10, 1, 1, 1, 1, '2025-11-10', '13:50:00', '3', '2025-11-10', '', ''),
(11, 33, 37, 1, 1, '2025-12-29', '00:00:14', '4', '2026-01-02', 'Belum Lunas', 'Sudah Checkout'),
(12, 33, 37, 1, 1, '2025-12-29', '00:00:00', '4', '2026-01-02', 'Lunas', 'Sudah Checkout');

-- --------------------------------------------------------

--
-- Table structure for table `detail_food_n_beverage`
--

CREATE TABLE `detail_food_n_beverage` (
  `ID_DETAIL_FNB` int(11) NOT NULL,
  `ID_PELANGGAN` int(11) DEFAULT NULL,
  `ID_FNB` int(11) DEFAULT NULL,
  `TGL_FNB` date DEFAULT NULL,
  `JAM_FNB` time DEFAULT NULL,
  `FOOD` varchar(255) DEFAULT NULL,
  `BEVERAGE` varchar(255) DEFAULT NULL,
  `JUMLAH_FNB` int(11) DEFAULT NULL,
  `SUBTOTAL_FNB` int(11) DEFAULT NULL,
  `TOTAL_FNB` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_food_n_beverage`
--

INSERT INTO `detail_food_n_beverage` (`ID_DETAIL_FNB`, `ID_PELANGGAN`, `ID_FNB`, `TGL_FNB`, `JAM_FNB`, `FOOD`, `BEVERAGE`, `JUMLAH_FNB`, `SUBTOTAL_FNB`, `TOTAL_FNB`) VALUES
(1, 37, 10, '2025-12-02', '08:16:00', '0', '0', 18, 88, 1584);

-- --------------------------------------------------------

--
-- Table structure for table `detail_laundry`
--

CREATE TABLE `detail_laundry` (
  `ID_DETAIL_LAUNDRY` int(11) NOT NULL,
  `ID_PELANGGAN` int(11) DEFAULT NULL,
  `ID_LAUNDRY` int(11) DEFAULT NULL,
  `TGL_LAUNDRY` date DEFAULT NULL,
  `JENIS_LAUNDRY` varchar(255) DEFAULT NULL,
  `JAM_LAUNDRY` time DEFAULT NULL,
  `HARGA_SATUAN` int(11) DEFAULT NULL,
  `JUMLAH_LAUNDRY` int(11) DEFAULT NULL,
  `TOTAL_LAUNDRY` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_laundry`
--

INSERT INTO `detail_laundry` (`ID_DETAIL_LAUNDRY`, `ID_PELANGGAN`, `ID_LAUNDRY`, `TGL_LAUNDRY`, `JENIS_LAUNDRY`, `JAM_LAUNDRY`, `HARGA_SATUAN`, `JUMLAH_LAUNDRY`, `TOTAL_LAUNDRY`) VALUES
(1, 1, 2, '2025-11-01', 'qq', '02:20:31', 11, 11, 11),
(2, 37, 4, '2025-12-02', '0', '09:27:00', 333, 13, 4329),
(3, 1, 2, '2025-12-02', '0', '05:28:00', 222, 12, 2664),
(4, 1, 2, '2025-12-02', '2', '05:29:00', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_pelanggan`
--

CREATE TABLE `feedback_pelanggan` (
  `ID_FEEDBACK` int(11) NOT NULL,
  `ID_PELANGGAN` int(11) NOT NULL,
  `ID_CHECKIN_OUT` int(100) NOT NULL,
  `RATING` int(11) NOT NULL,
  `KATEGORI_FEEDBACK` enum('pujian','saran','keluhan','') NOT NULL,
  `PESAN` text NOT NULL,
  `FOTO_FEEDBACK` varchar(255) NOT NULL,
  `TGL_FEEDBACK` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_pelanggan`
--

INSERT INTO `feedback_pelanggan` (`ID_FEEDBACK`, `ID_PELANGGAN`, `ID_CHECKIN_OUT`, `RATING`, `KATEGORI_FEEDBACK`, `PESAN`, `FOTO_FEEDBACK`, `TGL_FEEDBACK`) VALUES
(3, 37, 12, 5, 'pujian', 'wedfgh', 'feedback_1764746208_692fe3e09d592.jpg', '0000-00-00 00:00:00'),
(7, 37, 12, 5, 'saran', 'bnm,', 'feedback_1764747580_692fe93c2df72.png', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `food_n_beverage`
--

CREATE TABLE `food_n_beverage` (
  `ID_FNB` int(11) NOT NULL,
  `ID_PELANGGAN` int(11) DEFAULT NULL,
  `ID_KAMAR` int(11) DEFAULT NULL,
  `KODE_FNB` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_n_beverage`
--

INSERT INTO `food_n_beverage` (`ID_FNB`, `ID_PELANGGAN`, `ID_KAMAR`, `KODE_FNB`) VALUES
(2, 1, 1, 'FNB002'),
(10, 37, 1, 'FNB003'),
(11, 37, 1, 'FNB004');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_kamar`
--

CREATE TABLE `jenis_kamar` (
  `ID_JENIS_KAMAR` int(11) NOT NULL,
  `JENIS_KAMAR` varchar(255) DEFAULT NULL,
  `FASILITAS` varchar(255) DEFAULT NULL,
  `TARIF` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_kamar`
--

INSERT INTO `jenis_kamar` (`ID_JENIS_KAMAR`, `JENIS_KAMAR`, `FASILITAS`, `TARIF`) VALUES
(1, 'delux', 'lengkap', 25000),
(2, 'Standard Room', 'AC, TV', 500000),
(3, 'Deluxe King', 'Lengkap, Bathtub', 850000),
(4, 'Family Suite', 'Lengkap, Dapur Kecil', 1500000),
(5, 'Executive Suite', 'Lengkap, Ruang Tamu', 2200000);

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `ID_KAMAR` int(11) NOT NULL,
  `ID_JENIS_KAMAR` int(11) DEFAULT NULL,
  `NO_KAMAR` varchar(255) DEFAULT NULL,
  `STATUS` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kamar`
--

INSERT INTO `kamar` (`ID_KAMAR`, `ID_JENIS_KAMAR`, `NO_KAMAR`, `STATUS`) VALUES
(1, 1, '12', 'Tersedia'),
(2, 2, '1001', 'Terisi'),
(3, 3, '2001', 'Tersedia'),
(5, 4, '3001', 'Tersedia'),
(6, 5, '4001', 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `laundry`
--

CREATE TABLE `laundry` (
  `ID_LAUNDRY` int(11) NOT NULL,
  `ID_KAMAR` int(11) DEFAULT NULL,
  `ID_PELANGGAN` int(11) DEFAULT NULL,
  `KODE_LAUNDRY` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laundry`
--

INSERT INTO `laundry` (`ID_LAUNDRY`, `ID_KAMAR`, `ID_PELANGGAN`, `KODE_LAUNDRY`) VALUES
(2, 1, 1, 'LDR001'),
(4, 1, 37, 'LDR002'),
(5, 1, 37, 'LDR003');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `ID_PELANGGAN` int(11) NOT NULL,
  `NAMA_PELANGGAN` varchar(255) DEFAULT NULL,
  `JENIS_KELAMIN` varchar(255) DEFAULT NULL,
  `ALAMAT` varchar(255) DEFAULT NULL,
  `KOTA` varchar(255) DEFAULT NULL,
  `NO_TLP` varchar(255) DEFAULT NULL,
  `EMAIL` varchar(255) DEFAULT NULL,
  `PASSWORD` varchar(125) NOT NULL,
  `STATUS_PELANGGAN` varchar(125) NOT NULL,
  `ROLE` varchar(125) NOT NULL,
  `verify_token` varchar(150) NOT NULL,
  `verify_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`ID_PELANGGAN`, `NAMA_PELANGGAN`, `JENIS_KELAMIN`, `ALAMAT`, `KOTA`, `NO_TLP`, `EMAIL`, `PASSWORD`, `STATUS_PELANGGAN`, `ROLE`, `verify_token`, `verify_status`) VALUES
(1, 'admin', 'L', 'jghkjhg', 'ee', 'eee', 'nylaanidia@gmail.com', 'admin', 'aktif', 'admin', '', 1),
(34, 'admin', 'admin', 'admin', 'admin', 'admin', 'nylaanidia@gmail.com', 'admin', 'aktif', 'admin', '', 1),
(35, 'nylaanidia', 'Perempuan', 'sidoarjo', 'sidoarjo', '085234804959', 'nilaeeanidia@gmail.com', '$2y$10$qe8MgdLZ/j60HObTYvTG3OGEN6TA.yZngo1GAzyvJUjYwLrV8V9T.', 'Tidak Aktif', 'Pelanggan', 'daa0352f067617ee2085ffbd5a023e8d', 0),
(37, 'swed', 'Perempuan', 'www', 'wadungasih', '222', 'nilaanidia@gmail.com', '$2y$10$9/j8qlwKJHXIqfYA.4agy.w9BAcLmz0au1Zwmh9Pi/K0RXtQayygm', 'Aktif', 'Pelanggan', '626a9c10a09362fd0e93b7c7099e1023', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `ID_PEMBAYARAN` int(11) NOT NULL,
  `ID_JENIS_KAMAR` int(11) DEFAULT NULL,
  `ID_FNB` int(11) DEFAULT NULL,
  `ID_LAUNDRY` int(11) DEFAULT NULL,
  `ID_PELANGGAN` int(11) DEFAULT NULL,
  `TGL_CHECK_IN` varchar(125) NOT NULL,
  `ID_CHECKIN_OUT` varchar(125) NOT NULL,
  `LAMA_INAP` varchar(125) NOT NULL,
  `TOTAL` varchar(125) NOT NULL,
  `UANG_MUKA` varchar(125) NOT NULL,
  `SISA_PEMBAYARAN` varchar(125) NOT NULL,
  `BIAYA_LAUNDRY` varchar(125) NOT NULL,
  `BIAYA_FNB` varchar(125) NOT NULL,
  `TOTAL_BAYAR` varchar(125) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`ID_PEMBAYARAN`, `ID_JENIS_KAMAR`, `ID_FNB`, `ID_LAUNDRY`, `ID_PELANGGAN`, `TGL_CHECK_IN`, `ID_CHECKIN_OUT`, `LAMA_INAP`, `TOTAL`, `UANG_MUKA`, `SISA_PEMBAYARAN`, `BIAYA_LAUNDRY`, `BIAYA_FNB`, `TOTAL_BAYAR`) VALUES
(1, 1, NULL, NULL, 1, '', '', '', '', '', '', '', '', ''),
(2, 1, NULL, NULL, 35, '', '', '', '', '', '', '', '', ''),
(6, 1, NULL, NULL, 37, '2025-12-29', '12', '4', '100000', '22220', '0', '0', '0', '77780');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `ID_PEMESANAN` int(11) NOT NULL,
  `ID_JENIS_KAMAR` int(11) DEFAULT NULL,
  `ID_PELANGGAN` int(11) DEFAULT NULL,
  `TGL_PESAN` date DEFAULT NULL,
  `JAM_PESAN` time DEFAULT NULL,
  `TGL_CHECK_IN` varchar(125) NOT NULL,
  `UANG_MUKA` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`ID_PEMESANAN`, `ID_JENIS_KAMAR`, `ID_PELANGGAN`, `TGL_PESAN`, `JAM_PESAN`, `TGL_CHECK_IN`, `UANG_MUKA`) VALUES
(1, 1, 1, '2025-11-09', '22:01:00', '', 22220),
(28, 2, 35, '2025-12-25', '18:54:00', '2025-12-01', 23000),
(33, 1, 37, '2025-12-26', '01:31:00', '2025-12-29', 22220);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(125) NOT NULL,
  `password` varchar(125) NOT NULL,
  `id_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `id_level`) VALUES
(1, 'admin', 'admin', 0),
(5, 'nyla', 'nyla', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checkin_checkout`
--
ALTER TABLE `checkin_checkout`
  ADD PRIMARY KEY (`ID_CHECKIN_OUT`),
  ADD KEY `RELATIONSHIP_6_FK` (`ID_PELANGGAN`),
  ADD KEY `RELATIONSHIP_10_FK` (`ID_JENIS_KAMAR`),
  ADD KEY `RELATIONSHIP_15_FK` (`ID_KAMAR`),
  ADD KEY `RELATIONSHIP_19_FK` (`ID_PEMESANAN`);

--
-- Indexes for table `detail_food_n_beverage`
--
ALTER TABLE `detail_food_n_beverage`
  ADD PRIMARY KEY (`ID_DETAIL_FNB`),
  ADD KEY `RELATIONSHIP_5_FK` (`ID_PELANGGAN`),
  ADD KEY `RELATIONSHIP_18_FK` (`ID_FNB`);

--
-- Indexes for table `detail_laundry`
--
ALTER TABLE `detail_laundry`
  ADD PRIMARY KEY (`ID_DETAIL_LAUNDRY`),
  ADD KEY `RELATIONSHIP_2_FK` (`ID_PELANGGAN`),
  ADD KEY `RELATIONSHIP_16_FK` (`ID_LAUNDRY`);

--
-- Indexes for table `feedback_pelanggan`
--
ALTER TABLE `feedback_pelanggan`
  ADD PRIMARY KEY (`ID_FEEDBACK`),
  ADD KEY `ID_PELANGGAN` (`ID_PELANGGAN`) USING BTREE,
  ADD KEY `ID_CHECKIN_OUT` (`ID_CHECKIN_OUT`) USING BTREE;

--
-- Indexes for table `food_n_beverage`
--
ALTER TABLE `food_n_beverage`
  ADD PRIMARY KEY (`ID_FNB`),
  ADD KEY `RELATIONSHIP_4_FK` (`ID_PELANGGAN`),
  ADD KEY `RELATIONSHIP_12_FK` (`ID_KAMAR`);

--
-- Indexes for table `jenis_kamar`
--
ALTER TABLE `jenis_kamar`
  ADD PRIMARY KEY (`ID_JENIS_KAMAR`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`ID_KAMAR`),
  ADD KEY `RELATIONSHIP_8_FK` (`ID_JENIS_KAMAR`);

--
-- Indexes for table `laundry`
--
ALTER TABLE `laundry`
  ADD PRIMARY KEY (`ID_LAUNDRY`),
  ADD KEY `RELATIONSHIP_3_FK` (`ID_PELANGGAN`),
  ADD KEY `RELATIONSHIP_13_FK` (`ID_KAMAR`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`ID_PELANGGAN`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`ID_PEMBAYARAN`),
  ADD KEY `RELATIONSHIP_7_FK` (`ID_PELANGGAN`),
  ADD KEY `RELATIONSHIP_9_FK` (`ID_JENIS_KAMAR`),
  ADD KEY `RELATIONSHIP_14_FK` (`ID_LAUNDRY`),
  ADD KEY `RELATIONSHIP_17_FK` (`ID_FNB`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`ID_PEMESANAN`),
  ADD KEY `RELATIONSHIP_1_FK` (`ID_PELANGGAN`),
  ADD KEY `RELATIONSHIP_11_FK` (`ID_JENIS_KAMAR`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checkin_checkout`
--
ALTER TABLE `checkin_checkout`
  MODIFY `ID_CHECKIN_OUT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `detail_food_n_beverage`
--
ALTER TABLE `detail_food_n_beverage`
  MODIFY `ID_DETAIL_FNB` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_laundry`
--
ALTER TABLE `detail_laundry`
  MODIFY `ID_DETAIL_LAUNDRY` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feedback_pelanggan`
--
ALTER TABLE `feedback_pelanggan`
  MODIFY `ID_FEEDBACK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `food_n_beverage`
--
ALTER TABLE `food_n_beverage`
  MODIFY `ID_FNB` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `jenis_kamar`
--
ALTER TABLE `jenis_kamar`
  MODIFY `ID_JENIS_KAMAR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kamar`
--
ALTER TABLE `kamar`
  MODIFY `ID_KAMAR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `laundry`
--
ALTER TABLE `laundry`
  MODIFY `ID_LAUNDRY` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `ID_PELANGGAN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `ID_PEMBAYARAN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `ID_PEMESANAN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checkin_checkout`
--
ALTER TABLE `checkin_checkout`
  ADD CONSTRAINT `FK_CHECKIN__RELATIONS_JENIS_KA` FOREIGN KEY (`ID_JENIS_KAMAR`) REFERENCES `jenis_kamar` (`ID_JENIS_KAMAR`),
  ADD CONSTRAINT `FK_CHECKIN__RELATIONS_KAMAR` FOREIGN KEY (`ID_KAMAR`) REFERENCES `kamar` (`ID_KAMAR`),
  ADD CONSTRAINT `FK_CHECKIN__RELATIONS_PELANGGA` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`),
  ADD CONSTRAINT `FK_CHECKIN__RELATIONS_PEMESANA` FOREIGN KEY (`ID_PEMESANAN`) REFERENCES `pemesanan` (`ID_PEMESANAN`);

--
-- Constraints for table `detail_food_n_beverage`
--
ALTER TABLE `detail_food_n_beverage`
  ADD CONSTRAINT `FK_DETAIL_F_RELATIONS_FOOD_N_B` FOREIGN KEY (`ID_FNB`) REFERENCES `food_n_beverage` (`ID_FNB`),
  ADD CONSTRAINT `FK_DETAIL_F_RELATIONS_PELANGGA` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`);

--
-- Constraints for table `detail_laundry`
--
ALTER TABLE `detail_laundry`
  ADD CONSTRAINT `FK_DETAIL_L_RELATIONS_LAUNDRY` FOREIGN KEY (`ID_LAUNDRY`) REFERENCES `laundry` (`ID_LAUNDRY`),
  ADD CONSTRAINT `FK_DETAIL_L_RELATIONS_PELANGGA` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`);

--
-- Constraints for table `feedback_pelanggan`
--
ALTER TABLE `feedback_pelanggan`
  ADD CONSTRAINT `feedback_pelanggan_ibfk_1` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`),
  ADD CONSTRAINT `feedback_pelanggan_ibfk_2` FOREIGN KEY (`ID_CHECKIN_OUT`) REFERENCES `checkin_checkout` (`ID_CHECKIN_OUT`);

--
-- Constraints for table `food_n_beverage`
--
ALTER TABLE `food_n_beverage`
  ADD CONSTRAINT `FK_FOOD_N_B_RELATIONS_KAMAR` FOREIGN KEY (`ID_KAMAR`) REFERENCES `kamar` (`ID_KAMAR`),
  ADD CONSTRAINT `FK_FOOD_N_B_RELATIONS_PELANGGA` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`);

--
-- Constraints for table `kamar`
--
ALTER TABLE `kamar`
  ADD CONSTRAINT `FK_KAMAR_RELATIONS_JENIS_KA` FOREIGN KEY (`ID_JENIS_KAMAR`) REFERENCES `jenis_kamar` (`ID_JENIS_KAMAR`);

--
-- Constraints for table `laundry`
--
ALTER TABLE `laundry`
  ADD CONSTRAINT `FK_LAUNDRY_RELATIONS_KAMAR` FOREIGN KEY (`ID_KAMAR`) REFERENCES `kamar` (`ID_KAMAR`),
  ADD CONSTRAINT `FK_LAUNDRY_RELATIONS_PELANGGA` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `FK_PEMBAYAR_RELATIONS_FOOD_N_B` FOREIGN KEY (`ID_FNB`) REFERENCES `food_n_beverage` (`ID_FNB`),
  ADD CONSTRAINT `FK_PEMBAYAR_RELATIONS_JENIS_KA` FOREIGN KEY (`ID_JENIS_KAMAR`) REFERENCES `jenis_kamar` (`ID_JENIS_KAMAR`),
  ADD CONSTRAINT `FK_PEMBAYAR_RELATIONS_LAUNDRY` FOREIGN KEY (`ID_LAUNDRY`) REFERENCES `laundry` (`ID_LAUNDRY`),
  ADD CONSTRAINT `FK_PEMBAYAR_RELATIONS_PELANGGA` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`);

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `FK_PEMESANA_RELATIONS_JENIS_KA` FOREIGN KEY (`ID_JENIS_KAMAR`) REFERENCES `jenis_kamar` (`ID_JENIS_KAMAR`),
  ADD CONSTRAINT `FK_PEMESANA_RELATIONS_PELANGGA` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
