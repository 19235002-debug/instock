<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  header('location: ../../login.php?pesan=2');
  exit();
}

require_once "../../config/database.php";
require_once "../../helper/approval_helper.php";
require_once "../../helper/audit_helper.php";

initApprovalColumns($mysqli);

if (isset($_POST['simpan'])) {
  $id_barang          = mysqli_real_escape_string($mysqli, $_POST['id_barang']);
  $tanggal_adjustment = mysqli_real_escape_string($mysqli, trim($_POST['tanggal_adjustment']));
  $jumlah_adjustment  = (int)($_POST['jumlah_adjustment'] ?? 0);
  $keterangan         = mysqli_real_escape_string($mysqli, $_POST['keterangan'] ?? '');

  $tanggal_adjustment = date('Y-m-d', strtotime($tanggal_adjustment));
  $role               = $_SESSION['hak_akses'] ?? 'Unknown';
  $user_name          = $_SESSION['nama_user'] ?? $_SESSION['username'] ?? 'User';

  if ($role === 'Super Admin' || $role === 'Administrator' || $role === 'Kepala Gudang') {
    $status = 'Approved';
    $query  = mysqli_query($mysqli, "INSERT INTO adjustment (id_barang, tanggal_adjustment, jumlah_adjustment, keterangan, status, created_by_role, approved_by, approved_at)
            VALUES ('$id_barang', '$tanggal_adjustment', '$jumlah_adjustment', '$keterangan', '$status', '$role', '$user_name', NOW())");

    if ($query) {
      mysqli_query($mysqli, "UPDATE tbl_barang SET stok = '$jumlah_adjustment' WHERE id_barang = '$id_barang'");
      logActivity($mysqli, 'Adjustment (Approved)', 'adjustment', "ID Barang: $id_barang (Set Stok: $jumlah_adjustment) oleh $role");
      header('location: ../../main.php?module=adjustment&pesan=1');
      exit();
    }
  } else {
    $status = 'Pending';
    $query  = mysqli_query($mysqli, "INSERT INTO adjustment (id_barang, tanggal_adjustment, jumlah_adjustment, keterangan, status, created_by_role)
            VALUES ('$id_barang', '$tanggal_adjustment', '$jumlah_adjustment', '$keterangan', '$status', '$role')");

    if ($query) {
      logActivity($mysqli, 'Pengajuan Adjustment (Pending)', 'adjustment', "ID Barang: $id_barang (Stok Disesuaikan: $jumlah_adjustment) oleh $role - Menunggu Persetujuan");
      header('location: ../../main.php?module=adjustment&pesan=1');
      exit();
    }
  }
}
