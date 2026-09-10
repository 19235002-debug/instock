<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

require_once "config/database.php";

// Ambil ID barang dari POST (array selected_items) atau GET (id)
$selected_ids = [];
if (isset($_POST['selected_items']) && is_array($_POST['selected_items'])) {
  $selected_ids = $_POST['selected_items'];
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
  $selected_ids = [$_GET['id']];
}

// Ambil data barang dari database
$items_data = [];
if (!empty($selected_ids)) {
  $escaped_ids = array_map(function ($id) use ($mysqli) {
    return "'" . mysqli_real_escape_string($mysqli, $id) . "'";
  }, $selected_ids);
  $in_clause = implode(',', $escaped_ids);
  $query = mysqli_query($mysqli, "SELECT b.*, j.nama_jenis, s.nama_satuan 
                                    FROM tbl_barang b 
                                    LEFT JOIN tbl_jenis j ON b.jenis = j.id_jenis 
                                    LEFT JOIN tbl_satuan s ON b.satuan = s.id_satuan 
                                    WHERE b.id_barang IN ($in_clause) 
                                    ORDER BY b.id_barang ASC");
  while ($r = mysqli_fetch_assoc($query)) {
    $items_data[] = $r;
  }
} else {
  // Default: Ambil data barang jika dibuka tanpa parameter
  $active_cabang_id = getActiveCabangId();
  $where_cb = ($active_cabang_id === 0) ? "" : "WHERE b.id_cabang = '$active_cabang_id'";
  $query = mysqli_query($mysqli, "SELECT b.*, j.nama_jenis, s.nama_satuan 
                                    FROM tbl_barang b 
                                    LEFT JOIN tbl_jenis j ON b.jenis = j.id_jenis 
                                    LEFT JOIN tbl_satuan s ON b.satuan = s.id_satuan 
                                    $where_cb
                                    ORDER BY b.id_barang ASC LIMIT 24");
  while ($r = mysqli_fetch_assoc($query)) {
    $items_data[] = $r;
  }
}
?>

<!-- Style Khusus Cetak Barcode & Sticker Layout -->
<style>
  /* Tampilan Layar (Screen View) */
  .sticker-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 20px;
    padding: 10px;
  }

  .sticker-card {
    background: #ffffff;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    position: relative;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    page-break-inside: avoid;
    break-inside: avoid;
  }

  .sticker-card:hover {
    border-color: #6366f1;
    box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1);
  }

  .sticker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 6px;
    margin-bottom: 10px;
  }

  .sticker-brand {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.5px;
    color: #475569;
    text-transform: uppercase;
  }

  .sticker-id-badge {
    background: #e2e8f0;
    color: #1e293b;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
  }

  .sticker-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .sticker-meta {
    font-size: 10px;
    color: #64748b;
    margin-bottom: 10px;
  }

  .barcode-wrapper {
    background: #fafafa;
    padding: 10px 6px;
    border-radius: 8px;
    border: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .barcode-img {
    max-width: 100%;
    height: 48px;
    object-fit: contain;
    image-rendering: pixelated;
  }

  .barcode-text {
    font-family: 'Courier New', Courier, monospace;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 2px;
    color: #1e293b;
    margin-top: 4px;
    margin-bottom: 0;
  }

  /* Styling khusus Cetak (Print View) */
  @media print {
    /* Sembunyikan semua elemen UI Atlantis */
    .sidebar,
    .main-header,
    .panel-header,
    .footer,
    .no-print,
    .navbar,
    .quick-sidebar {
      display: none !important;
    }

    .main-panel {
      width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    .content {
      padding: 0 !important;
      margin: 0 !important;
    }

    body {
      background: #ffffff !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    .page-inner {
      padding: 0 !important;
      margin: 0 !important;
    }

    .sticker-container {
      display: grid !important;
      grid-template-columns: repeat(3, 1fr) !important;
      gap: 12px !important;
      padding: 10px !important;
    }

    .sticker-card {
      border: 1px solid #000000 !important;
      box-shadow: none !important;
      padding: 10px !important;
      border-radius: 6px !important;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }

    .sticker-title {
      font-size: 12pt !important;
      color: #000 !important;
    }

    .barcode-img {
      height: 50px !important;
    }
  }
</style>

<!-- Header Halaman (No Print) -->
<div class="panel-header bg-primary-gradient no-print">
  <div class="page-inner py-4">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
      <div class="page-header text-white">
        <h2 class="text-white pb-2 fw-bold"><i class="fas fa-barcode mr-2"></i> Cetak Label Barcode Barang</h2>
        <ul class="breadcrumbs mt-2">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang" class="text-white">Master Barang</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item active"><span>Cetak Label</span></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <button onclick="window.print()" class="btn btn-success btn-round mr-2">
          <i class="fas fa-print mr-2"></i> Cetak Label (Print)
        </button>
        <a href="?module=barang" class="btn btn-white btn-border btn-round">
          <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--4">
  <!-- Toolbar Pengaturan Cetak (No Print) -->
  <div class="row no-print mb-3">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap">
          <div class="d-flex align-items-center">
            <span class="badge badge-primary font-weight-bold p-2 mr-3">
              <i class="fas fa-tags mr-1"></i> Total: <?= count($items_data) ?> Barang Terpilih
            </span>
            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Klik tombol <strong>Cetak Label</strong> atau gunakan pintasan <code>Ctrl + P</code> untuk mencetak ke printer label.</small>
          </div>
          <div>
            <button onclick="window.print()" class="btn btn-sm btn-primary">
              <i class="fas fa-print mr-1"></i> Print Sekarang
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Grid Preview Label Barcode -->
  <div class="card card-round">
    <div class="card-body">
      <?php if (empty($items_data)): ?>
        <div class="text-center py-5">
          <i class="fas fa-barcode fa-4x text-muted mb-3 d-block"></i>
          <h4 class="font-weight-bold text-dark">Tidak ada barang yang dipilih</h4>
          <p class="text-muted">Silakan pilih barang dari menu Data Barang terlebih dahulu untuk mencetak label.</p>
          <a href="?module=barang" class="btn btn-primary btn-round">
            <i class="fas fa-boxes mr-1"></i> Buka Data Barang
          </a>
        </div>
      <?php else: ?>
        <div class="sticker-container">
          <?php foreach ($items_data as $item): ?>
            <div class="sticker-card">
              <div class="sticker-header">
                <span class="sticker-brand"><i class="fas fa-warehouse mr-1"></i> INSTOCK WMS</span>
                <span class="sticker-id-badge"><?= htmlspecialchars($item['id_barang']) ?></span>
              </div>

              <div class="sticker-title" title="<?= htmlspecialchars($item['nama_barang']) ?>">
                <?= htmlspecialchars($item['nama_barang']) ?>
              </div>

              <div class="sticker-meta">
                <span><?= htmlspecialchars($item['nama_jenis'] ?? 'Umum') ?></span>
                <?php if (!empty($item['nama_satuan'])): ?>
                  &bull; <span><?= htmlspecialchars($item['nama_satuan']) ?></span>
                <?php endif; ?>
              </div>

              <div class="barcode-wrapper">
                <img class="barcode-img" src="generate_barcode.php?id=<?= urlencode($item['id_barang']) ?>" alt="Barcode <?= htmlspecialchars($item['id_barang']) ?>">
                <p class="barcode-text">*<?= htmlspecialchars($item['id_barang']) ?>*</p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
