<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Nonaktifkan display_errors agar error/notice PHP tidak merusak stream binary PDF
error_reporting(0);
ini_set('display_errors', 0);

// Include autoloader untuk load dompdf
require_once __DIR__ . "/../../assets/js/plugin/dompdf/autoload.inc.php";
use Dompdf\Dompdf;

// Pengecekan session login user 
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  header('location: ../../login.php?pesan=2');
  exit;
} else {
  // Panggil file koneksi database dan helper
  require_once __DIR__ . "/../../config/database.php";
  require_once __DIR__ . "/../../helper/fungsi_tanggal_indo.php";
  require_once __DIR__ . "/../../helper/cabang_helper.php";
  require_once __DIR__ . "/../../helper/setting_helper.php";

  // Ambil parameter tanggal dari GET
  $tanggal_awal  = $_GET['tanggal_awal'] ?? date('Y-m-d');
  $tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');

  $tgl_awal_db  = date('Y-m-d', strtotime($tanggal_awal));
  $tgl_akhir_db = date('Y-m-d', strtotime($tanggal_akhir));

  $active_cabang_id = getActiveCabangId();
  $active_cabang_name = getActiveCabangName($mysqli);

  // Ambil pengaturan sistem & logo
  $setting = getSystemSetting($mysqli);
  $nama_perusahaan   = $setting['nama_perusahaan'] ?? 'PT Instock Fulfillment Indonesia';
  $slogan_perusahaan = $setting['slogan_aplikasi'] ?? 'Fulfillment Warehouse Management System';
  $alamat_perusahaan = $setting['alamat_perusahaan'] ?? 'Jl. Jendral Sudirman No. 123, Jakarta';
  $telepon_perusahaan= $setting['telepon'] ?? '0812-3456-7890';
  $email_perusahaan  = $setting['email'] ?? 'admin@instock.co.id';

  if ($active_cabang_id > 0) {
    $q_cb = mysqli_query($mysqli, "SELECT * FROM tbl_cabang WHERE id_cabang='$active_cabang_id'");
    if ($d_cb = mysqli_fetch_assoc($q_cb)) {
      if (!empty($d_cb['nama_cabang'])) {
        $nama_perusahaan .= ' - ' . $d_cb['nama_cabang'];
      }
      if (!empty($d_cb['alamat'])) {
        $alamat_perusahaan = $d_cb['alamat'];
      }
      if (!empty($d_cb['nomor_telepon'])) {
        $telepon_perusahaan = $d_cb['nomor_telepon'];
      }
      if (!empty($d_cb['email'])) {
        $email_perusahaan = $d_cb['email'];
      }
    }
  }

  // Base64 logo gambar agar kompatibel 100% dengan Dompdf
  $logo_path = __DIR__ . '/../../' . ($setting['logo'] ?? 'assets/img/logo.png');
  if (!file_exists($logo_path) || is_dir($logo_path)) {
    $logo_path = __DIR__ . '/../../assets/img/logo.png';
  }
  $logo_src = '';
  if (file_exists($logo_path) && !is_dir($logo_path)) {
    $logo_data = base64_encode(file_get_contents($logo_path));
    $logo_src = 'data:image/png;base64,' . $logo_data;
  }

  // Gunakan Class Dompdf
  $dompdf = new Dompdf();
  $options = $dompdf->getOptions();
  $options->setIsRemoteEnabled(true);
  $options->setChroot(realpath(__DIR__ . '/../../'));
  $dompdf->setOptions($options);

  // SQL Query Data Barang Keluar
  $where_c = ($active_cabang_id === 0) ? "" : " AND (a.id_cabang = '$active_cabang_id' OR b.id_cabang = '$active_cabang_id')";
  $query_keluar = mysqli_query($mysqli, "SELECT a.id_transaksi, a.tanggal, a.barang, a.jumlah, a.nama_lokasi, b.nama_barang, c.nama_satuan, d.nama_cabang
                                         FROM tbl_barang_keluar as a 
                                         INNER JOIN tbl_barang as b ON a.barang=b.id_barang 
                                         LEFT JOIN tbl_satuan as c ON b.satuan=c.id_satuan
                                         LEFT JOIN tbl_cabang as d ON a.id_cabang=d.id_cabang
                                         WHERE a.tanggal BETWEEN '$tgl_awal_db' AND '$tgl_akhir_db' $where_c 
                                         ORDER BY a.id_transaksi ASC")
                                         or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

  $page_title = "LAPORAN DATA PENGELUARAN BARANG KELUAR";
  $pdf_filename = "Laporan_Barang_Keluar_" . date('Ymd_His') . ".pdf";

  // Format HTML Template Laporan
  $html = '<!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($page_title) . '</title>
    <style>
      @page {
        margin: 30px 40px 40px 40px;
      }
      body {
        font-family: "Helvetica", "Arial", sans-serif;
        color: #0f172a;
        font-size: 10px;
        line-height: 1.4;
      }
      /* Kop Surat Header */
      .kop-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
      }
      .kop-table td {
        vertical-align: middle;
      }
      .company-title {
        font-size: 16px;
        font-weight: bold;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
      }
      .company-slogan {
        font-size: 9.5px;
        color: #475569;
        font-style: italic;
        margin-top: 1px;
      }
      .company-contact {
        font-size: 8.5px;
        color: #64748b;
        margin-top: 2px;
      }
      .kop-line {
        border: none;
        border-top: 3px solid #0f172a;
        border-bottom: 1px solid #cbd5e1;
        height: 3px;
        margin-bottom: 14px;
      }
      /* Title Box */
      .title-box {
        text-align: center;
        margin-bottom: 14px;
      }
      .report-title {
        font-size: 14px;
        font-weight: bold;
        color: #1e1b4b;
        text-transform: uppercase;
        letter-spacing: 0.8px;
      }
      .report-meta {
        font-size: 9px;
        color: #64748b;
        margin-top: 3px;
      }
      /* Table Styling */
      .table-data {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
      }
      .table-data th {
        background-color: #1e293b;
        color: #ffffff;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        padding: 7px 6px;
        border: 1px solid #334155;
        text-align: center;
      }
      .table-data td {
        padding: 6px;
        font-size: 9.5px;
        border: 1px solid #cbd5e1;
        vertical-align: middle;
      }
      .table-data tr:nth-child(even) {
        background-color: #f8fafc;
      }
      .text-center { text-align: center; }
      .text-right { text-align: right; }
      .text-left { text-align: left; }
      .font-bold { font-weight: bold; }
      
      /* Summary Box */
      .summary-table {
        width: 100%;
        margin-top: 12px;
        border-collapse: collapse;
      }
      .summary-table td {
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 6px 10px;
        font-size: 9px;
      }
      /* Signature Block */
      .signature-table {
        width: 100%;
        margin-top: 25px;
        border-collapse: collapse;
      }
      .signature-table td {
        text-align: center;
        vertical-align: top;
      }
    </style>
  </head>
  <body>

    <!-- KOP SURAT BERLOGO -->
    <table class="kop-table">
      <tr>
        <td width="75" class="text-center" style="padding-right: 12px;">';
          if (!empty($logo_src)) {
            $html .= '<img src="' . $logo_src . '" style="max-height: 55px; max-width: 75px; object-fit: contain;">';
          } else {
            $html .= '<div style="font-size: 16px; font-weight: bold; color: #2563eb;">INSTOCK</div>';
          }
  $html .= '</td>
        <td>
          <div class="company-title">' . htmlspecialchars($nama_perusahaan) . '</div>
          <div class="company-slogan">' . htmlspecialchars($slogan_perusahaan) . '</div>
          <div class="company-contact">Alamat: ' . htmlspecialchars($alamat_perusahaan) . ' | Telp: ' . htmlspecialchars($telepon_perusahaan) . ' | Email: ' . htmlspecialchars($email_perusahaan) . '</div>
        </td>
      </tr>
    </table>
    <div class="kop-line"></div>

    <!-- REPORT TITLE & METADATA -->
    <div class="title-box">
      <div class="report-title">' . htmlspecialchars($page_title) . '</div>
      <div class="report-meta">
        Periode: <strong>' . tanggal_indo($tgl_awal_db) . '</strong> s.d. <strong>' . tanggal_indo($tgl_akhir_db) . '</strong> &nbsp;|&nbsp; 
        Cabang / Area: <strong>' . htmlspecialchars($active_cabang_name) . '</strong> &nbsp;|&nbsp; 
        Dicetak Oleh: <strong>' . htmlspecialchars($_SESSION['nama_user'] ?? $_SESSION['username']) . '</strong>
      </div>
    </div>

    <!-- TABLE DATA BARANG KELUAR -->
    <table class="table-data">
      <thead>
        <tr>
          <th width="4%">No.</th>
          <th width="14%">ID Transaksi</th>
          <th width="12%">Tanggal Keluar</th>
          <th width="12%">ID Barang</th>
          <th width="26%">Nama Barang</th>
          <th width="16%">Tujuan / Lokasi STO</th>
          <th width="16%">Jumlah Keluar</th>
        </tr>
      </thead>
      <tbody>';

  $no = 1;
  $total_transaksi = 0;
  $total_jumlah_keluar = 0;

  if (mysqli_num_rows($query_keluar) > 0) {
    while ($r = mysqli_fetch_assoc($query_keluar)) {
      $total_transaksi++;
      $jumlah_val = (int)$r['jumlah'];
      $total_jumlah_keluar += $jumlah_val;

      $html .= '<tr>
        <td class="text-center">' . $no++ . '</td>
        <td class="text-center font-bold">' . htmlspecialchars($r['id_transaksi']) . '</td>
        <td class="text-center">' . date('d-m-Y', strtotime($r['tanggal'])) . '</td>
        <td class="text-center font-bold">' . htmlspecialchars($r['barang']) . '</td>
        <td>' . htmlspecialchars($r['nama_barang']) . '</td>
        <td>' . htmlspecialchars($r['nama_lokasi'] ?? '-') . '</td>
        <td class="text-right font-bold" style="color: #b91c1c;">-' . number_format($jumlah_val, 0, '', '.') . ' ' . htmlspecialchars($r['nama_satuan'] ?? '') . '</td>
      </tr>';
    }
  } else {
    $html .= '<tr><td colspan="7" class="text-center" style="padding: 15px; color: #64748b;">Tidak ada data pengeluaran barang keluar pada periode ini.</td></tr>';
  }

  $html .= '</tbody>
    </table>

    <!-- SUMMARY BAR -->
    <table class="summary-table">
      <tr>
        <td width="50%">Total Transaksi Barang Keluar: <strong>' . number_format($total_transaksi) . ' Transaksi</strong></td>
        <td width="50%" class="text-right">Total Akumulasi Barang Keluar: <strong style="color: #b91c1c;">-' . number_format($total_jumlah_keluar) . ' Unit</strong></td>
      </tr>
    </table>

    <!-- SIGNATURE BLOCK -->
    <table class="signature-table">
      <tr>
        <td width="60%"></td>
        <td width="40%">
          <div>' . htmlspecialchars($active_cabang_name) . ', ' . tanggal_indo(date('Y-m-d')) . '</div>
          <div style="font-weight: bold; margin-top: 2px;">Kepala Gudang / Penanggung Jawab</div>
          <div style="height: 50px;"></div>
          <div style="font-weight: bold; text-decoration: underline;">' . htmlspecialchars($_SESSION['nama_user'] ?? $_SESSION['username']) . '</div>
          <div style="font-size: 8.5px; color: #64748b;">(' . htmlspecialchars($_SESSION['hak_akses'] ?? 'Administrator') . ')</div>
        </td>
      </tr>
    </table>

  </body>
  </html>';

  // Load HTML ke Dompdf
  $dompdf->loadHtml($html);
  $dompdf->setPaper('A4', 'landscape');
  $dompdf->render();

  // Clean output buffer sebelum streaming PDF
  if (ob_get_length()) {
    ob_clean();
  }
  $dompdf->stream($pdf_filename, array('Attachment' => 0));
}
