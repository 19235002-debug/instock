-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 04, 2026 at 10:13 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.27RC1

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
-- Table structure for table `adjustment`
--

CREATE TABLE `adjustment` (
  `id_adjustment` int NOT NULL,
  `id_barang` varchar(5) DEFAULT NULL,
  `tanggal_adjustment` date DEFAULT NULL,
  `jumlah_adjustment` int DEFAULT NULL,
  `keterangan` text,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Approved',
  `created_by_role` varchar(50) DEFAULT 'Administrator',
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `catatan_approval` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` varchar(255) NOT NULL,
  `no_rak` varchar(255) NOT NULL,
  `status` enum('active','not active') NOT NULL DEFAULT 'not active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `no_rak`, `status`) VALUES
('1', 'A-001', 'active'),
('2', 'A-002', 'active'),
('3', 'A-003', 'active'),
('4', 'A-004', 'active'),
('5', 'A-005', 'active'),
('6', 'A-006', 'active'),
('7', 'A-007', 'active'),
('8', 'A-008', 'active'),
('9', 'A-009', 'active'),
('10', 'A-010', 'active'),
('11', 'A-011', 'active'),
('12', 'A-012', 'active'),
('13', 'A-013', 'active'),
('14', 'A-014', 'active'),
('15', 'A-015', 'active'),
('16', 'A-016', 'not active'),
('17', 'A-017', 'not active'),
('18', 'A-018', 'not active'),
('19', 'A-019', 'not active'),
('20', 'A-020', 'not active'),
('21', 'A-021', 'not active'),
('22', 'A-022', 'not active'),
('23', 'A-023', 'not active'),
('24', 'A-024', 'not active'),
('25', 'A-025', 'not active'),
('26', 'A-026', 'not active'),
('27', 'A-027', 'not active'),
('28', 'A-028', 'not active'),
('29', 'A-029', 'not active'),
('30', 'A-030', 'not active'),
('31', 'A-031', 'not active'),
('32', 'A-032', 'not active'),
('33', 'A-033', 'not active'),
('34', 'A-034', 'not active'),
('35', 'A-035', 'not active'),
('36', 'A-036', 'not active'),
('37', 'A-037', 'not active'),
('38', 'A-038', 'not active'),
('39', 'A-039', 'not active'),
('40', 'A-040', 'not active'),
('41', 'A-041', 'not active'),
('42', 'A-042', 'not active'),
('43', 'A-043', 'not active'),
('44', 'A-044', 'not active'),
('45', 'A-045', 'not active'),
('46', 'A-046', 'not active'),
('47', 'A-047', 'not active'),
('48', 'A-048', 'not active'),
('49', 'A-049', 'not active'),
('50', 'A-050', 'not active'),
('51', 'A-051', 'not active'),
('52', 'A-052', 'not active'),
('53', 'A-053', 'not active'),
('54', 'A-054', 'not active'),
('55', 'A-055', 'not active'),
('56', 'A-056', 'not active'),
('57', 'A-057', 'not active'),
('58', 'A-058', 'not active'),
('59', 'A-059', 'not active'),
('60', 'A-060', 'not active'),
('61', 'A-061', 'not active'),
('62', 'A-062', 'not active'),
('63', 'A-063', 'not active'),
('64', 'A-064', 'not active'),
('65', 'A-065', 'not active'),
('66', 'A-066', 'not active'),
('67', 'A-067', 'not active'),
('68', 'A-068', 'not active'),
('69', 'A-069', 'not active'),
('70', 'A-070', 'not active'),
('71', 'A-071', 'not active'),
('72', 'A-072', 'not active'),
('73', 'A-073', 'not active'),
('74', 'A-074', 'not active'),
('75', 'A-075', 'not active'),
('76', 'A-076', 'not active'),
('77', 'A-077', 'not active'),
('78', 'A-078', 'not active'),
('79', 'A-079', 'not active'),
('80', 'A-080', 'not active'),
('81', 'A-081', 'not active'),
('82', 'A-082', 'not active'),
('83', 'A-083', 'not active'),
('84', 'A-084', 'not active'),
('85', 'A-085', 'not active'),
('86', 'A-086', 'not active'),
('87', 'A-087', 'not active'),
('88', 'A-088', 'not active'),
('89', 'A-089', 'not active'),
('90', 'A-090', 'not active'),
('91', 'A-091', 'not active'),
('92', 'A-092', 'not active'),
('93', 'A-093', 'not active'),
('94', 'A-094', 'not active'),
('95', 'A-095', 'not active'),
('96', 'A-096', 'not active'),
('97', 'A-097', 'not active'),
('98', 'A-098', 'not active'),
('99', 'A-099', 'not active'),
('100', 'A-100', 'not active');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_audit_log`
--

CREATE TABLE `tbl_audit_log` (
  `id_log` int NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_user` int DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_audit_log`
--

INSERT INTO `tbl_audit_log` (`id_log`, `timestamp`, `id_user`, `username`, `role`, `action`, `module`, `details`, `ip_address`) VALUES
(1, '2026-09-04 16:27:52', 19, 'admin', 'Administrator', 'Ubah Pengaturan Sistem', 'pengaturan', 'Administrator memperbarui logo (images/img/logo_1788514071.png) dan identitas aplikasi.', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang`
--

CREATE TABLE `tbl_barang` (
  `id_barang` varchar(5) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jenis` int NOT NULL,
  `stok_minimum` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `satuan` int NOT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `rak_id` varchar(255) DEFAULT NULL,
  `id_cabang` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_barang`
--

INSERT INTO `tbl_barang` (`id_barang`, `nama_barang`, `jenis`, `stok_minimum`, `stok`, `satuan`, `foto`, `rak_id`, `id_cabang`) VALUES
('B0001', 'Air Freshener Matic Spray', 11, 1, 15, 7, NULL, '1', 1),
('B0002', 'Baterai AAA', 11, 1, 15, 7, NULL, '2', 1),
('B0003', 'Baterai AA', 11, 1, 15, 7, NULL, '3', 1),
('B0004', 'Floor Cleaner', 11, 1, 15, 7, NULL, '4', 1),
('B0005', 'Karbol Wangi', 11, 1, 15, 7, NULL, '5', 1),
('B0006', 'Hand Soap', 11, 1, 15, 7, NULL, '6', 1),
('B0007', 'AMPLAS HALUS NO. 500', 13, 1, 15, 7, NULL, '7', 1),
('B0008', 'HIT 600 Ml', 13, 1, 15, 7, NULL, '8', 1),
('B0009', 'BUFFING PAD 16\" MERAH - PUTIH', 13, 1, 15, 7, NULL, '9', 1),
('B0010', 'BULU AYAM RRT', 13, 1, 15, 7, NULL, '10', 1),
('B0011', 'BAK SAMPAH 80 LTR', 13, 1, 15, 7, NULL, '11', 1),
('B0012', 'Tissue Kotak', 14, 1, 15, 7, NULL, '12', 1),
('B0013', 'Tissue Roll', 14, 1, 15, 7, NULL, '13', 1),
('B0014', 'Tissue Roll Jumbo', 14, 1, 15, 7, NULL, '14', 1),
('B0015', 'Tissue Hand Towel', 14, 1, 15, 7, NULL, '15', 1),
('B0016', 'Air Freshener Matic Spray', 11, 1, 35, 7, NULL, '16', 2),
('B0017', 'Baterai AAA', 11, 1, 35, 7, NULL, '17', 2),
('B0018', 'Baterai AA', 11, 1, 35, 7, NULL, '18', 2),
('B0019', 'Floor Cleaner', 11, 1, 35, 7, NULL, '19', 2),
('B0020', 'Karbol Wangi', 11, 1, 35, 7, NULL, '20', 2),
('B0021', 'Hand Soap', 11, 1, 35, 7, NULL, '21', 2),
('B0022', 'AMPLAS HALUS NO. 500', 13, 1, 35, 7, NULL, '22', 2),
('B0023', 'HIT 600 Ml', 13, 1, 35, 7, NULL, '23', 2),
('B0024', 'BUFFING PAD 16\" MERAH - PUTIH', 13, 1, 35, 7, NULL, '24', 2),
('B0025', 'BULU AYAM RRT', 13, 1, 35, 7, NULL, '25', 2),
('B0026', 'BAK SAMPAH 80 LTR', 13, 1, 35, 7, NULL, '26', 2),
('B0027', 'Tissue Kotak', 14, 1, 35, 7, NULL, '27', 2),
('B0028', 'Tissue Roll', 14, 1, 35, 7, NULL, '28', 2),
('B0029', 'Tissue Roll Jumbo', 14, 1, 35, 7, NULL, '29', 2),
('B0030', 'Tissue Hand Towel', 14, 1, 35, 7, NULL, '30', 2),
('B0031', 'Air Freshener Matic Spray', 11, 1, 95, 7, NULL, '31', 4),
('B0032', 'Baterai AAA', 11, 1, 95, 7, NULL, '32', 4),
('B0033', 'Baterai AA', 11, 1, 95, 7, NULL, '33', 4),
('B0034', 'Floor Cleaner', 11, 1, 95, 7, NULL, '34', 4),
('B0035', 'Karbol Wangi', 11, 1, 95, 7, NULL, '35', 4),
('B0036', 'Hand Soap', 11, 1, 95, 7, NULL, '36', 4),
('B0037', 'AMPLAS HALUS NO. 500', 13, 1, 95, 7, NULL, '37', 4),
('B0038', 'HIT 600 Ml', 13, 1, 95, 7, NULL, '38', 4),
('B0039', 'BUFFING PAD 16\" MERAH - PUTIH', 13, 1, 95, 7, NULL, '39', 4),
('B0040', 'BULU AYAM RRT', 13, 1, 95, 7, NULL, '40', 4),
('B0041', 'BAK SAMPAH 80 LTR', 13, 1, 95, 7, NULL, '41', 4),
('B0042', 'Tissue Kotak', 14, 1, 95, 7, NULL, '42', 4),
('B0043', 'Tissue Roll', 14, 1, 95, 7, NULL, '43', 4),
('B0044', 'Tissue Roll Jumbo', 14, 1, 95, 7, NULL, '44', 4),
('B0045', 'Tissue Hand Towel', 14, 1, 95, 7, NULL, '45', 4),
('B0046', 'Air Freshener Matic Spray', 11, 1, 65, 7, NULL, '46', 5),
('B0047', 'Baterai AAA', 11, 1, 65, 7, NULL, '47', 5),
('B0048', 'Baterai AA', 11, 1, 65, 7, NULL, '48', 5),
('B0049', 'Floor Cleaner', 11, 1, 65, 7, NULL, '49', 5),
('B0050', 'Karbol Wangi', 11, 1, 65, 7, NULL, '50', 5),
('B0051', 'Hand Soap', 11, 1, 65, 7, NULL, '51', 5),
('B0052', 'AMPLAS HALUS NO. 500', 13, 1, 65, 7, NULL, '52', 5),
('B0053', 'HIT 600 Ml', 13, 1, 65, 7, NULL, '53', 5),
('B0054', 'BUFFING PAD 16\" MERAH - PUTIH', 13, 1, 65, 7, NULL, '54', 5),
('B0055', 'BULU AYAM RRT', 13, 1, 65, 7, NULL, '55', 5),
('B0056', 'BAK SAMPAH 80 LTR', 13, 1, 65, 7, NULL, '56', 5),
('B0057', 'Tissue Kotak', 14, 1, 65, 7, NULL, '57', 5),
('B0058', 'Tissue Roll', 14, 1, 65, 7, NULL, '58', 5),
('B0059', 'Tissue Roll Jumbo', 14, 1, 65, 7, NULL, '59', 5),
('B0060', 'Tissue Hand Towel', 14, 1, 65, 7, NULL, '60', 5),
('B0061', 'Air Freshener Matic Spray', 11, 1, 45, 7, NULL, '61', 6),
('B0062', 'Baterai AAA', 11, 1, 45, 7, NULL, '62', 6),
('B0063', 'Baterai AA', 11, 1, 45, 7, NULL, '63', 6),
('B0064', 'Floor Cleaner', 11, 1, 45, 7, NULL, '64', 6),
('B0065', 'Karbol Wangi', 11, 1, 45, 7, NULL, '65', 6),
('B0066', 'Hand Soap', 11, 1, 45, 7, NULL, '66', 6),
('B0067', 'AMPLAS HALUS NO. 500', 13, 1, 45, 7, NULL, '67', 6),
('B0068', 'HIT 600 Ml', 13, 1, 45, 7, NULL, '68', 6),
('B0069', 'BUFFING PAD 16\" MERAH - PUTIH', 13, 1, 45, 7, NULL, '69', 6),
('B0070', 'BULU AYAM RRT', 13, 1, 45, 7, NULL, '70', 6),
('B0071', 'BAK SAMPAH 80 LTR', 13, 1, 45, 7, NULL, '71', 6),
('B0072', 'Tissue Kotak', 14, 1, 45, 7, NULL, '72', 6),
('B0073', 'Tissue Roll', 14, 1, 45, 7, NULL, '73', 6),
('B0074', 'Tissue Roll Jumbo', 14, 1, 45, 7, NULL, '74', 6),
('B0075', 'Tissue Hand Towel', 14, 1, 45, 7, NULL, '75', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang_keluar`
--

CREATE TABLE `tbl_barang_keluar` (
  `id_transaksi` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `barang` varchar(5) NOT NULL,
  `jumlah` int DEFAULT NULL,
  `nama_lokasi` varchar(255) DEFAULT NULL,
  `id_cabang` int DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Approved',
  `created_by_role` varchar(50) DEFAULT 'Administrator',
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `catatan_approval` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `tbl_barang_keluar`
--
DELIMITER $$
CREATE TRIGGER `hapus_stok_keluar` BEFORE DELETE ON `tbl_barang_keluar` FOR EACH ROW BEGIN
UPDATE tbl_barang SET stok=stok+OLD.jumlah
WHERE id_barang=OLD.barang;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stok_keluar` AFTER INSERT ON `tbl_barang_keluar` FOR EACH ROW BEGIN
UPDATE tbl_barang SET stok=stok-NEW.jumlah
WHERE id_barang=NEW.barang;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang_masuk`
--

CREATE TABLE `tbl_barang_masuk` (
  `id_transaksi` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `barang` varchar(5) NOT NULL,
  `jumlah` int DEFAULT NULL,
  `id_satuan` int DEFAULT NULL,
  `id_cabang` int DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Approved',
  `created_by_role` varchar(50) DEFAULT 'Administrator',
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `catatan_approval` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_barang_masuk`
--

INSERT INTO `tbl_barang_masuk` (`id_transaksi`, `tanggal`, `barang`, `jumlah`, `id_satuan`, `id_cabang`, `status`, `created_by_role`, `approved_by`, `approved_at`, `catatan_approval`) VALUES
('TM-0000001', '2023-10-26', 'B0001', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000002', '2023-10-26', 'B0002', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000003', '2023-10-26', 'B0003', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000004', '2023-10-26', 'B0004', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000005', '2023-10-26', 'B0005', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000006', '2023-10-26', 'B0006', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000007', '2023-10-26', 'B0007', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000008', '2023-10-26', 'B0008', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000009', '2023-10-26', 'B0009', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000010', '2023-10-26', 'B0010', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000011', '2023-10-26', 'B0011', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000012', '2023-10-26', 'B0012', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000013', '2023-10-26', 'B0013', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000014', '2023-10-26', 'B0014', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000015', '2023-10-26', 'B0015', 15, 7, 1, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000016', '2023-10-26', 'B0016', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000017', '2023-10-26', 'B0017', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000018', '2023-10-26', 'B0018', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000019', '2023-10-26', 'B0019', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000020', '2023-10-26', 'B0020', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000021', '2023-10-26', 'B0021', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000022', '2023-10-26', 'B0022', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000023', '2023-10-26', 'B0023', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000024', '2023-10-26', 'B0024', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000025', '2023-10-26', 'B0025', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000026', '2023-10-26', 'B0026', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000027', '2023-10-26', 'B0027', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000028', '2023-10-26', 'B0028', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000029', '2023-10-26', 'B0029', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000030', '2023-10-26', 'B0030', 35, 7, 2, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000031', '2023-10-26', 'B0031', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000032', '2023-10-26', 'B0032', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000033', '2023-10-26', 'B0033', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000034', '2023-10-26', 'B0034', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000035', '2023-10-26', 'B0035', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000036', '2023-10-26', 'B0036', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000037', '2023-10-26', 'B0037', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000038', '2023-10-26', 'B0038', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000039', '2023-10-26', 'B0039', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000040', '2023-10-26', 'B0040', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000041', '2023-10-26', 'B0041', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000042', '2023-10-26', 'B0042', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000043', '2023-10-26', 'B0043', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000044', '2023-10-26', 'B0044', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000045', '2023-10-26', 'B0045', 95, 7, 4, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000046', '2023-10-26', 'B0046', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000047', '2023-10-26', 'B0047', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000048', '2023-10-26', 'B0048', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000049', '2023-10-26', 'B0049', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000050', '2023-10-26', 'B0050', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000051', '2023-10-26', 'B0051', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000052', '2023-10-26', 'B0052', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000053', '2023-10-26', 'B0053', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000054', '2023-10-26', 'B0054', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000055', '2023-10-26', 'B0055', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000056', '2023-10-26', 'B0056', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000057', '2023-10-26', 'B0057', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000058', '2023-10-26', 'B0058', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000059', '2023-10-26', 'B0059', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000060', '2023-10-26', 'B0060', 65, 7, 5, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000061', '2023-10-26', 'B0061', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000062', '2023-10-26', 'B0062', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000063', '2023-10-26', 'B0063', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000064', '2023-10-26', 'B0064', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000065', '2023-10-26', 'B0065', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000066', '2023-10-26', 'B0066', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000067', '2023-10-26', 'B0067', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000068', '2023-10-26', 'B0068', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000069', '2023-10-26', 'B0069', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000070', '2023-10-26', 'B0070', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000071', '2023-10-26', 'B0071', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000072', '2023-10-26', 'B0072', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000073', '2023-10-26', 'B0073', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000074', '2023-10-26', 'B0074', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL),
('TM-0000075', '2023-10-26', 'B0075', 45, 7, 6, 'Approved', 'Administrator', NULL, NULL, NULL);

--
-- Triggers `tbl_barang_masuk`
--
DELIMITER $$
CREATE TRIGGER `hapus_stok_masuk` BEFORE DELETE ON `tbl_barang_masuk` FOR EACH ROW BEGIN
UPDATE tbl_barang SET stok=stok-OLD.jumlah
WHERE id_barang=OLD.barang;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stok_masuk` AFTER INSERT ON `tbl_barang_masuk` FOR EACH ROW BEGIN
UPDATE tbl_barang SET stok=stok+NEW.jumlah
WHERE id_barang=NEW.barang;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cabang`
--

CREATE TABLE `tbl_cabang` (
  `id_cabang` int NOT NULL,
  `nama_cabang` varchar(255) NOT NULL,
  `nama_fm` varchar(255) DEFAULT NULL,
  `nomor_telepon` varchar(15) DEFAULT NULL,
  `id_teritory` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_cabang`
--

INSERT INTO `tbl_cabang` (`id_cabang`, `nama_cabang`, `nama_fm`, `nomor_telepon`, `id_teritory`) VALUES
(1, 'Jakarta Timur - Bekasi', 'Bpk. Sahudin', '123', NULL),
(2, 'Jakarta Selatan - Bogor', 'Bpk Noordian Yanuar Tesar', '123', NULL),
(4, 'Jakarta Barat - Utara', 'Bpk Drajat', '-', NULL),
(5, 'Tanggerang - Banten', 'Bpk Angga', '-', NULL),
(6, 'Jakarta Pusat - MM', 'Bpk Santo', '-', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_jenis`
--

CREATE TABLE `tbl_jenis` (
  `id_jenis` int NOT NULL,
  `nama_jenis` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_jenis`
--

INSERT INTO `tbl_jenis` (`id_jenis`, `nama_jenis`) VALUES
(11, 'Chemical'),
(13, 'Peralatan Kerja'),
(14, 'Tissue');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_lokasi_sto`
--

CREATE TABLE `tbl_lokasi_sto` (
  `id_lokasi` int NOT NULL,
  `nama_lokasi` varchar(255) DEFAULT NULL,
  `kode_wilayah` varchar(255) DEFAULT NULL,
  `id_teritory` int DEFAULT NULL,
  `id_cabang` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_lokasi_sto`
--

INSERT INTO `tbl_lokasi_sto` (`id_lokasi`, `nama_lokasi`, `kode_wilayah`, `id_teritory`, `id_cabang`) VALUES
(31, 'STO Pondok Gede', 'Bekasi Kota', 1, 1),
(32, 'STO Kranji', 'Bekasi Kota', 1, 1),
(33, 'STO Bekasi', 'Bekasi Kota', 1, 1),
(34, 'STO Cibitung', 'Bekasi Kota', 1, 1),
(35, 'STO Bantar Gebang', 'Bekasi Kota', 1, 1),
(36, 'STO Kali Abang', 'Bekasi Utara', 1, 1),
(37, 'STO Muara Gembong', 'Bekasi Utara', 1, 1),
(38, 'STO Babelan', 'Bekasi Utara', 1, 1),
(39, 'STO Cibarusah', 'Bekasi Selatan', 1, 1),
(40, 'STO Cikarang', 'Bekasi Selatan', 1, 1),
(41, 'STO Sukaresmi', 'Bekasi Selatan', 1, 1),
(45, 'STO Palmerah', 'Area 1', 4, 4),
(46, 'STO Slipi', 'Area 1', 4, 4),
(47, 'STO Kedoya', 'Area 1', 4, 4),
(48, 'STO Meruya', 'Area 1', 4, 4),
(49, 'STO Cempaka Putih', 'Area 1', 6, 6),
(50, 'STO Rawa Mangun', 'Area 1', 6, 6),
(51, 'STO Pramuka', 'Area 1', 6, 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_permission`
--

CREATE TABLE `tbl_permission` (
  `id_permission` int NOT NULL,
  `role` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `is_allowed` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_permission`
--

INSERT INTO `tbl_permission` (`id_permission`, `role`, `module`, `is_allowed`) VALUES
(1, 'Kepala Gudang', 'barang', 1),
(2, 'Kepala Gudang', 'jenis', 1),
(3, 'Kepala Gudang', 'satuan', 1),
(4, 'Kepala Gudang', 'location', 1),
(5, 'Kepala Gudang', 'lokasi_sto', 1),
(6, 'Kepala Gudang', 'barang_masuk', 1),
(7, 'Kepala Gudang', 'barang_keluar', 1),
(8, 'Kepala Gudang', 'adjustment', 1),
(9, 'Kepala Gudang', 'laporan_stok', 1),
(10, 'Kepala Gudang', 'laporan_barang_masuk', 1),
(11, 'Kepala Gudang', 'laporan_barang_keluar', 1),
(12, 'Kepala Gudang', 'user', 1),
(13, 'Admin Gudang', 'barang', 1),
(14, 'Admin Gudang', 'jenis', 1),
(15, 'Admin Gudang', 'satuan', 1),
(16, 'Admin Gudang', 'location', 1),
(17, 'Admin Gudang', 'lokasi_sto', 1),
(18, 'Admin Gudang', 'barang_masuk', 1),
(19, 'Admin Gudang', 'barang_keluar', 1),
(20, 'Admin Gudang', 'adjustment', 0),
(21, 'Admin Gudang', 'laporan_stok', 1),
(22, 'Admin Gudang', 'laporan_barang_masuk', 0),
(23, 'Admin Gudang', 'laporan_barang_keluar', 1),
(24, 'Admin Gudang', 'user', 0),
(25, 'Supplier', 'barang', 1),
(26, 'Supplier', 'jenis', 1),
(27, 'Supplier', 'satuan', 1),
(28, 'Supplier', 'location', 1),
(29, 'Supplier', 'lokasi_sto', 0),
(30, 'Supplier', 'barang_masuk', 1),
(31, 'Supplier', 'barang_keluar', 0),
(32, 'Supplier', 'adjustment', 0),
(33, 'Supplier', 'laporan_stok', 1),
(34, 'Supplier', 'laporan_barang_masuk', 1),
(35, 'Supplier', 'laporan_barang_keluar', 0),
(36, 'Supplier', 'user', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_satuan`
--

CREATE TABLE `tbl_satuan` (
  `id_satuan` int NOT NULL,
  `nama_satuan` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_satuan`
--

INSERT INTO `tbl_satuan` (`id_satuan`, `nama_satuan`) VALUES
(7, 'BUAH');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_setting`
--

CREATE TABLE `tbl_setting` (
  `id_setting` int NOT NULL,
  `nama_aplikasi` varchar(255) DEFAULT 'INSTOCK',
  `slogan_aplikasi` varchar(255) DEFAULT 'Fulfillment Warehouse Management System',
  `nama_perusahaan` varchar(255) DEFAULT 'PT Instock Fulfillment Indonesia',
  `alamat_perusahaan` text,
  `telepon` varchar(50) DEFAULT '0812-3456-7890',
  `email` varchar(100) DEFAULT 'admin@instock.co.id',
  `logo` varchar(255) DEFAULT 'images/img/q.png',
  `favicon` varchar(255) DEFAULT 'images/img/logo.png',
  `footer_text` varchar(255) DEFAULT 'INSTOCK',
  `stok_minimum_default` int DEFAULT '5',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_setting`
--

INSERT INTO `tbl_setting` (`id_setting`, `nama_aplikasi`, `slogan_aplikasi`, `nama_perusahaan`, `alamat_perusahaan`, `telepon`, `email`, `logo`, `favicon`, `footer_text`, `stok_minimum_default`, `updated_at`) VALUES
(1, 'INSTOCK', 'Fulfillment Warehouse Management System', 'PT Instock Fulfillment Indonesia', 'Jl. Jendral Sudirman No. 123, Jakarta Selatan', '0812-3456-7890', 'admin@instock.co.id', 'images/img/logo_1788514071.png', 'images/img/logo.png', 'INSTOCK', 5, '2026-09-04 16:27:51');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transfer_stok`
--

CREATE TABLE `tbl_transfer_stok` (
  `id_transfer` int NOT NULL,
  `tanggal_transfer` date NOT NULL,
  `id_barang` varchar(50) NOT NULL,
  `jumlah_transfer` int NOT NULL,
  `id_cabang_asal` int NOT NULL,
  `id_cabang_tujuan` int NOT NULL,
  `nama_barang` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int NOT NULL,
  `nama_user` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hak_akses` enum('Administrator','Admin Gudang','Kepala Gudang','Supplier') NOT NULL,
  `id_cabang` int DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `nama_user`, `username`, `password`, `hak_akses`, `id_cabang`, `foto`) VALUES
(14, 'Bpk. Sahudin', 'Jaktim', '$2y$12$Z5BWtQC2ydkPLKjVM3PPP.HkCr5Ou01XZGmq2IF9qe1W4vUZ/.MJa', 'Admin Gudang', 1, 'user_14_1788516135.jpg'),
(15, 'Bpk. Tesar', 'Jaksel', '$2y$12$CGLKdYIQ4wp2YkyDXCULfedCZpppf/rFXY9b6SH393ktWtthJ5LKq', 'Admin Gudang', 2, NULL),
(16, 'Bpk. Santo', 'Jakpus', '$2y$12$zjiTDftmFtoLthTBG9NPEuQFls2UTNb/4PEO2Bxa0gK21qUdcKuOy', 'Admin Gudang', 6, NULL),
(17, 'Bpk. Drajat', 'Jakbar', '$2y$12$Vmikz/NMGwKkezoYQ5i/HOJYDBpYeD9QeWn/ct8VaiRVRQ2punix.', 'Admin Gudang', 4, NULL),
(18, 'Bpk Angga', 'Tgr', '$2y$12$X1jBsXgt2A.7a320pHWj9ecLjkCgrqTQhLlwqYbcwJwsHYowq41QS', 'Admin Gudang', 5, NULL),
(19, 'Regional', 'Admin', '$2y$12$w8WUhk.Qowq8B8TANI6AiO.iCwZ09LlcMOvyZ0Ki/vDrIrV3VcOXu', 'Administrator', 1, NULL),
(20, 'supp', 'supp', '$2y$12$DsLVKrgurBr1/zXtJ7r9AOvt4PFdRGAhavgggbgbJS49wATVDa.5u', 'Supplier', 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_wilayah`
--

CREATE TABLE `tbl_wilayah` (
  `id_wilayah` int NOT NULL,
  `kode_wilayah` varchar(255) NOT NULL,
  `id_cabang` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `temp_barang`
--

CREATE TABLE `temp_barang` (
  `id` int NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jenis_barang` varchar(100) NOT NULL,
  `id_jenis` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `temp_barang`
--

INSERT INTO `temp_barang` (`id`, `nama_barang`, `jenis_barang`, `id_jenis`) VALUES
(1, 'AMPLAS HALUS NO. 500', 'Peralatan Kerja', 13),
(2, 'HIT 600 Ml', 'Peralatan Kerja', 13),
(3, 'BUFFING PAD 16\" MERAH - PUTIH', 'Peralatan Kerja', 13),
(4, 'BUFFING PAD 16\" COKLAT - HITAM', 'Peralatan Kerja', 13),
(5, 'BUFFING PAD 20\" ( MERAH - PUTIH )', 'Peralatan Kerja', 13),
(6, 'BULU AYAM RRT', 'Peralatan Kerja', 13),
(7, 'BAK SAMPAH 80 LTR', 'Peralatan Kerja', 13),
(8, 'CANGKUL BESAR', 'Peralatan Kerja', 13),
(9, 'DUSTING MOP STRING SUPER COTTING I', 'Peralatan Kerja', 13),
(10, 'EMBER PLASTIK 4 GL (20 LTR)', 'Peralatan Kerja', 13),
(11, 'GOLOK', 'Peralatan Kerja', 13),
(12, 'GAYUNG', 'Peralatan Kerja', 13),
(13, 'GUNTING RUMPUT HITAM', 'Peralatan Kerja', 13),
(14, 'GUNTING RANTING', 'Peralatan Kerja', 13),
(15, 'JAMUR KACA', 'Peralatan Kerja', 13),
(16, 'KUAS 2\"', 'Peralatan Kerja', 13),
(17, 'KOP TOILET ( WC ) SUPER MERK NAGATA', 'Peralatan Kerja', 13),
(18, 'KACA MATA SAVETY', 'Peralatan Kerja', 13),
(19, 'KESED KARPET', 'Peralatan Kerja', 13),
(20, 'KESED KAIN', 'Peralatan Kerja', 13),
(21, 'KAIN LAP KOTAK (KATUN)', 'Peralatan Kerja', 13),
(22, 'KAIN MAJUN SUPER', 'Peralatan Kerja', 13),
(23, 'KANEBO', 'Peralatan Kerja', 13),
(24, 'KAMPER PING PONG', 'Peralatan Kerja', 13),
(25, 'LOBY DUSTER 60 CM SUPER (KANTONG) KW- 2', 'Peralatan Kerja', 13),
(26, 'LEMON PLADGE UK. BESAR', 'Peralatan Kerja', 13),
(27, 'PORSTEX', 'Peralatan Kerja', 13),
(28, 'PENGKI PLASTIK', 'Peralatan Kerja', 13),
(29, 'PENGKI KALENG GAGANG KAYU', 'Peralatan Kerja', 13),
(30, 'PLASTIK SAMPAH UK. 90 X 120 CM', 'Peralatan Kerja', 13),
(31, 'SEKAM BAKAR', 'Peralatan Kerja', 13),
(32, 'GRAMOXON UK,250ML', 'Peralatan Kerja', 13),
(33, 'KOMPOS', 'Peralatan Kerja', 13),
(34, 'STROPER', 'Peralatan Kerja', 13),
(35, 'SAPU NYLON NAGATA', 'Peralatan Kerja', 13),
(36, 'SAPU LIDI TANGKAI KAYU', 'Peralatan Kerja', 13),
(37, 'SAPU LIDI TAMAN', 'Peralatan Kerja', 13),
(38, 'SAIKAT TANGAN PLASTIK', 'Peralatan Kerja', 13),
(39, 'SIKAT TANGAN ETERNA', 'Peralatan Kerja', 13),
(40, 'SIKAT WC PLASTIK (LENGKUNG)', 'Peralatan Kerja', 13),
(41, 'SARUNG TANGAN KARET', 'Peralatan Kerja', 13),
(42, 'SARUNG TANGAN KAIN', 'Peralatan Kerja', 13),
(43, 'SIKAT LANTAI UK. 25 CM NYLON NAGATA', 'Peralatan Kerja', 13),
(44, 'TAPAS CUCI', 'Peralatan Kerja', 13),
(45, 'TAPAS SPOON', 'Peralatan Kerja', 13),
(46, 'TAPAS BESI', 'Peralatan Kerja', 13),
(47, 'WIFER LANTAI 50 CM TANGKAI BESI', 'Peralatan Kerja', 13),
(48, 'DUSTING MOP WARNA (MERAH,BIRU,HIJAU,KUNING)', 'Peralatan Kerja', 13),
(49, 'SIKAT CARPET NILFISK 16\"', 'Peralatan Kerja', 13),
(50, 'TANGKAI LOBY 60 CM ALMUNIUM', 'Peralatan Kerja', 13),
(51, 'LOBY DUSTER 60 CM ACRYLIC', 'Peralatan Kerja', 13),
(52, 'WIFER KACA UK. 35 CM', 'Peralatan Kerja', 13),
(53, 'WASHER UK. 35 CM', 'Peralatan Kerja', 13),
(54, 'KAIN WASHER UK 35 CM', 'Peralatan Kerja', 13),
(55, 'PAPAN WET FLOOR', 'Peralatan Kerja', 13),
(56, 'Air Freshener Matic Spray', 'Chemical', 11),
(57, 'Baterai AAA', 'Chemical', 11),
(58, 'Baterai AA', 'Chemical', 11),
(59, 'Floor Cleaner', 'Chemical', 11),
(60, 'Karbol Wangi', 'Chemical', 11),
(61, 'Hand Soap', 'Chemical', 11),
(62, 'Glass Cleaner', 'Chemical', 11),
(63, 'Pembersih & Pengharum Telepon', 'Chemical', 11),
(64, 'Pengharum Ruangan & Toilet', 'Chemical', 11),
(65, 'Pembersih Lantai Berkerak', 'Chemical', 11),
(66, 'Pengkilap Furniture', 'Chemical', 11),
(67, 'Marble Extra', 'Chemical', 11),
(68, 'Multi Purpose Cleaner (MPC)', 'Chemical', 11),
(69, 'Rugbee Shampo', 'Chemical', 11),
(70, 'Pembersih Keramik Sedang (Alkali Powder)', 'Chemical', 11),
(71, 'Tissue Kotak', 'Tissue', 14),
(72, 'Tissue Roll', 'Tissue', 14),
(73, 'Tissue Roll Jumbo', 'Tissue', 14),
(74, 'Tissue Hand Towel', 'Tissue', 14);

-- --------------------------------------------------------

--
-- Table structure for table `teritory_manager`
--

CREATE TABLE `teritory_manager` (
  `id_teritory` int NOT NULL,
  `teritory_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teritory_manager`
--

INSERT INTO `teritory_manager` (`id_teritory`, `teritory_name`) VALUES
(1, 'Jakarta Timur - Bekasi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adjustment`
--
ALTER TABLE `adjustment`
  ADD PRIMARY KEY (`id_adjustment`),
  ADD KEY `fk_adjustment_barang` (`id_barang`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`no_rak`);

--
-- Indexes for table `tbl_audit_log`
--
ALTER TABLE `tbl_audit_log`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `tbl_barang`
--
ALTER TABLE `tbl_barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `fk_rak` (`rak_id`);

--
-- Indexes for table `tbl_barang_keluar`
--
ALTER TABLE `tbl_barang_keluar`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `tbl_barang_masuk`
--
ALTER TABLE `tbl_barang_masuk`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `tbl_cabang`
--
ALTER TABLE `tbl_cabang`
  ADD PRIMARY KEY (`id_cabang`);

--
-- Indexes for table `tbl_jenis`
--
ALTER TABLE `tbl_jenis`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indexes for table `tbl_lokasi_sto`
--
ALTER TABLE `tbl_lokasi_sto`
  ADD PRIMARY KEY (`id_lokasi`),
  ADD KEY `id_teritory` (`id_teritory`);

--
-- Indexes for table `tbl_permission`
--
ALTER TABLE `tbl_permission`
  ADD PRIMARY KEY (`id_permission`),
  ADD UNIQUE KEY `role_module` (`role`,`module`);

--
-- Indexes for table `tbl_satuan`
--
ALTER TABLE `tbl_satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `tbl_setting`
--
ALTER TABLE `tbl_setting`
  ADD PRIMARY KEY (`id_setting`);

--
-- Indexes for table `tbl_transfer_stok`
--
ALTER TABLE `tbl_transfer_stok`
  ADD PRIMARY KEY (`id_transfer`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `fk_cabang` (`id_cabang`);

--
-- Indexes for table `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  ADD PRIMARY KEY (`id_wilayah`),
  ADD KEY `id_cabang` (`id_cabang`);

--
-- Indexes for table `temp_barang`
--
ALTER TABLE `temp_barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jenis` (`id_jenis`);

--
-- Indexes for table `teritory_manager`
--
ALTER TABLE `teritory_manager`
  ADD PRIMARY KEY (`id_teritory`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adjustment`
--
ALTER TABLE `adjustment`
  MODIFY `id_adjustment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_audit_log`
--
ALTER TABLE `tbl_audit_log`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_cabang`
--
ALTER TABLE `tbl_cabang`
  MODIFY `id_cabang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_jenis`
--
ALTER TABLE `tbl_jenis`
  MODIFY `id_jenis` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_lokasi_sto`
--
ALTER TABLE `tbl_lokasi_sto`
  MODIFY `id_lokasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `tbl_permission`
--
ALTER TABLE `tbl_permission`
  MODIFY `id_permission` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tbl_satuan`
--
ALTER TABLE `tbl_satuan`
  MODIFY `id_satuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_setting`
--
ALTER TABLE `tbl_setting`
  MODIFY `id_setting` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_transfer_stok`
--
ALTER TABLE `tbl_transfer_stok`
  MODIFY `id_transfer` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  MODIFY `id_wilayah` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `temp_barang`
--
ALTER TABLE `temp_barang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `teritory_manager`
--
ALTER TABLE `teritory_manager`
  MODIFY `id_teritory` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adjustment`
--
ALTER TABLE `adjustment`
  ADD CONSTRAINT `fk_adjustment_barang` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD CONSTRAINT `fk_cabang` FOREIGN KEY (`id_cabang`) REFERENCES `tbl_cabang` (`id_cabang`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  ADD CONSTRAINT `tbl_wilayah_ibfk_1` FOREIGN KEY (`id_cabang`) REFERENCES `tbl_cabang` (`id_cabang`) ON DELETE CASCADE;

--
-- Constraints for table `temp_barang`
--
ALTER TABLE `temp_barang`
  ADD CONSTRAINT `temp_barang_ibfk_1` FOREIGN KEY (`id_jenis`) REFERENCES `tbl_jenis` (`id_jenis`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
