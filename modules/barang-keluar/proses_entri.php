<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  header('location: ../../login.php?pesan=2');
} else {
  require_once "../../config/database.php";
  require_once "../../helper/approval_helper.php";
  require_once "../../helper/audit_helper.php";

  initApprovalColumns($mysqli);

  if (isset($_POST['simpan'])) {
    $id_transaksi  = mysqli_real_escape_string($mysqli, $_POST['id_transaksi']);
    $tanggal       = mysqli_real_escape_string($mysqli, trim($_POST['tanggal']));
    $barang        = mysqli_real_escape_string($mysqli, $_POST['barang'] ?? '');
    $jumlah        = (int)($_POST['jumlah'] ?? 0);
    $lokasi_sto    = mysqli_real_escape_string($mysqli, $_POST['lokasi_sto'] ?? '');
    $id_cabang     = mysqli_real_escape_string($mysqli, $_POST['id_cabang'] ?? 0);

    if (empty($barang) || $jumlah <= 0 || empty($tanggal)) {
      header('location: ../../main.php?module=form_entri_barang_keluar&pesan=3');
      exit;
    }

    $tanggal_keluar = date('Y-m-d', strtotime($tanggal));
    $role           = $_SESSION['hak_akses'] ?? 'Unknown';
    $user_name      = $_SESSION['nama_user'] ?? $_SESSION['username'] ?? 'User';

    // Cek kecukupan stok saat ini
    $cek_stok = mysqli_query($mysqli, "SELECT stok FROM tbl_barang WHERE id_barang = '$barang'");
    $stok_sekarang = mysqli_fetch_assoc($cek_stok)['stok'] ?? 0;

    if ($jumlah > $stok_sekarang) {
      header('location: ../../main.php?module=form_entri_barang_keluar&pesan=3');
      exit;
    }

    if ($role === 'Super Admin' || $role === 'Administrator' || $role === 'Kepala Gudang') {
      $status = 'Approved';
      $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang_keluar(id_transaksi, tanggal, barang, jumlah, nama_lokasi, id_cabang, status, created_by_role, approved_by, approved_at) 
                                  VALUES('$id_transaksi', '$tanggal_keluar', '$barang', '$jumlah', '$lokasi_sto', '$id_cabang', '$status', '$role', '$user_name', NOW())")
        or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));

      if ($insert) {
        mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok - $jumlah WHERE id_barang = '$barang'");
        logActivity($mysqli, 'Barang Keluar (Approved)', 'barang_keluar', "ID: $id_transaksi ($jumlah unit dari $barang) oleh $role");
        header('location: ../../main.php?module=barang_keluar&pesan=1');
      }
    } else {
      $status = 'Pending';
      $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang_keluar(id_transaksi, tanggal, barang, jumlah, nama_lokasi, id_cabang, status, created_by_role) 
                                  VALUES('$id_transaksi', '$tanggal_keluar', '$barang', '$jumlah', '$lokasi_sto', '$id_cabang', '$status', '$role')")
        or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));

      if ($insert) {
        logActivity($mysqli, 'Pengajuan Barang Keluar (Pending)', 'barang_keluar', "ID: $id_transaksi ($jumlah unit dari $barang) oleh $role - Menunggu Persetujuan");
        header('location: ../../main.php?module=barang_keluar&pesan=1');
      }
    }
  }
}
