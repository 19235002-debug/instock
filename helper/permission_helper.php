<?php
/**
 * Helper Manajemen Permission / Hak Akses Modul Dinamis (High Performance & Self-Healing)
 */

/**
 * Inisialisasi tabel permission hanya jika belum ada di database
 */
function initPermissionTable($mysqli)
{
  static $table_initialized = false;
  if ($table_initialized || !$mysqli) return;

  try {
    // Cek cepat apakah tabel sudah memiliki data
    $check = @mysqli_query($mysqli, "SELECT 1 FROM tbl_permission LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
      $table_initialized = true;
      return;
    }
  } catch (Throwable $e) {
    // Tabel belum ada, lanjut buat tabel
  }

  $create_table = "CREATE TABLE IF NOT EXISTS tbl_permission (
        id_permission INT AUTO_INCREMENT PRIMARY KEY,
        role VARCHAR(50) NOT NULL,
        module VARCHAR(50) NOT NULL,
        is_allowed TINYINT(1) DEFAULT 1,
        UNIQUE KEY role_module (role, module)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

  try {
    @mysqli_query($mysqli, $create_table);

    // Daftar modul default dan role non-admin
    $default_permissions = [
      'Kepala Gudang' => ['barang', 'jenis', 'satuan', 'location', 'lokasi_sto', 'barang_masuk', 'barang_keluar', 'adjustment', 'laporan_stok', 'laporan_barang_masuk', 'laporan_barang_keluar', 'user'],
      'Staff Gudang'  => ['barang', 'jenis', 'satuan', 'location', 'lokasi_sto', 'barang_masuk', 'barang_keluar', 'laporan_stok', 'laporan_barang_keluar'],
      'Supplier'      => ['barang', 'location', 'jenis', 'satuan', 'barang_masuk', 'laporan_stok', 'laporan_barang_masuk'],
    ];

    $all_modules = ['barang', 'jenis', 'satuan', 'location', 'lokasi_sto', 'barang_masuk', 'barang_keluar', 'adjustment', 'laporan_stok', 'laporan_barang_masuk', 'laporan_barang_keluar', 'user'];

    foreach ($default_permissions as $role => $allowed_list) {
      foreach ($all_modules as $mod) {
        $allowed = in_array($mod, $allowed_list) ? 1 : 0;
        $mod_esc = mysqli_real_escape_string($mysqli, $mod);
        $role_esc = mysqli_real_escape_string($mysqli, $role);
        @mysqli_query($mysqli, "INSERT IGNORE INTO tbl_permission (role, module, is_allowed) VALUES ('$role_esc', '$mod_esc', $allowed)");
      }
    }
  } catch (Throwable $e) {
    // Tangani error pembuatan skema secara aman
  }

  $table_initialized = true;
}

/**
 * Cek apakah modul tertentu diperbolehkan untuk role pengguna (Memory Cached)
 */
function isModuleAllowed($role, $module, $mysqli = null)
{
  static $permission_cache = [];

  if ($role === 'Super Admin' || $role === 'Administrator' || empty($module) || $module === 'dashboard' || $module === 'form_ubah_password') {
    return true;
  }

  if (empty($mysqli)) {
    global $mysqli;
  }

  if (!$mysqli) {
    return true; // Fallback jika database connection belum siap
  }

  // Otomatis inisialisasi jika tabel terhapus / belum dibuat
  initPermissionTable($mysqli);

  // Modul turunan / form dipetakan ke modul utamanya
  $module_map = [
    'form_entri_barang' => 'barang',
    'tampil_detail_barang' => 'barang',
    'form_ubah_barang' => 'barang',

    'form_entri_jenis' => 'jenis',
    'form_ubah_jenis' => 'jenis',

    'form_entri_satuan' => 'satuan',
    'form_ubah_satuan' => 'satuan',

    'form_entri_location' => 'location',
    'tampil_detail_location' => 'location',
    'form_ubah_location' => 'location',

    'form_entri_lokasi_sto' => 'lokasi_sto',
    'form_ubah_lokasi_sto' => 'lokasi_sto',
    'proses_hapus_lokasi_sto' => 'lokasi_sto',
    'proses_entri_lokasi_sto' => 'lokasi_sto',

    'form_entri_barang_masuk' => 'barang_masuk',
    'form_entri_barang_keluar' => 'barang_keluar',

    'form_entri_adjustment' => 'adjustment',
    'proses_hapus_adjustment' => 'adjustment',

    'form_entri_user' => 'user',
    'form_ubah_user' => 'user',
  ];

  $base_module = $module_map[$module] ?? $module;

  // 1. Ambil dari Memory Cache jika sudah di-fetch dalam request ini
  if (!isset($permission_cache[$role])) {
    $permission_cache[$role] = [];
    try {
      $role_esc = mysqli_real_escape_string($mysqli, $role);
      $res = @mysqli_query($mysqli, "SELECT module, is_allowed FROM tbl_permission WHERE role = '$role_esc'");
      if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
          $permission_cache[$role][$row['module']] = (int)$row['is_allowed'];
        }
      }
    } catch (Throwable $e) {
      // Fallback jika query gagal
    }
  }

  // 2. Lookup instan dari memory O(1)
  if (isset($permission_cache[$role][$base_module])) {
    return $permission_cache[$role][$base_module] === 1;
  }

  return true; // Default fallback jika belum diset
}

/**
 * Guard backend process script against unauthorized execution
 */
function protectProcessScript($allowed_roles, $module_name = '')
{
  if (session_status() == PHP_SESSION_NONE) {
    @session_start();
  }

  $role = $_SESSION['hak_akses'] ?? '';

  if (empty($_SESSION['username']) || empty($_SESSION['password']) || empty($role)) {
    header('location: ../../login.php?pesan=2');
    exit;
  }

  if (!in_array($role, $allowed_roles)) {
    header('location: ../../403.php');
    exit;
  }

  if (!empty($module_name)) {
    global $mysqli;
    if (function_exists('isModuleAllowed') && !isModuleAllowed($role, $module_name, $mysqli)) {
      header('location: ../../403.php');
      exit;
    }
  }
}
