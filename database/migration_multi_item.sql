-- SQL Migration for Multi-Item Batching Barang Masuk & Batch Stock Tracking

-- 1. Tambah Kolom Pendukung di tbl_barang_masuk jika belum ada
ALTER TABLE `tbl_barang_masuk` 
  ADD COLUMN IF NOT EXISTS `no_surat_jalan` VARCHAR(100) NULL AFTER `id_transaksi`,
  ADD COLUMN IF NOT EXISTS `supplier` VARCHAR(100) NULL AFTER `no_surat_jalan`,
  ADD COLUMN IF NOT EXISTS `catatan` TEXT NULL AFTER `catatan_approval`;

-- 2. Buat Tabel Detail Barang Masuk (tbl_barang_masuk_detail)
CREATE TABLE IF NOT EXISTS `tbl_barang_masuk_detail` (
  `id_detail` INT NOT NULL AUTO_INCREMENT,
  `id_transaksi` VARCHAR(50) NOT NULL,
  `id_barang` VARCHAR(10) NOT NULL,
  `jumlah` INT NOT NULL DEFAULT 0,
  `id_satuan` INT NULL,
  `no_batch` VARCHAR(50) NULL,
  `expired_date` DATE NULL,
  `rak_id` VARCHAR(255) NULL,
  PRIMARY KEY (`id_detail`),
  KEY `idx_transaksi` (`id_transaksi`),
  KEY `idx_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 3. Buat Tabel Stok Batch (tbl_stok_batch) untuk FIFO/FEFO
CREATE TABLE IF NOT EXISTS `tbl_stok_batch` (
  `id_batch` INT NOT NULL AUTO_INCREMENT,
  `id_transaksi` VARCHAR(50) NOT NULL,
  `id_barang` VARCHAR(10) NOT NULL,
  `id_cabang` INT NULL,
  `no_batch` VARCHAR(50) NULL,
  `tanggal_masuk` DATE NOT NULL,
  `expired_date` DATE NULL,
  `stok_awal` INT NOT NULL DEFAULT 0,
  `stok_sisa` INT NOT NULL DEFAULT 0,
  `rak_id` VARCHAR(255) NULL,
  PRIMARY KEY (`id_batch`),
  KEY `idx_trans_barang` (`id_transaksi`, `id_barang`),
  KEY `idx_cabang_barang` (`id_cabang`, `id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 4. Migrasi Data Terdahulu (Single-Item Legacy) ke Detail
INSERT INTO `tbl_barang_masuk_detail` (`id_transaksi`, `id_barang`, `jumlah`, `id_satuan`, `no_batch`, `expired_date`, `rak_id`)
SELECT 
  bm.`id_transaksi`, 
  bm.`barang`, 
  bm.`jumlah`, 
  bm.`id_satuan`, 
  CONCAT('BATCH-', bm.`id_transaksi`), 
  NULL, 
  b.`rak_id`
FROM `tbl_barang_masuk` bm
LEFT JOIN `tbl_barang` b ON bm.`barang` = b.`id_barang`
WHERE bm.`barang` IS NOT NULL 
  AND bm.`barang` != '' 
  AND NOT EXISTS (
    SELECT 1 FROM `tbl_barang_masuk_detail` bmd WHERE bmd.`id_transaksi` = bm.`id_transaksi`
  );

-- 5. Migrasi Batch Stok untuk Transaksi Terdahulu yang Approved
INSERT INTO `tbl_stok_batch` (`id_transaksi`, `id_barang`, `id_cabang`, `no_batch`, `tanggal_masuk`, `expired_date`, `stok_awal`, `stok_sisa`, `rak_id`)
SELECT 
  bm.`id_transaksi`, 
  bm.`barang`, 
  bm.`id_cabang`, 
  CONCAT('BATCH-', bm.`id_transaksi`), 
  bm.`tanggal`, 
  NULL, 
  bm.`jumlah`, 
  bm.`jumlah`, 
  b.`rak_id`
FROM `tbl_barang_masuk` bm
LEFT JOIN `tbl_barang` b ON bm.`barang` = b.`id_barang`
WHERE bm.`status` = 'Approved' 
  AND bm.`barang` IS NOT NULL 
  AND bm.`barang` != ''
  AND NOT EXISTS (
    SELECT 1 FROM `tbl_stok_batch` sb WHERE sb.`id_transaksi` = bm.`id_transaksi` AND sb.`id_barang` = bm.`barang`
  );
