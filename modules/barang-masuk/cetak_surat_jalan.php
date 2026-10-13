<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  header('location: ../../login.php?pesan=2');
  exit;
}

require_once "../../config/database.php";
require_once "../../helper/setting_helper.php";

$sys_setting = getSystemSetting($mysqli);
$id_transaksi = isset($_GET['id']) ? mysqli_real_escape_string($mysqli, $_GET['id']) : '';

if (empty($id_transaksi)) {
  die("ID Transaksi tidak valid.");
}

// Fetch Header Data
$qHeader = mysqli_query($mysqli, "
  SELECT 
    bm.*,
    cb.nama_cabang,
    cb.alamat AS alamat_cabang
  FROM tbl_barang_masuk bm
  LEFT JOIN tbl_cabang cb ON bm.id_cabang = cb.id_cabang
  WHERE bm.id_transaksi = '$id_transaksi'
");

$data = mysqli_fetch_assoc($qHeader);
if (!$data) {
  die("Data penerimaan barang tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Bukti Penerimaan Barang - <?= htmlspecialchars($data['id_transaksi']) ?></title>
  <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/css/fonts.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      color: #333;
      background: #fff;
      font-size: 13px;
    }
    .header-kop {
      border-bottom: 3px double #333;
      padding-bottom: 12px;
      margin-bottom: 20px;
    }
    .company-logo {
      max-height: 55px;
    }
    .table-detail th {
      background-color: #f1f3f5 !important;
      color: #000;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 11px;
    }
    .signature-box {
      margin-top: 50px;
    }
    .signature-line {
      border-bottom: 1px dashed #555;
      height: 60px;
      margin-bottom: 5px;
    }
    @media print {
      .no-print {
        display: none !important;
      }
      body {
        padding: 0;
        margin: 0;
      }
    }
  </style>
</head>
<body onload="window.print()">
  <div class="container py-4">

    <!-- Control Buttons -->
    <div class="no-print mb-4 d-flex justify-content-between">
      <button onclick="window.history.back()" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
      <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-print mr-1"></i> Cetak / Save PDF</button>
    </div>

    <!-- Kop Surat -->
    <div class="header-kop d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <img src="../../<?= getLogoAppPath($sys_setting) ?>" alt="Logo" class="company-logo mr-3">
        <div>
          <h4 class="font-weight-bold text-uppercase mb-0"><?= htmlspecialchars($sys_setting['nama_perusahaan'] ?: $sys_setting['nama_aplikasi']) ?></h4>
          <small class="text-muted"><?= htmlspecialchars($sys_setting['alamat_perusahaan'] ?: 'Sistem Manajemen Gudang Multi-Cabang') ?> | Telp: <?= htmlspecialchars($sys_setting['telepon_perusahaan'] ?: '-') ?></small>
        </div>
      </div>
      <div class="text-right">
        <h5 class="font-weight-bold text-primary mb-0">BUKTI PENERIMAAN BARANG</h5>
        <span class="badge badge-dark font-weight-bold px-2 py-1"><?= htmlspecialchars($data['id_transaksi']) ?></span>
      </div>
    </div>

    <!-- Informasi Surat Jalan -->
    <div class="row mb-4">
      <div class="col-6">
        <table class="table table-sm table-borderless">
          <tr>
            <td style="width: 35%;" class="font-weight-bold">No. Surat Jalan</td>
            <td>: <strong><?= htmlspecialchars($data['no_surat_jalan'] ?: '-') ?></strong></td>
          </tr>
          <tr>
            <td class="font-weight-bold">Supplier / Vendor</td>
            <td>: <?= htmlspecialchars($data['supplier'] ?: '-') ?></td>
          </tr>
          <tr>
            <td class="font-weight-bold">Status Penerimaan</td>
            <td>: <span class="badge badge-<?= ($data['status'] == 'Approved') ? 'success' : 'warning' ?>"><?= htmlspecialchars($data['status']) ?></span></td>
          </tr>
        </table>
      </div>
      <div class="col-6">
        <table class="table table-sm table-borderless">
          <tr>
            <td style="width: 35%;" class="font-weight-bold">Tanggal Terima</td>
            <td>: <?= date('d F Y', strtotime($data['tanggal'])) ?></td>
          </tr>
          <tr>
            <td class="font-weight-bold">Cabang Tujuan</td>
            <td>: <?= htmlspecialchars($data['nama_cabang'] ?: 'Gudang Utama') ?></td>
          </tr>
          <tr>
            <td class="font-weight-bold">Dicatat Oleh</td>
            <td>: <?= htmlspecialchars($data['approved_by'] ?: $data['created_by_role']) ?></td>
          </tr>
        </table>
      </div>
    </div>

    <!-- Tabel Detail Barang -->
    <table class="table table-bordered table-detail mb-4">
      <thead>
        <tr class="text-center">
          <th style="width: 5%;">No.</th>
          <th style="width: 15%;">Kode Barang</th>
          <th>Nama Barang</th>
          <th style="width: 12%;">Jumlah</th>
          <th style="width: 10%;">Satuan</th>
          <th style="width: 18%;">No. Batch / Lot</th>
          <th style="width: 15%;">Exp Date</th>
          <th style="width: 10%;">Rak</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $id_trans_curr = mysqli_real_escape_string($mysqli, $data['id_transaksi']);
        $q_items = mysqli_query($mysqli, "
          SELECT 
            bmd.*,
            b.nama_barang,
            s.nama_satuan,
            r.no_rak
          FROM tbl_barang_masuk_detail bmd
          LEFT JOIN tbl_barang b ON bmd.id_barang = b.id_barang
          LEFT JOIN tbl_satuan s ON bmd.id_satuan = s.id_satuan
          LEFT JOIN location r ON bmd.rak_id = r.id
          WHERE bmd.id_transaksi = '$id_trans_curr'
          ORDER BY bmd.id_detail ASC
        ");

        $no_item = 1;
        $grand_qty = 0;
        while ($row = mysqli_fetch_assoc($q_items)) {
          $grand_qty += (int)$row['jumlah'];
        ?>
          <tr>
            <td class="text-center align-middle"><?= $no_item++ ?></td>
            <td class="text-center align-middle font-weight-bold"><?= htmlspecialchars($row['id_barang']) ?></td>
            <td class="align-middle"><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td class="text-center align-middle font-weight-bold"><?= number_format($row['jumlah'], 0, '', '.') ?></td>
            <td class="text-center align-middle"><?= htmlspecialchars($row['nama_satuan'] ?: '-') ?></td>
            <td class="text-center align-middle font-weight-bold"><?= htmlspecialchars($row['no_batch'] ?: '-') ?></td>
            <td class="text-center align-middle">
              <?= !empty($row['expired_date']) ? date('d-m-Y', strtotime($row['expired_date'])) : '-' ?>
            </td>
            <td class="text-center align-middle"><?= htmlspecialchars($row['no_rak'] ?: 'JKT-01-01-A') ?></td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr class="font-weight-bold bg-light">
          <td colspan="3" class="text-right">TOTAL BARANG MASUK:</td>
          <td class="text-center text-primary"><?= number_format($grand_qty, 0, '', '.') ?></td>
          <td colspan="4"></td>
        </tr>
      </tfoot>
    </table>

    <?php if (!empty($data['catatan'])): ?>
      <div class="alert alert-light border mb-4">
        <strong>Catatan / Keterangan:</strong> <?= htmlspecialchars($data['catatan']) ?>
      </div>
    <?php endif; ?>

    <!-- Signature Boxes -->
    <div class="row signature-box text-center">
      <div class="col-4">
        <p class="mb-1 font-weight-bold">Pengirim / Supplier</p>
        <div class="signature-line"></div>
        <p class="mb-0 text-muted">( <?= htmlspecialchars($data['supplier'] ?: '.......................') ?> )</p>
      </div>
      <div class="col-4">
        <p class="mb-1 font-weight-bold">Penerima (Staff Gudang)</p>
        <div class="signature-line"></div>
        <p class="mb-0 text-muted">( ....................... )</p>
      </div>
      <div class="col-4">
        <p class="mb-1 font-weight-bold">Mengetahui (Kepala Gudang)</p>
        <div class="signature-line"></div>
        <p class="mb-0 text-muted">( ....................... )</p>
      </div>
    </div>

  </div>
</body>
</html>
