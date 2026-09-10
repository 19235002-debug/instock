<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

/**
 * Inisialisasi tabel tbl_setting hanya jika belum ada (Static Fast Check)
 */
function initSettingTable($mysqli)
{
  static $setting_initialized = false;
  if ($setting_initialized || !$mysqli) return;

  try {
    $check = @mysqli_query($mysqli, "SELECT 1 FROM tbl_setting LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
      $setting_initialized = true;
      return;
    }
  } catch (Throwable $e) {
    // Tabel belum ada, lanjut ke proses pembuatan
  }

  $query = "CREATE TABLE IF NOT EXISTS tbl_setting (
        id_setting INT AUTO_INCREMENT PRIMARY KEY,
        nama_aplikasi VARCHAR(255) DEFAULT 'INSTOCK',
        slogan_aplikasi VARCHAR(255) DEFAULT 'Fulfillment Warehouse Management System',
        nama_perusahaan VARCHAR(255) DEFAULT 'PT Instock Fulfillment Indonesia',
        alamat_perusahaan TEXT NULL,
        telepon VARCHAR(50) DEFAULT '0812-3456-7890',
        email VARCHAR(100) DEFAULT 'admin@instock.co.id',
        logo VARCHAR(255) DEFAULT 'images/img/q.png',
        favicon VARCHAR(255) DEFAULT 'images/img/logo.png',
        footer_text VARCHAR(255) DEFAULT 'INSTOCK',
        stok_minimum_default INT DEFAULT 5,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

  try {
    @mysqli_query($mysqli, $query);

    $check_empty = @mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_setting");
    $total = 0;
    if ($check_empty) {
      $total = mysqli_fetch_assoc($check_empty)['total'] ?? 0;
    }

    if ($total == 0) {
      @mysqli_query($mysqli, "INSERT INTO tbl_setting (id_setting, nama_aplikasi, slogan_aplikasi, nama_perusahaan, alamat_perusahaan, telepon, email, logo, favicon, footer_text, stok_minimum_default)
              VALUES (1, 'INSTOCK', 'Fulfillment Warehouse Management System', 'PT Instock Fulfillment Indonesia', 'Jl. Jendral Sudirman No. 123, Jakarta Selatan', '0812-3456-7890', 'admin@instock.co.id', 'images/img/q.png', 'images/img/logo.png', 'INSTOCK', 5)");
    }
  } catch (Throwable $e) {
    // Tangani error pembuatan tabel secara aman
  }

  $setting_initialized = true;
}

/**
 * Mengambil data pengaturan sistem terkini (Static In-Memory Cache)
 */
function getSystemSetting($mysqli)
{
  static $cached_setting = null;
  if ($cached_setting !== null) {
    return $cached_setting;
  }

  $defaults = [
    'id_setting' => 1,
    'nama_aplikasi' => 'INSTOCK',
    'slogan_aplikasi' => 'Fulfillment Warehouse Management System',
    'nama_perusahaan' => 'PT Instock Fulfillment Indonesia',
    'alamat_perusahaan' => 'Jl. Jendral Sudirman No. 123, Jakarta Selatan',
    'telepon' => '0812-3456-7890',
    'email' => 'admin@instock.co.id',
    'logo' => 'images/img/q.png',
    'favicon' => 'images/img/logo.png',
    'footer_text' => 'INSTOCK',
    'stok_minimum_default' => 5
  ];

  if (!$mysqli) return $defaults;

  // Otomatis inisialisasi tabel jika belum ada
  initSettingTable($mysqli);

  try {
    $res = @mysqli_query($mysqli, "SELECT * FROM tbl_setting LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
      $data = mysqli_fetch_assoc($res);
      $cached_setting = array_merge($defaults, array_filter($data, function ($val) {
        return !is_null($val);
      }));
      return $cached_setting;
    }
  } catch (Throwable $e) {
    // Jika query gagal/error, gunakan nilai default secara aman
  }

  $cached_setting = $defaults;
  return $cached_setting;
}

/**
 * Helper untuk mendapatkan URL/path logo atau favicon aplikasi secara aman dengan fallback
 */
function getLogoAppPath($sys_setting, $key = 'logo')
{
  $path = $sys_setting[$key] ?? '';
  if (!empty($path) && file_exists($path)) {
    return htmlspecialchars($path);
  }

  if ($key === 'favicon') {
    if (file_exists('assets/img/favicon.png')) return 'assets/img/favicon.png';
    if (file_exists('images/img/q.png')) return 'images/img/q.png';
  }

  if (file_exists('images/img/q.png')) return 'images/img/q.png';
  if (file_exists('assets/img/logo.png')) return 'assets/img/logo.png';

  return 'assets/img/logo.png';
}

