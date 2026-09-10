<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  header('location: ../../login.php?pesan=2');
  exit;
} else {
  $hak_akses = $_SESSION['hak_akses'];

  if ($hak_akses == 'Staff Gudang' || $hak_akses == 'Admin Gudang' || $hak_akses == 'Supplier') {
    header('location: ../../main.php?module=barang_masuk&pesan=3');
    exit;
  }

  require_once "../../config/database.php";
  require_once "../../helper/audit_helper.php";

  if (isset($_GET['id'])) {
    $id_transaksi = mysqli_real_escape_string($mysqli, $_GET['id']);

    // Check status of transaction
    $q_check = mysqli_query($mysqli, "SELECT status FROM tbl_barang_masuk WHERE id_transaksi = '$id_transaksi'");
    if ($row_check = mysqli_fetch_assoc($q_check)) {
      $status = $row_check['status'];

      // If Approved, rollback stock in tbl_barang
      if ($status === 'Approved') {
        $q_det = mysqli_query($mysqli, "SELECT id_barang, jumlah FROM tbl_barang_masuk_detail WHERE id_transaksi = '$id_transaksi'");
        while ($det = mysqli_fetch_assoc($q_det)) {
          $id_b = mysqli_real_escape_string($mysqli, $det['id_barang']);
          $qty  = (int)$det['jumlah'];
          mysqli_query($mysqli, "UPDATE tbl_barang SET stok = GREATEST(0, stok - $qty) WHERE id_barang = '$id_b'");
        }
      }

      // Delete from tbl_stok_batch
      mysqli_query($mysqli, "DELETE FROM tbl_stok_batch WHERE id_transaksi = '$id_transaksi'");

      // Delete from tbl_barang_masuk_detail
      mysqli_query($mysqli, "DELETE FROM tbl_barang_masuk_detail WHERE id_transaksi = '$id_transaksi'");

      // Delete from tbl_barang_masuk Header
      $delete = mysqli_query($mysqli, "DELETE FROM tbl_barang_masuk WHERE id_transaksi = '$id_transaksi'");

      if ($delete) {
        logActivity($mysqli, 'Hapus Barang Masuk', 'barang_masuk', "Menghapus pengiriman ID: $id_transaksi");
        header('location: ../../main.php?module=barang_masuk&pesan=2');
        exit;
      }
    }

    header('location: ../../main.php?module=barang_masuk&pesan=3');
  }
}
