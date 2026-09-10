<?php
// Prevent direct access
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

require_once __DIR__ . "/audit_helper.php";

/**
 * Ensures status and approval tracking columns exist in transaction tables
 */
function initApprovalColumns($mysqli)
{
  $tables = ['tbl_barang_masuk', 'tbl_barang_keluar', 'adjustment'];

  foreach ($tables as $table) {
    // Check if status column exists
    $checkQuery = "SHOW COLUMNS FROM `$table` LIKE 'status'";
    $res = @mysqli_query($mysqli, $checkQuery);

    if ($res && mysqli_num_rows($res) === 0) {
      @mysqli_query($mysqli, "ALTER TABLE `$table` ADD COLUMN `status` ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Approved'");
      @mysqli_query($mysqli, "ALTER TABLE `$table` ADD COLUMN `created_by_role` VARCHAR(50) NULL DEFAULT 'Super Admin'");
      @mysqli_query($mysqli, "ALTER TABLE `$table` ADD COLUMN `approved_by` VARCHAR(100) NULL");
      @mysqli_query($mysqli, "ALTER TABLE `$table` ADD COLUMN `approved_at` DATETIME NULL");
      @mysqli_query($mysqli, "ALTER TABLE `$table` ADD COLUMN `catatan_approval` TEXT NULL");
    }
  }
}

/**
 * Approve a pending transaction and perform stock mutation
 */
