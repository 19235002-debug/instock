-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 24 Okt 2023 pada 18.49
-- Versi server: 10.4.13-MariaDB
-- Versi PHP: 7.4.8

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
-- Struktur dari tabel `adjustment`
--

CREATE TABLE `adjustment` (
  `id_adjustment` int(11) NOT NULL,
  `id_barang` varchar(5) DEFAULT NULL,
  `tanggal_adjustment` date DEFAULT NULL,
  `jumlah_adjustment` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `location`
--

CREATE TABLE `location` (
  `id` varchar(255) NOT NULL,
  `no_rak` varchar(255) NOT NULL,
  `status` enum('active','not active') NOT NULL DEFAULT 'not active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `location`
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
('16', 'A-016', 'active'),
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
-- Struktur dari tabel `tbl_barang`
--

CREATE TABLE `tbl_barang` (
  `id_barang` varchar(5) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jenis` int(11) NOT NULL,
  `stok_minimum` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `satuan` int(11) NOT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `rak_id` varchar(255) DEFAULT NULL,
  `id_cabang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_barang`
--

INSERT INTO `tbl_barang` (`id_barang`, `nama_barang`, `jenis`, `stok_minimum`, `stok`, `satuan`, `foto`, `rak_id`, `id_cabang`) VALUES
('B0001', 'Air Freshener Matic Spray', 11, 1, 111, 7, NULL, '1', 1),
('B0002', 'Floor Cleaner', 11, 0, 111, 7, NULL, '2', 1),
('B0003', 'Karbol Wangi', 11, 0, 111, 7, NULL, '3', 1),
('B0004', 'Hand Soap', 11, 0, 111, 7, NULL, '4', 1),
('B0005', 'Glass Cleaner', 11, 0, 111, 7, NULL, '5', 1),
('B0006', 'SAPU LIDI TAMAN', 13, 0, 111, 7, NULL, '6', 1),
('B0007', 'KOP TOILET ( WC ) SUPER MERK NAGATA', 13, 0, 111, 7, NULL, '7', 1),
('B0008', 'DUSTING MOP STRING SUPER COTTING I', 13, 0, 111, 7, NULL, '8', 1),
('B0009', 'AMPLAS HALUS NO. 500', 13, 0, 111, 7, NULL, '9', 1),
('B0010', 'EMBER PLASTIK 4 GL (20 LTR)', 13, 0, 111, 7, NULL, '10', 1),
('B0011', 'Tissue Kotak', 14, 0, 111, 7, NULL, '11', 1),
('B0012', 'Tissue Roll', 14, 0, 111, 7, NULL, '12', 1),
('B0013', 'Tissue Roll Jumbo', 14, 0, 111, 7, NULL, '13', 1),
('B0014', 'Tissue Hand Towel', 14, 0, 111, 7, NULL, '14', 1),
('B0015', 'Air Freshener Matic Spray', 11, 1, 187, 7, NULL, '1', 2),
('B0016', 'Floor Cleaner', 11, 0, 187, 7, NULL, '2', 2),
('B0017', 'Karbol Wangi', 11, 0, 187, 7, NULL, '3', 2),
('B0018', 'Hand Soap', 11, 0, 187, 7, NULL, '4', 2),
('B0019', 'Glass Cleaner', 11, 0, 187, 7, NULL, '5', 2),
('B0020', 'SAPU LIDI TAMAN', 13, 0, 187, 7, NULL, '6', 2),
('B0021', 'KOP TOILET ( WC ) SUPER MERK NAGATA', 13, 0, 187, 7, NULL, '7', 2),
('B0022', 'DUSTING MOP STRING SUPER COTTING I', 13, 0, 187, 7, NULL, '8', 2),
('B0023', 'AMPLAS HALUS NO. 500', 13, 0, 187, 7, NULL, '9', 2),
('B0024', 'EMBER PLASTIK 4 GL (20 LTR)', 13, 0, 187, 7, NULL, '10', 2),
('B0025', 'Tissue Kotak', 14, 0, 187, 7, NULL, '11', 2),
('B0026', 'Tissue Roll', 14, 0, 187, 7, NULL, '12', 2),
('B0027', 'Tissue Roll Jumbo', 14, 0, 187, 7, NULL, '13', 2),
('B0028', 'Tissue Hand Towel', 14, 0, 187, 7, NULL, '14', 2),
('B0029', 'Air Freshener Matic Spray', 11, 1, 85, 7, NULL, '1', 4),
('B0030', 'Floor Cleaner', 11, 0, 85, 7, NULL, '2', 4),
('B0031', 'Karbol Wangi', 11, 0, 85, 7, NULL, '3', 4),
('B0032', 'Hand Soap', 11, 0, 85, 7, NULL, '4', 4),
('B0033', 'Glass Cleaner', 11, 0, 85, 7, NULL, '5', 4),
('B0034', 'SAPU LIDI TAMAN', 13, 0, 85, 7, NULL, '6', 4),
('B0035', 'KOP TOILET ( WC ) SUPER MERK NAGATA', 13, 0, 85, 7, NULL, '7', 4),
('B0036', 'DUSTING MOP STRING SUPER COTTING I', 13, 0, 85, 7, NULL, '8', 4),
('B0037', 'AMPLAS HALUS NO. 500', 13, 0, 85, 7, NULL, '9', 4),
('B0038', 'EMBER PLASTIK 4 GL (20 LTR)', 13, 0, 85, 7, NULL, '10', 4),
('B0039', 'Tissue Kotak', 14, 0, 85, 7, NULL, '11', 4),
('B0040', 'Tissue Roll', 14, 0, 85, 7, NULL, '12', 4),
('B0041', 'Tissue Roll Jumbo', 14, 0, 85, 7, NULL, '13', 4),
('B0042', 'Tissue Hand Towel', 14, 0, 85, 7, NULL, '14', 4),
('B0043', 'Air Freshener Matic Spray', 11, 1, 97, 7, NULL, '1', 5),
('B0044', 'Floor Cleaner', 11, 0, 97, 7, NULL, '2', 5),
('B0045', 'Karbol Wangi', 11, 0, 97, 7, NULL, '3', 5),
('B0046', 'Hand Soap', 11, 0, 97, 7, NULL, '4', 5),
('B0047', 'Glass Cleaner', 11, 0, 97, 7, NULL, '5', 5),
('B0048', 'SAPU LIDI TAMAN', 13, 0, 97, 7, NULL, '6', 5),
('B0049', 'KOP TOILET ( WC ) SUPER MERK NAGATA', 13, 0, 97, 7, NULL, '7', 5),
('B0050', 'DUSTING MOP STRING SUPER COTTING I', 13, 0, 97, 7, NULL, '8', 5),
('B0051', 'AMPLAS HALUS NO. 500', 13, 0, 97, 7, NULL, '9', 5),
('B0052', 'EMBER PLASTIK 4 GL (20 LTR)', 13, 0, 97, 7, NULL, '10', 5),
('B0053', 'Tissue Kotak', 14, 0, 97, 7, NULL, '11', 5),
('B0054', 'Tissue Roll', 14, 0, 97, 7, NULL, '12', 5),
('B0055', 'Tissue Roll Jumbo', 14, 0, 97, 7, NULL, '13', 5),
('B0056', 'Tissue Hand Towel', 14, 0, 97, 7, NULL, '14', 5),
('B0057', 'Air Freshener Matic Spray', 11, 1, 44, 7, NULL, '1', 6),
('B0058', 'Floor Cleaner', 11, 0, 44, 7, NULL, '2', 6),
('B0059', 'Karbol Wangi', 11, 0, 44, 7, NULL, '3', 6),
('B0060', 'Hand Soap', 11, 0, 44, 7, NULL, '4', 6),
('B0061', 'Glass Cleaner', 11, 0, 44, 7, NULL, '5', 6),
('B0062', 'SAPU LIDI TAMAN', 13, 0, 44, 7, NULL, '6', 6),
('B0063', 'KOP TOILET ( WC ) SUPER MERK NAGATA', 13, 0, 44, 7, NULL, '7', 6),
('B0064', 'DUSTING MOP STRING SUPER COTTING I', 13, 0, 44, 7, NULL, '8', 6),
('B0065', 'AMPLAS HALUS NO. 500', 13, 0, 44, 7, NULL, '9', 6),
('B0066', 'EMBER PLASTIK 4 GL (20 LTR)', 13, 0, 44, 7, NULL, '10', 6),
('B0067', 'Tissue Kotak', 14, 0, 44, 7, NULL, '11', 6),
('B0068', 'Tissue Roll', 14, 0, 44, 7, NULL, '12', 6),
('B0069', 'Tissue Roll Jumbo', 14, 0, 44, 7, NULL, '13', 6),
('B0070', 'Tissue Hand Towel', 14, 0, 44, 7, NULL, '14', 6);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_barang_keluar`
--

CREATE TABLE `tbl_barang_keluar` (
  `id_transaksi` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `barang` varchar(5) NOT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `nama_lokasi` varchar(255) DEFAULT NULL,
  `id_cabang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Trigger `tbl_barang_keluar`
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
-- Struktur dari tabel `tbl_barang_masuk`
--

CREATE TABLE `tbl_barang_masuk` (
  `id_transaksi` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `barang` varchar(5) NOT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `id_satuan` int(11) DEFAULT NULL,
  `id_cabang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_barang_masuk`
--

INSERT INTO `tbl_barang_masuk` (`id_transaksi`, `tanggal`, `barang`, `jumlah`, `id_satuan`, `id_cabang`) VALUES
('TM-0000001', '2023-10-25', 'B0001', 111, 7, 1),
('TM-0000002', '2023-10-25', 'B0002', 111, 7, 1),
('TM-0000003', '2023-10-25', 'B0003', 111, 7, 1),
('TM-0000004', '2023-10-25', 'B0004', 111, 7, 1),
('TM-0000005', '2023-10-25', 'B0005', 111, 7, 1),
('TM-0000006', '2023-10-25', 'B0006', 111, 7, 1),
('TM-0000007', '2023-10-25', 'B0007', 111, 7, 1),
('TM-0000008', '2023-10-25', 'B0008', 111, 7, 1),
('TM-0000009', '2023-10-25', 'B0009', 111, 7, 1),
('TM-0000010', '2023-10-25', 'B0010', 111, 7, 1),
('TM-0000011', '2023-10-25', 'B0011', 111, 7, 1),
('TM-0000012', '2023-10-25', 'B0013', 111, 7, 1),
('TM-0000013', '2023-10-25', 'B0012', 111, 7, 1),
('TM-0000014', '2023-10-25', 'B0014', 111, 7, 1),
('TM-0000015', '2023-10-25', 'B0015', 20, 7, 2),
('TM-0000016', '2023-10-25', 'B0016', 20, 7, 2),
('TM-0000017', '2023-10-25', 'B0017', 20, 7, 2),
('TM-0000018', '2023-10-25', 'B0018', 20, 7, 2),
('TM-0000019', '2023-10-25', 'B0019', 20, 7, 2),
('TM-0000020', '2023-10-25', 'B0020', 20, 7, 2),
('TM-0000021', '2023-10-25', 'B0021', 20, 7, 2),
('TM-0000022', '2023-10-25', 'B0022', 20, 7, 2),
('TM-0000023', '2023-10-25', 'B0023', 20, 7, 2),
('TM-0000024', '2023-10-25', 'B0024', 20, 7, 2),
('TM-0000025', '2023-10-25', 'B0025', 20, 7, 2),
('TM-0000026', '2023-10-25', 'B0026', 20, 7, 2),
('TM-0000027', '2023-10-25', 'B0027', 20, 7, 2),
('TM-0000028', '2023-10-25', 'B0028', 20, 7, 2),
('TM-0000029', '2023-10-25', 'B0029', 111, 7, 4),
('TM-0000030', '2023-10-25', 'B0030', 111, 7, 4),
('TM-0000031', '2023-10-25', 'B0031', 111, 7, 4),
('TM-0000032', '2023-10-25', 'B0032', 111, 7, 4),
('TM-0000033', '2023-10-25', 'B0033', 111, 7, 4),
('TM-0000034', '2023-10-25', 'B0034', 111, 7, 4),
('TM-0000035', '2023-10-25', 'B0035', 111, 7, 4),
('TM-0000036', '2023-10-25', 'B0036', 111, 7, 4),
('TM-0000037', '2023-10-25', 'B0037', 111, 7, 4),
('TM-0000038', '2023-10-25', 'B0038', 111, 7, 4),
('TM-0000039', '2023-10-25', 'B0039', 111, 7, 4),
('TM-0000040', '2023-10-25', 'B0040', 111, 7, 4),
('TM-0000041', '2023-10-25', 'B0041', 111, 7, 4),
('TM-0000042', '2023-10-25', 'B0042', 111, 7, 4),
('TM-0000043', '2023-10-25', 'B0043', 111, 7, 5),
('TM-0000044', '2023-10-25', 'B0044', 111, 7, 5),
('TM-0000045', '2023-10-25', 'B0045', 111, 7, 5),
('TM-0000046', '2023-10-25', 'B0046', 111, 7, 5),
('TM-0000047', '2023-10-25', 'B0047', 111, 7, 5),
('TM-0000048', '2023-10-25', 'B0048', 111, 7, 5),
('TM-0000049', '2023-10-25', 'B0049', 111, 7, 5),
('TM-0000050', '2023-10-25', 'B0050', 111, 7, 5),
('TM-0000051', '2023-10-25', 'B0051', 111, 7, 5),
('TM-0000052', '2023-10-25', 'B0052', 111, 7, 5),
('TM-0000053', '2023-10-25', 'B0053', 111, 7, 5),
('TM-0000054', '2023-10-25', 'B0054', 111, 7, 5),
('TM-0000055', '2023-10-25', 'B0055', 111, 7, 5),
('TM-0000056', '2023-10-25', 'B0056', 111, 7, 5),
('TM-0000057', '2023-10-25', 'B0057', 20, 7, 6),
('TM-0000058', '2023-10-25', 'B0058', 20, 7, 6),
('TM-0000059', '2023-10-25', 'B0059', 20, 7, 6),
('TM-0000060', '2023-10-25', 'B0060', 20, 7, 6),
('TM-0000061', '2023-10-25', 'B0061', 20, 7, 6),
('TM-0000062', '2023-10-25', 'B0062', 20, 7, 6),
('TM-0000063', '2023-10-25', 'B0063', 20, 7, 6),
('TM-0000064', '2023-10-25', 'B0064', 20, 7, 6),
('TM-0000065', '2023-10-25', 'B0065', 20, 7, 6),
('TM-0000066', '2023-10-25', 'B0066', 20, 7, 6),
('TM-0000067', '2023-10-25', 'B0067', 20, 7, 6),
('TM-0000068', '2023-10-25', 'B0068', 20, 7, 6),
('TM-0000069', '2023-10-25', 'B0069', 20, 7, 6),
('TM-0000070', '2023-10-25', 'B0070', 20, 7, 6);

--
-- Trigger `tbl_barang_masuk`
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
-- Struktur dari tabel `tbl_cabang`
--

CREATE TABLE `tbl_cabang` (
  `id_cabang` int(11) NOT NULL,
  `nama_cabang` varchar(255) NOT NULL,
  `nama_fm` varchar(255) DEFAULT NULL,
  `nomor_telepon` varchar(15) DEFAULT NULL,
  `id_teritory` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_cabang`
--

INSERT INTO `tbl_cabang` (`id_cabang`, `nama_cabang`, `nama_fm`, `nomor_telepon`, `id_teritory`) VALUES
(1, 'Jakarta Timur - Bekasi', 'Bpk. Sahudin', '123', NULL),
(2, 'Jakarta Selatan - Bogor', 'Bpk Noordian Yanuar Tesar', '123', NULL),
(4, 'Jakarta Barat - Utara', 'Bpk Drajat', '-', NULL),
(5, 'Tanggerang - Banten', 'Bpk Angga', '-', NULL),
(6, 'Jakarta Pusat - MM', 'Bpk Santo', '-', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_jenis`
--

CREATE TABLE `tbl_jenis` (
  `id_jenis` int(11) NOT NULL,
  `nama_jenis` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_jenis`
--

INSERT INTO `tbl_jenis` (`id_jenis`, `nama_jenis`) VALUES
(11, 'Chemical'),
(13, 'Peralatan Kerja'),
(14, 'Tissue');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_lokasi_sto`
--

CREATE TABLE `tbl_lokasi_sto` (
  `id_lokasi` int(11) NOT NULL,
  `nama_lokasi` varchar(255) DEFAULT NULL,
  `kode_wilayah` varchar(255) DEFAULT NULL,
  `id_teritory` int(11) DEFAULT NULL,
  `id_cabang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_lokasi_sto`
--

INSERT INTO `tbl_lokasi_sto` (`id_lokasi`, `nama_lokasi`, `kode_wilayah`, `id_teritory`, `id_cabang`) VALUES
(31, 'STO PONDOK GEDE', 'Bekasi Kota', 1, 1),
(32, 'STO KRANJI', 'Bekasi Kota', 1, 1),
(33, 'STO BEKASI', 'Bekasi Kota', 1, 1),
(34, 'STO CIBITUNG', 'Bekasi Kota', 1, 1),
(35, 'STO BANTAR GEBANG', 'Bekasi Kota', 1, 1),
(36, 'STO KALI ABANG', 'Bekasi Utara', 1, 1),
(37, 'STO MUARA GEMBONG', 'Bekasi Utara', 1, 1),
(38, 'STO BABELAN', 'Bekasi Utara', 1, 1),
(39, 'STO CIBARUSAH', 'Bekasi Selatan', 1, 1),
(40, 'STO CIKARANG', 'Bekasi Selatan', 1, 1),
(41, 'STO SUKARESMI', 'Bekasi Selatan', 1, 1),
(45, 'STO PALMERAH', 'Area 1', 4, 4),
(46, 'STO SLIPI', 'Area 1', 4, 4),
(47, 'STO KEDOYA', 'Area 1', 4, 4),
(48, 'STO MERUYA', 'Area 1', 4, 4),
(49, 'STO CEMPAKA PUTIH', 'Area 1', 6, 6),
(50, 'STO RAWA MANGUN', 'Area 1', 6, 6),
(51, 'STO PRAMUKA', 'Area 1', 6, 6);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_satuan`
--

CREATE TABLE `tbl_satuan` (
  `id_satuan` int(11) NOT NULL,
  `nama_satuan` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_satuan`
--

INSERT INTO `tbl_satuan` (`id_satuan`, `nama_satuan`) VALUES
(7, 'BUAH');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_transfer_stok`
--

CREATE TABLE `tbl_transfer_stok` (
  `id_transfer` int(11) NOT NULL,
  `tanggal_transfer` date NOT NULL,
  `id_barang` varchar(50) NOT NULL,
  `jumlah_transfer` int(11) NOT NULL,
  `id_cabang_asal` int(11) NOT NULL,
  `id_cabang_tujuan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hak_akses` enum('Administrator','Admin Gudang','Kepala Gudang','Supplier') NOT NULL,
  `id_cabang` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `nama_user`, `username`, `password`, `hak_akses`, `id_cabang`) VALUES
(14, 'Bpk. Sahudin', 'Jaktim', '$2y$12$Z5BWtQC2ydkPLKjVM3PPP.HkCr5Ou01XZGmq2IF9qe1W4vUZ/.MJa', 'Admin Gudang', 1),
(15, 'Bpk. Tesar', 'Jaksel', '$2y$12$CGLKdYIQ4wp2YkyDXCULfedCZpppf/rFXY9b6SH393ktWtthJ5LKq', 'Admin Gudang', 2),
(16, 'Bpk. Santo', 'Jakpus', '$2y$12$zjiTDftmFtoLthTBG9NPEuQFls2UTNb/4PEO2Bxa0gK21qUdcKuOy', 'Admin Gudang', 6),
(17, 'Bpk. Drajat', 'Jakbar', '$2y$12$Vmikz/NMGwKkezoYQ5i/HOJYDBpYeD9QeWn/ct8VaiRVRQ2punix.', 'Admin Gudang', 4),
(18, 'Bpk Angga', 'Tgr', '$2y$12$X1jBsXgt2A.7a320pHWj9ecLjkCgrqTQhLlwqYbcwJwsHYowq41QS', 'Admin Gudang', 5),
(19, 'Regional', 'Admin', '$2y$12$w8WUhk.Qowq8B8TANI6AiO.iCwZ09LlcMOvyZ0Ki/vDrIrV3VcOXu', 'Administrator', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_wilayah`
--

CREATE TABLE `tbl_wilayah` (
  `id_wilayah` int(11) NOT NULL,
  `kode_wilayah` varchar(255) NOT NULL,
  `id_cabang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_wilayah`
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
-- Struktur dari tabel `temp_barang`
--

CREATE TABLE `temp_barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jenis_barang` varchar(100) NOT NULL,
  `id_jenis` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `temp_barang`
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
-- Struktur dari tabel `teritory_manager`
--

CREATE TABLE `teritory_manager` (
  `id_teritory` int(11) NOT NULL,
  `teritory_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `teritory_manager`
--

INSERT INTO `teritory_manager` (`id_teritory`, `teritory_name`) VALUES
(1, 'Jakarta Timur - Bekasi');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `adjustment`
--
ALTER TABLE `adjustment`
  ADD PRIMARY KEY (`id_adjustment`),
  ADD KEY `fk_adjustment_barang` (`id_barang`);

--
-- Indeks untuk tabel `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`no_rak`);

--
-- Indeks untuk tabel `tbl_barang`
--
ALTER TABLE `tbl_barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `fk_rak` (`rak_id`);

--
-- Indeks untuk tabel `tbl_barang_keluar`
--
ALTER TABLE `tbl_barang_keluar`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indeks untuk tabel `tbl_barang_masuk`
--
ALTER TABLE `tbl_barang_masuk`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indeks untuk tabel `tbl_cabang`
--
ALTER TABLE `tbl_cabang`
  ADD PRIMARY KEY (`id_cabang`);

--
-- Indeks untuk tabel `tbl_jenis`
--
ALTER TABLE `tbl_jenis`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indeks untuk tabel `tbl_lokasi_sto`
--
ALTER TABLE `tbl_lokasi_sto`
  ADD PRIMARY KEY (`id_lokasi`),
  ADD KEY `id_teritory` (`id_teritory`);

--
-- Indeks untuk tabel `tbl_satuan`
--
ALTER TABLE `tbl_satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indeks untuk tabel `tbl_transfer_stok`
--
ALTER TABLE `tbl_transfer_stok`
  ADD PRIMARY KEY (`id_transfer`);

--
-- Indeks untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `fk_cabang` (`id_cabang`);

--
-- Indeks untuk tabel `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  ADD PRIMARY KEY (`id_wilayah`),
  ADD KEY `id_cabang` (`id_cabang`);

--
-- Indeks untuk tabel `temp_barang`
--
ALTER TABLE `temp_barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jenis` (`id_jenis`);

--
-- Indeks untuk tabel `teritory_manager`
--
ALTER TABLE `teritory_manager`
  ADD PRIMARY KEY (`id_teritory`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `adjustment`
--
ALTER TABLE `adjustment`
  MODIFY `id_adjustment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tbl_cabang`
--
ALTER TABLE `tbl_cabang`
  MODIFY `id_cabang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tbl_jenis`
--
ALTER TABLE `tbl_jenis`
  MODIFY `id_jenis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tbl_lokasi_sto`
--
ALTER TABLE `tbl_lokasi_sto`
  MODIFY `id_lokasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `tbl_satuan`
--
ALTER TABLE `tbl_satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tbl_transfer_stok`
--
ALTER TABLE `tbl_transfer_stok`
  MODIFY `id_transfer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  MODIFY `id_wilayah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `temp_barang`
--
ALTER TABLE `temp_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT untuk tabel `teritory_manager`
--
ALTER TABLE `teritory_manager`
  MODIFY `id_teritory` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `adjustment`
--
ALTER TABLE `adjustment`
  ADD CONSTRAINT `fk_adjustment_barang` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD CONSTRAINT `fk_cabang` FOREIGN KEY (`id_cabang`) REFERENCES `tbl_cabang` (`id_cabang`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_wilayah`
--
ALTER TABLE `tbl_wilayah`
  ADD CONSTRAINT `tbl_wilayah_ibfk_1` FOREIGN KEY (`id_cabang`) REFERENCES `tbl_cabang` (`id_cabang`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `temp_barang`
--
ALTER TABLE `temp_barang`
  ADD CONSTRAINT `temp_barang_ibfk_1` FOREIGN KEY (`id_jenis`) REFERENCES `tbl_jenis` (`id_jenis`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
