<?php
require_once __DIR__ . '/../config/database.php';

echo "=== MENGUBAH NAMA ROLE DI DATABASE ===\n\n";

// 1. Change hak_akses to VARCHAR(50) first so values can be updated safely
mysqli_query($mysqli, "ALTER TABLE `tbl_user` MODIFY COLUMN `hak_akses` VARCHAR(50) NOT NULL");
echo "[OK] Changed column `hak_akses` to VARCHAR(50)\n";

// 2. Update Administrator -> Super Admin
$u1 = mysqli_query($mysqli, "UPDATE `tbl_user` SET `hak_akses` = 'Super Admin' WHERE `hak_akses` = 'Administrator'");
echo "[OK] Updated tbl_user Administrator -> Super Admin (" . mysqli_affected_rows($mysqli) . " rows)\n";

// 3. Update Admin Gudang -> Staff Gudang
$u2 = mysqli_query($mysqli, "UPDATE `tbl_user` SET `hak_akses` = 'Staff Gudang' WHERE `hak_akses` = 'Admin Gudang'");
echo "[OK] Updated tbl_user Admin Gudang -> Staff Gudang (" . mysqli_affected_rows($mysqli) . " rows)\n";

// 4. Change hak_akses to ENUM with new values
mysqli_query($mysqli, "ALTER TABLE `tbl_user` MODIFY COLUMN `hak_akses` ENUM('Super Admin', 'Staff Gudang', 'Kepala Gudang', 'Supplier') NOT NULL");
echo "[OK] Column `hak_akses` in tbl_user updated to ENUM('Super Admin', 'Staff Gudang', 'Kepala Gudang', 'Supplier')\n";

// 5. Update tbl_permission
mysqli_query($mysqli, "UPDATE `tbl_permission` SET `role` = 'Super Admin' WHERE `role` = 'Administrator'");
mysqli_query($mysqli, "UPDATE `tbl_permission` SET `role` = 'Staff Gudang' WHERE `role` = 'Admin Gudang'");
echo "[OK] Updated tbl_permission records.\n";

// 6. Update transaction created_by_role
mysqli_query($mysqli, "UPDATE `tbl_barang_masuk` SET `created_by_role` = 'Super Admin' WHERE `created_by_role` = 'Administrator'");
mysqli_query($mysqli, "UPDATE `tbl_barang_masuk` SET `created_by_role` = 'Staff Gudang' WHERE `created_by_role` = 'Admin Gudang'");

mysqli_query($mysqli, "UPDATE `tbl_barang_keluar` SET `created_by_role` = 'Super Admin' WHERE `created_by_role` = 'Administrator'");
mysqli_query($mysqli, "UPDATE `tbl_barang_keluar` SET `created_by_role` = 'Staff Gudang' WHERE `created_by_role` = 'Admin Gudang'");

mysqli_query($mysqli, "UPDATE `adjustment` SET `created_by_role` = 'Super Admin' WHERE `created_by_role` = 'Administrator'");
mysqli_query($mysqli, "UPDATE `adjustment` SET `created_by_role` = 'Staff Gudang' WHERE `created_by_role` = 'Admin Gudang'");

echo "\n=== RENAME ROLE DATABASE SELESAI ===\n";
