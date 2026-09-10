<?php
require_once __DIR__ . '/../config/database.php';

echo "=== MEMULAI PEMBERSIHAN / RESET DATABASE ===\n\n";

mysqli_query($mysqli, "SET FOREIGN_KEY_CHECKS = 0;");

$tablesToTruncate = [
    'adjustment',
    'tbl_barang_masuk',
    'tbl_barang_masuk_detail',
    'tbl_barang_keluar',
    'tbl_stok_batch',
    'tbl_transfer_stok',
    'tbl_audit_log',
    'tbl_barang',
    'tbl_jenis',
    'tbl_satuan',
    'tbl_cabang',
    'tbl_lokasi_sto',
    'location',
    'temp_barang'
];

foreach ($tablesToTruncate as $tb) {
    $chk = mysqli_query($mysqli, "SHOW TABLES LIKE '$tb'");
    if ($chk && mysqli_num_rows($chk) > 0) {
        if (mysqli_query($mysqli, "TRUNCATE TABLE `$tb`")) {
            echo "[OK] Tabel `$tb` berhasil dikosongkan.\n";
        } else {
            echo "[ERROR] Gagal mengosongkan `$tb`: " . mysqli_error($mysqli) . "\n";
        }
    }
}

// Hapus User Non-Admin (Hanya sisakan Administrator)
$delUser = mysqli_query($mysqli, "DELETE FROM `tbl_user` WHERE `hak_akses` != 'Administrator' AND `username` != 'admin' AND `username` != 'Admin'");
if ($delUser) {
    echo "[OK] Tabel `tbl_user` berhasil dibersihkan (Hanya akun Administrator yang disisakan).\n";
}

// Tambah 1 cabang default jika tbl_cabang memiliki kolom nama_cabang
$chkCab = mysqli_query($mysqli, "SELECT COUNT(*) as c FROM `tbl_cabang`");
if ($chkCab) {
    $rowCab = mysqli_fetch_assoc($chkCab);
    if ($rowCab['c'] == 0) {
        @mysqli_query($mysqli, "INSERT INTO `tbl_cabang` (`id_cabang`, `nama_cabang`) VALUES (1, 'Gudang Utama (Pusat)')");
        echo "[INFO] Menambahkan 1 Cabang Default 'Gudang Utama (Pusat)' untuk kemudahan simulasi awal.\n";
    }
}

mysqli_query($mysqli, "SET FOREIGN_KEY_CHECKS = 1;");

echo "\n=== RESET DATABASE SELESAI ===\n";
echo "Semua data transaksi, barang, jenis, satuan, rak, dan user biasa telah dikosongkan.\n";
echo "Akun Administrator tetap aktif dan siap digunakan untuk simulasi input data dari awal.\n";
