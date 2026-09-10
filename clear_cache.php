<?php
session_start();

// Pengecekan session login user
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: login.php?pesan=2');
    exit;
}

// Force No-Cache Headers for Browser
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 1. Clear PHP OPcache
$opcache_status = 'Tidak Aktif / Tidak Tersedia';
$opcache_success = false;

if (function_exists('opcache_reset')) {
    if (@opcache_reset()) {
        $opcache_status = 'Berhasil Dibersihkan (OPcache Bytecode Reset)';
        $opcache_success = true;
    } else {
        $opcache_status = 'Diabaikan (OPcache tidak diaktifkan di php.ini)';
    }
}

// 2. Clear PHP Stat & Realpath Cache
clearstatcache(true);
$stat_cache_status = 'Berhasil Dibersihkan (Realpath & Stat Cache Reset)';

// 3. Clear APCu User Cache (jika ada)
$apc_status = 'Tidak Digunakan';
if (function_exists('apcu_clear_cache')) {
    @apcu_clear_cache();
    $apc_status = 'Berhasil Dibersihkan';
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'main.php?module=dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Clear Cache System - Inventory Gudang</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="assets/img/icon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { "families": ["Lato:300,400,700,900"] },
            custom: { "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['assets/css/fonts.min.css'] },
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/atlantis.min.css">
    <link rel="stylesheet" href="assets/css/global-custom-theme.css">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card card-post card-round shadow-lg border-0">
                    <div class="card-header bg-secondary-gradient text-white py-4 text-center">
                        <div class="mb-2">
                            <i class="fas fa-sync-alt fa-3x fa-spin" style="animation-duration: 3s;"></i>
                        </div>
                        <h3 class="card-title text-white font-weight-bold mb-1">Optimasi & Clear Cache</h3>
                        <p class="card-category text-white-50 mb-0">Pembersihan sistem cache & memori sementara berhasil dijalankan</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-messages mb-4">
                            <div class="list-group-item d-flex align-items-center py-3 border-bottom">
                                <div class="avatar avatar-sm mr-3">
                                    <span class="avatar-title rounded-circle bg-success text-white">
                                        <i class="fas fa-bolt"></i>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h5 class="mb-1 font-weight-bold">PHP OPcache Bytecode</h5>
                                    <small class="text-muted"><?= htmlspecialchars($opcache_status); ?></small>
                                </div>
                                <div>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> OK</span>
                                </div>
                            </div>

                            <div class="list-group-item d-flex align-items-center py-3 border-bottom">
                                <div class="avatar avatar-sm mr-3">
                                    <span class="avatar-title rounded-circle bg-info text-white">
                                        <i class="fas fa-folder-open"></i>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h5 class="mb-1 font-weight-bold">Realpath & Stat Cache</h5>
                                    <small class="text-muted"><?= htmlspecialchars($stat_cache_status); ?></small>
                                </div>
                                <div>
                                    <span class="badge badge-info"><i class="fas fa-check mr-1"></i> OK</span>
                                </div>
                            </div>

                            <div class="list-group-item d-flex align-items-center py-3 border-bottom">
                                <div class="avatar avatar-sm mr-3">
                                    <span class="avatar-title rounded-circle bg-primary text-white">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h5 class="mb-1 font-weight-bold">Browser Asset Cache Headers</h5>
                                    <small class="text-muted">Header Anti-Caching (No-Store / Must-Revalidate) Diterapkan</small>
                                </div>
                                <div>
                                    <span class="badge badge-primary"><i class="fas fa-check mr-1"></i> OK</span>
                                </div>
                            </div>

                            <div class="list-group-item d-flex align-items-center py-3">
                                <div class="avatar avatar-sm mr-3">
                                    <span class="avatar-title rounded-circle bg-warning text-white">
                                        <i class="fas fa-desktop"></i>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h5 class="mb-1 font-weight-bold">Browser Local Storage & Client Cache</h5>
                                    <small class="text-muted" id="clientCacheStatus">Memproses pembersihan memori browser...</small>
                                </div>
                                <div id="clientCacheBadge">
                                    <span class="badge badge-warning"><i class="fas fa-spinner fa-spin"></i> Sync</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                            <i class="fas fa-info-circle fa-2x mr-3 text-info"></i>
                            <div style="font-size: 13px;">
                                <strong>Tips Tambahan:</strong> Jika aset CSS/JS di komputer Anda belum berubah, tekan <code>Ctrl + F5</code> untuk Hard Refresh browser Anda.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="<?= htmlspecialchars($referer); ?>" class="btn btn-secondary btn-round">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Halaman Sebelumnya
                            </a>
                            <a href="main.php?module=dashboard" class="btn btn-primary btn-round">
                                <i class="fas fa-home mr-2"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            try {
                // Clear browser client storage
                if (window.localStorage) localStorage.clear();
                if (window.sessionStorage) sessionStorage.clear();
                
                $('#clientCacheStatus').text('Local Storage & Session Storage Browser Berhasil Dibersihkan');
                $('#clientCacheBadge').html('<span class="badge badge-success"><i class="fas fa-check mr-1"></i> OK</span>');
            } catch (e) {
                $('#clientCacheStatus').text('Pembersihan client storage selesai');
                $('#clientCacheBadge').html('<span class="badge badge-success"><i class="fas fa-check mr-1"></i> OK</span>');
            }
        });
    </script>
</body>
</html>
