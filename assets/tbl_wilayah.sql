-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2023 at 10:14 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gudang`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_wilayah`
--

CREATE TABLE `tbl_wilayah` (
  `id_wilayah` int(11) NOT NULL,
  `kode_wilayah` varchar(255) NOT NULL,
  `id_cabang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_wilayah`
--

INSERT INTO `tbl_wilayah` (`id_wilayah`, `kode_wilayah`, `id_cabang`) VALUES
(8, 'Area 1', 1),
(9, 'Area 1', 2),
(10, 'Area 1', 4),
(11, 'Area 1', 5),
(12, 'Area 1', 6),
(13, 'Area 2', 1),
(14, 'Area 2', 2),
(15, 'Area 2', 4),
(16, 'Area 2', 5),
(17, 'Area 2', 6),
(18, 'Area 3', 1),
(19, 'Area 3', 2),
(20, 'Area 3', 4),
(21, 'Area 3', 5),
(22, 'Area 3', 6),
(23, 'Eks Telkom', 1),
(24, 'Eks Telkom', 2),
(25, 'Eks Telkom', 4),
(26, 'Eks Telkom', 5),
(27, 'Eks Telkom', 6),
(28, 'Bekasi Kota', 1),
(29, 'Bekasi Utara', 1),
(30, 'Bekasi Selatan', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  ADD PRIMARY KEY (`id_wilayah`),
  ADD KEY `id_cabang` (`id_cabang`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  MODIFY `id_wilayah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  ADD CONSTRAINT `tbl_wilayah_ibfk_1` FOREIGN KEY (`id_cabang`) REFERENCES `tbl_cabang` (`id_cabang`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