function approveTransaction($mysqli, $type, $id_val, $approved_by, $catatan = '')
{
  initApprovalColumns($mysqli);

  $approved_by_clean = mysqli_real_escape_string($mysqli, $approved_by);
  $catatan_clean = mysqli_real_escape_string($mysqli, $catatan);
  $id_val_clean = mysqli_real_escape_string($mysqli, $id_val);

  if ($type === 'masuk') {
    $get = mysqli_query($mysqli, "SELECT id_transaksi, id_cabang, tanggal, status FROM tbl_barang_masuk WHERE id_transaksi = '$id_val_clean'");
    if ($row = mysqli_fetch_assoc($get)) {
      if ($row['status'] === 'Pending') {
        $id_cabang = (int)($row['id_cabang'] ?? 0);
        $tanggal_masuk = $row['tanggal'] ?? date('Y-m-d');

        // Fetch details from tbl_barang_masuk_detail
        $qDetail = mysqli_query($mysqli, "SELECT * FROM tbl_barang_masuk_detail WHERE id_transaksi = '$id_val_clean'");
        $totalItems = 0;

        while ($det = mysqli_fetch_assoc($qDetail)) {
          $id_barang = mysqli_real_escape_string($mysqli, $det['id_barang']);
          $jumlah = (int)$det['jumlah'];
          $no_batch = mysqli_real_escape_string($mysqli, !empty($det['no_batch']) ? $det['no_batch'] : ('BATCH-' . $id_val_clean));
          $exp_date = !empty($det['expired_date']) ? "'" . mysqli_real_escape_string($mysqli, $det['expired_date']) . "'" : "NULL";
          $rak_id = mysqli_real_escape_string($mysqli, $det['rak_id'] ?? '');

          // 1. Update master stock in tbl_barang
          mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok + $jumlah WHERE id_barang = '$id_barang'");

          // 2. Add batch record in tbl_stok_batch
          mysqli_query($mysqli, "INSERT INTO tbl_stok_batch (id_transaksi, id_barang, id_cabang, no_batch, tanggal_masuk, expired_date, stok_awal, stok_sisa, rak_id) 
                                 VALUES ('$id_val_clean', '$id_barang', $id_cabang, '$no_batch', '$tanggal_masuk', $exp_date, $jumlah, $jumlah, '$rak_id')");
          $totalItems++;
        }

        // 3. Update status in tbl_barang_masuk
        mysqli_query($mysqli, "UPDATE tbl_barang_masuk SET status = 'Approved', approved_by = '$approved_by_clean', approved_at = NOW(), catatan_approval = '$catatan_clean' WHERE id_transaksi = '$id_val_clean'");

        logActivity($mysqli, 'Persetujuan Barang Masuk', 'barang_masuk', "Disetujui ID: $id_val_clean ($totalItems item disetujui). Catatan: $catatan");
        return true;
      }
    }
  } elseif ($type === 'keluar') {
    $get = mysqli_query($mysqli, "SELECT barang, jumlah, status FROM tbl_barang_keluar WHERE id_transaksi = '$id_val_clean'");
    if ($row = mysqli_fetch_assoc($get)) {
      if ($row['status'] === 'Pending') {
        $id_barang = mysqli_real_escape_string($mysqli, $row['barang']);
        $jumlah = (int)$row['jumlah'];

        // 1. Update stock in tbl_barang
        mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok - $jumlah WHERE id_barang = '$id_barang'");

        // 2. Update status
        mysqli_query($mysqli, "UPDATE tbl_barang_keluar SET status = 'Approved', approved_by = '$approved_by_clean', approved_at = NOW(), catatan_approval = '$catatan_clean' WHERE id_transaksi = '$id_val_clean'");

        logActivity($mysqli, 'Persetujuan Barang Keluar', 'barang_keluar', "Disetujui ID: $id_val_clean ($jumlah unit). Catatan: $catatan");
        return true;
      }
    }
  } elseif ($type === 'adjustment') {
    $get = mysqli_query($mysqli, "SELECT id_barang, jumlah_adjustment, status FROM adjustment WHERE id_adjustment = '$id_val_clean'");
    if ($row = mysqli_fetch_assoc($get)) {
      if ($row['status'] === 'Pending') {
        $id_barang = mysqli_real_escape_string($mysqli, $row['id_barang']);
        $jumlah = (int)$row['jumlah_adjustment'];

        // 1. Update stock in tbl_barang to match adjustment value
        mysqli_query($mysqli, "UPDATE tbl_barang SET stok = $jumlah WHERE id_barang = '$id_barang'");

        // 2. Update status
        mysqli_query($mysqli, "UPDATE adjustment SET status = 'Approved', approved_by = '$approved_by_clean', approved_at = NOW(), catatan_approval = '$catatan_clean' WHERE id_adjustment = '$id_val_clean'");

        logActivity($mysqli, 'Persetujuan Adjustment', 'adjustment', "Disetujui ID Adjustment: $id_val_clean (Set Stok: $jumlah). Catatan: $catatan");
        return true;
      }
    }
  }

  return false;
}

/**
 * Reject a pending transaction
 */
function rejectTransaction($mysqli, $type, $id_val, $rejected_by, $catatan = '')
{
  initApprovalColumns($mysqli);

  $rejected_by_clean = mysqli_real_escape_string($mysqli, $rejected_by);
  $catatan_clean = mysqli_real_escape_string($mysqli, $catatan);
  $id_val_clean = mysqli_real_escape_string($mysqli, $id_val);

  $table = '';
  $id_col = '';
  $module = '';

  if ($type === 'masuk') {
    $table = 'tbl_barang_masuk';
    $id_col = 'id_transaksi';
    $module = 'barang_masuk';
  } elseif ($type === 'keluar') {
    $table = 'tbl_barang_keluar';
    $id_col = 'id_transaksi';
    $module = 'barang_keluar';
  } elseif ($type === 'adjustment') {
    $table = 'adjustment';
    $id_col = 'id_adjustment';
    $module = 'adjustment';
  }

  if ($table) {
    $query = "UPDATE `$table` SET status = 'Rejected', approved_by = '$rejected_by_clean', approved_at = NOW(), catatan_approval = '$catatan_clean' WHERE `$id_col` = '$id_val_clean' AND status = 'Pending'";
    if (mysqli_query($mysqli, $query)) {
      logActivity($mysqli, 'Penolakan Transaksi', $module, "Ditolak ID: $id_val_clean. Catatan: $catatan");
      return true;
    }
  }

  return false;
}
