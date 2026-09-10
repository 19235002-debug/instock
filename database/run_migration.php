<?php
require_once __DIR__ . '/../config/database.php';

echo "Running Migration Multi-Item & Batching...\n";

// 1. Add columns to tbl_barang_masuk if missing
$columnsToAdd = [
    'no_surat_jalan' => "ALTER TABLE `tbl_barang_masuk` ADD COLUMN `no_surat_jalan` VARCHAR(100) NULL AFTER `id_transaksi`",
    'supplier'       => "ALTER TABLE `tbl_barang_masuk` ADD COLUMN `supplier` VARCHAR(100) NULL AFTER `no_surat_jalan`",
    'catatan'        => "ALTER TABLE `tbl_barang_masuk` ADD COLUMN `catatan` TEXT NULL"
];

foreach ($columnsToAdd as $col => $sqlAdd) {
    $check = mysqli_query($mysqli, "SHOW COLUMNS FROM `tbl_barang_masuk` LIKE '$col'");
    if ($check && mysqli_num_rows($check) === 0) {
        if (mysqli_query($mysqli, $sqlAdd)) {
            echo "Added column '$col' to tbl_barang_masuk\n";
        } else {
            echo "Error adding column '$col': " . mysqli_error($mysqli) . "\n";
        }
    }
}

// 2. Create tbl_barang_masuk_detail
$createDetailTable = "CREATE TABLE IF NOT EXISTS `tbl_barang_masuk_detail` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($mysqli, $createDetailTable)) {
    echo "Table tbl_barang_masuk_detail READY\n";
} else {
    echo "Error creating tbl_barang_masuk_detail: " . mysqli_error($mysqli) . "\n";
}

// 3. Create tbl_stok_batch
$createBatchTable = "CREATE TABLE IF NOT EXISTS `tbl_stok_batch` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($mysqli, $createBatchTable)) {
    echo "Table tbl_stok_batch READY\n";
} else {
    echo "Error creating tbl_stok_batch: " . mysqli_error($mysqli) . "\n";
}

// 4. Migrate Legacy Single Item rows to Detail Table
$migDetail = "INSERT INTO `tbl_barang_masuk_detail` (`id_transaksi`, `id_barang`, `jumlah`, `id_satuan`, `no_batch`, `expired_date`, `rak_id`)
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
  );";

if (mysqli_query($mysqli, $migDetail)) {
    echo "Migrated legacy items to tbl_barang_masuk_detail: " . mysqli_affected_rows($mysqli) . " rows\n";
}

// 5. Migrate Legacy Approved Barang Masuk to tbl_stok_batch
$migBatch = "INSERT INTO `tbl_stok_batch` (`id_transaksi`, `id_barang`, `id_cabang`, `no_batch`, `tanggal_masuk`, `expired_date`, `stok_awal`, `stok_sisa`, `rak_id`)
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
  );";

if (mysqli_query($mysqli, $migBatch)) {
    echo "Migrated legacy batch stock to tbl_stok_batch: " . mysqli_affected_rows($mysqli) . " rows\n";
}

echo "MIGRATION COMPLETE!\n";
