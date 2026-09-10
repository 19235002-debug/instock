<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  header('location: ../../login.php?pesan=2');
  exit;
} else {
  require_once "../../config/database.php";
  require_once "../../helper/approval_helper.php";
  require_once "../../helper/audit_helper.php";

  initApprovalColumns($mysqli);

  if (isset($_POST['simpan'])) {
    $id_transaksi   = mysqli_real_escape_string($mysqli, trim($_POST['id_transaksi']));
    $no_surat_jalan = mysqli_real_escape_string($mysqli, trim($_POST['no_surat_jalan'] ?? ''));
    $supplier       = mysqli_real_escape_string($mysqli, trim($_POST['supplier'] ?? ''));
    $tanggal        = mysqli_real_escape_string($mysqli, trim($_POST['tanggal']));
    $id_cabang      = (int)($_POST['id_cabang'] ?? $_SESSION['id_cabang'] ?? 0);
    $catatan        = mysqli_real_escape_string($mysqli, trim($_POST['catatan'] ?? ''));

    $tanggal_masuk  = date('Y-m-d', strtotime($tanggal));
    $role           = $_SESSION['hak_akses'] ?? 'Unknown';
    $user_name      = $_SESSION['nama_user'] ?? $_SESSION['username'] ?? 'User';

    // Arrays dari form
    $barang_arr     = $_POST['barang'] ?? [];
    $jumlah_arr     = $_POST['jumlah'] ?? [];
    $satuan_arr     = $_POST['satuan'] ?? [];
    $no_batch_arr   = $_POST['no_batch'] ?? [];
    $exp_date_arr   = $_POST['expired_date'] ?? [];

    if (empty($barang_arr) || count($barang_arr) === 0) {
      header('location: ../../main.php?module=barang_masuk&pesan=2');
      exit;
    }

    $is_approved = ($role === 'Super Admin' || $role === 'Administrator' || $role === 'Kepala Gudang');
    $status      = $is_approved ? 'Approved' : 'Pending';

    $approved_by_val = $is_approved ? "'$user_name'" : "NULL";
    $approved_at_val = $is_approved ? "NOW()" : "NULL";

    // First item for legacy single-item column fallback
    $first_barang = mysqli_real_escape_string($mysqli, $barang_arr[0] ?? '');
    $first_jumlah = (int)($jumlah_arr[0] ?? 0);
    $first_satuan = (int)($satuan_arr[0] ?? 0);

    // Insert Header into tbl_barang_masuk
    $qHeader = "INSERT INTO tbl_barang_masuk (id_transaksi, no_surat_jalan, supplier, tanggal, barang, jumlah, id_satuan, id_cabang, status, created_by_role, approved_by, approved_at, catatan)
                VALUES ('$id_transaksi', '$no_surat_jalan', '$supplier', '$tanggal_masuk', '$first_barang', $first_jumlah, $first_satuan, $id_cabang, '$status', '$role', $approved_by_val, $approved_at_val, '$catatan')";

    $insertHeader = mysqli_query($mysqli, $qHeader) or die('Error inserting header: ' . mysqli_error($mysqli));

    if ($insertHeader) {
      $totalItemsProcessed = 0;
      $totalQtyProcessed   = 0;

      for ($i = 0; $i < count($barang_arr); $i++) {
        $id_barang = mysqli_real_escape_string($mysqli, $barang_arr[$i]);
        $jumlah    = (int)($jumlah_arr[$i] ?? 0);
        $id_satuan = (int)($satuan_arr[$i] ?? 0);
        $no_batch  = mysqli_real_escape_string($mysqli, !empty($no_batch_arr[$i]) ? trim($no_batch_arr[$i]) : ('BATCH-' . $id_transaksi . '-' . ($i + 1)));

        $exp_raw   = trim($exp_date_arr[$i] ?? '');
        $exp_date_val = !empty($exp_raw) ? "'" . date('Y-m-d', strtotime($exp_raw)) . "'" : "NULL";

        // Get default rak_id of this barang
        $q_rak = mysqli_query($mysqli, "SELECT rak_id FROM tbl_barang WHERE id_barang = '$id_barang'");
        $r_rak = mysqli_fetch_assoc($q_rak);
        $rak_id = mysqli_real_escape_string($mysqli, $r_rak['rak_id'] ?? '');

        // 1. Insert into tbl_barang_masuk_detail
        $qDet = "INSERT INTO tbl_barang_masuk_detail (id_transaksi, id_barang, jumlah, id_satuan, no_batch, expired_date, rak_id)
                 VALUES ('$id_transaksi', '$id_barang', $jumlah, $id_satuan, '$no_batch', $exp_date_val, '$rak_id')";
        mysqli_query($mysqli, $qDet) or die('Error inserting detail item: ' . mysqli_error($mysqli));

        if ($is_approved) {
          // 2. Update Master Stock in tbl_barang
          mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok + $jumlah WHERE id_barang = '$id_barang'");

          // 3. Add record into tbl_stok_batch
          $qBatch = "INSERT INTO tbl_stok_batch (id_transaksi, id_barang, id_cabang, no_batch, tanggal_masuk, expired_date, stok_awal, stok_sisa, rak_id)
                     VALUES ('$id_transaksi', '$id_barang', $id_cabang, '$no_batch', '$tanggal_masuk', $exp_date_val, $jumlah, $jumlah, '$rak_id')";
          mysqli_query($mysqli, $qBatch);
        }

        $totalItemsProcessed++;
        $totalQtyProcessed += $jumlah;
      }

      $actionLog = $is_approved ? 'Barang Masuk (Approved)' : 'Pengajuan Barang Masuk (Pending)';
      $descLog   = "No SJ: $no_surat_jalan | ID: $id_transaksi ($totalItemsProcessed item, Total Qty: $totalQtyProcessed) oleh $role";
      logActivity($mysqli, $actionLog, 'barang_masuk', $descLog);

      header('location: ../../main.php?module=barang_masuk&pesan=1');
      exit;
    }
  }
}
