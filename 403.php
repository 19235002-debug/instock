<?php
if (session_status() == PHP_SESSION_NONE) {
  @session_start();
}
$sys_setting = [];
if (file_exists(__DIR__ . "/config/database.php") && file_exists(__DIR__ . "/helper/setting_helper.php")) {
  require_once __DIR__ . "/config/database.php";
  require_once __DIR__ . "/helper/setting_helper.php";
  if (isset($mysqli) && $mysqli) {
    $sys_setting = getSystemSetting($mysqli);
  }
}

$nama_aplikasi = !empty($sys_setting['nama_aplikasi']) ? htmlspecialchars($sys_setting['nama_aplikasi']) : 'INSTOCK WMS';
$logo = !empty($sys_setting['logo']) ? htmlspecialchars($sys_setting['logo']) : 'assets/img/logo.png';
$favicon = !empty($sys_setting['favicon']) ? htmlspecialchars($sys_setting['favicon']) : 'assets/img/favicon.png';
$fav_v = md5($favicon . ($sys_setting['updated_at'] ?? ''));

$is_embedded = (basename($_SERVER['PHP_SELF']) === 'main.php') || isset($module);

if ($is_embedded): ?>
  <div class="page-inner">
    <div class="row justify-content-center py-4">
      <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-lg text-center p-4" style="border-radius: 18px; background: #ffffff;">
          <div class="card-body">
            <div class="mb-3">
              <span class="badge badge-danger px-3 py-2 font-weight-bold" style="border-radius: 20px; font-size: 0.85rem; letter-spacing: 0.5px;">
                ERROR 403
              </span>
            </div>
            <div class="mb-3">
              <div style="width: 76px; height: 76px; border-radius: 50%; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.15); display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-lock text-danger" style="font-size: 2.2rem;"></i>
              </div>
            </div>
            <h3 class="font-weight-bold text-dark mb-2" style="letter-spacing: -0.3px;">Akses Ditolak</h3>
            <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6; max-width: 440px; margin: 0 auto;">
              Maaf, akun Anda tidak memiliki hak akses untuk membuka halaman ini. Silakan hubungi <strong>Super Admin</strong> jika Anda memerlukan akses khusus.
            </p>
            <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 10px;">
              <a href="?module=dashboard" class="btn btn-primary btn-round font-weight-bold px-4 py-2 shadow-sm">
                <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
              </a>
              <button onclick="window.history.back()" class="btn btn-light btn-round font-weight-bold border px-4 py-2">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php else: ?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Aplikasi Persediaan Barang Gudang Material - <?= $nama_aplikasi ?>" />

  <!-- Title -->
  <title>403 Akses Ditolak - <?= $nama_aplikasi ?></title>

  <!-- Favicon icon -->
  <link rel="icon" href="<?= $favicon ?>?v=<?= $fav_v ?>" type="image/x-icon" />
  <link rel="shortcut icon" href="<?= $favicon ?>?v=<?= $fav_v ?>" type="image/x-icon" />

  <!-- CSS Files -->
  <link rel="stylesheet" href="assets/css/fonts.min.css">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/atlantis.min.css">
  <link rel="stylesheet" href="assets/css/global-custom-theme.css">

  <style>
    html, body {
      height: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      background: #f4f6f9 !important;
    }

    body.error-body {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      min-height: 100vh !important;
      padding: 1.5rem !important;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }

    .error-wrapper-box {
      width: 100%;
      max-width: 480px;
      margin: auto;
    }

    .error-card-main {
      background: #ffffff;
      border-radius: 20px !important;
      border: 1px solid #e2e8f0 !important;
      box-shadow: 0 20px 40px -15px rgba(21, 114, 232, 0.15) !important;
      padding: 2.75rem 2.25rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .error-card-main::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #ef4444 0%, #f59e0b 100%);
    }

    .error-logo-img {
      max-width: 180px;
      max-height: 55px;
      object-fit: contain;
      margin-bottom: 1.75rem;
    }

    .error-icon-circle {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: rgba(239, 68, 68, 0.08);
      border: 1px solid rgba(239, 68, 68, 0.15);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.25rem auto;
    }

    .error-icon-circle i {
      font-size: 2.25rem;
      color: #ef4444;
    }

    .error-badge-tag {
      display: inline-block;
      background: #fef2f2;
      color: #dc2626;
      font-weight: 800;
      font-size: 0.8rem;
      letter-spacing: 1px;
      padding: 4px 14px;
      border-radius: 20px;
      margin-bottom: 0.75rem;
      border: 1px solid #fecaca;
    }

    .error-title-text {
      font-size: 1.5rem;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 0.5rem;
      letter-spacing: -0.3px;
    }

    .error-desc-text {
      font-size: 0.9rem;
      color: #64748b;
      line-height: 1.6;
      margin-bottom: 1.75rem;
      padding: 0 0.5rem;
    }

    .error-btn-group {
      display: flex;
      gap: 10px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-gradient-dashboard {
      background: linear-gradient(135deg, #1572e8 0%, #064397 100%) !important;
      color: #ffffff !important;
      border: none !important;
      border-radius: 30px !important;
      padding: 10px 22px !important;
      font-size: 0.875rem !important;
      font-weight: 700 !important;
      box-shadow: 0 4px 12px rgba(21, 114, 232, 0.25) !important;
      transition: all 0.2s ease !important;
      text-decoration: none !important;
      display: inline-flex;
      align-items: center;
    }

    .btn-gradient-dashboard:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(21, 114, 232, 0.35) !important;
    }

    .btn-outline-back {
      background: #f8fafc !important;
      color: #475569 !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 30px !important;
      padding: 10px 20px !important;
      font-size: 0.875rem !important;
      font-weight: 600 !important;
      transition: all 0.2s ease !important;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
    }

    .btn-outline-back:hover {
      background: #f1f5f9 !important;
      color: #1e293b !important;
    }

    .error-footer-copy {
      margin-top: 1.75rem;
      font-size: 0.8rem;
      color: #94a3b8;
    }
  </style>
</head>

<body class="error-body">
  <div class="error-wrapper-box animated fadeIn">
    <div class="error-card-main">
      <!-- Logo Aplikasi -->
      <?php if (!empty($logo)): ?>
        <div>
          <img src="<?= $logo ?>" alt="<?= $nama_aplikasi ?>" class="error-logo-img">
        </div>
      <?php endif; ?>

      <!-- Icon Shield/Lock -->
      <div class="error-icon-circle">
        <i class="fas fa-user-lock"></i>
      </div>

      <!-- Badge status -->
      <div>
        <span class="error-badge-tag">ERROR 403</span>
      </div>

      <!-- Title & Content -->
      <h2 class="error-title-text">Akses Ditolak</h2>
      <p class="error-desc-text">
        Maaf, akun Anda tidak memiliki hak akses untuk membuka halaman ini. Silakan hubungi <strong>Super Admin</strong> jika Anda memerlukan akses khusus.
      </p>

      <!-- Action Buttons -->
      <div class="error-btn-group">
        <a href="main.php" class="btn-gradient-dashboard">
          <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
        </a>
        <button onclick="window.history.back()" class="btn-outline-back">
          <i class="fas fa-arrow-left mr-2"></i> Kembali
        </button>
      </div>

      <!-- Footer -->
      <div class="error-footer-copy">
        Copyright &copy; <?= date('Y') ?> <strong class="text-primary"><?= $nama_aplikasi ?></strong>. All rights reserved.
      </div>
    </div>
  </div>

  <!-- Core JS -->
  <script src="assets/js/core/jquery.3.2.1.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
</body>

</html>
<?php endif; ?>
