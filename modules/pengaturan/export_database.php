<?php
session_start();

// Pengecekan session login user
if (empty($_SESSION['username']) || empty($_SESSION['password'])) {
  header('location: ../../login.php?pesan=2');
  exit;
}

require_once "../../config/database.php";
require_once "../../helper/permission_helper.php";

// Proteksi keamanan: Hanya Super Admin & Administrator yang dapat mengunduh backup database
protectProcessScript(['Super Admin', 'Administrator'], 'pengaturan');

// Disable output buffering & set max execution time
if (function_exists('set_time_limit')) {
  @set_time_limit(300);
}

// Ambil daftar semua tabel di database
$tables = [];
$result = mysqli_query($mysqli, "SHOW TABLES");
if ($result) {
  while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
  }
}

// Header metadata file SQL
$sql_dump  = "-- ========================================================\n";
$sql_dump .= "-- INSTOCK WMS - DATABASE BACKUP SQL\n";
$sql_dump .= "-- Database: `" . $database . "`\n";
$sql_dump .= "-- Tanggal Backup: " . date('Y-m-d H:i:s') . "\n";
$sql_dump .= "-- Di-generate Oleh: " . ($_SESSION['nama_user'] ?? 'Super Admin') . " (" . ($_SESSION['hak_akses'] ?? '') . ")\n";
$sql_dump .= "-- ========================================================\n\n";

$sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n";
$sql_dump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$sql_dump .= "SET time_zone = \"+07:00\";\n\n";

// Loop untuk setiap tabel
foreach ($tables as $table) {
  $sql_dump .= "-- --------------------------------------------------------\n";
  $sql_dump .= "-- Struktur tabel untuk `" . $table . "`\n";
  $sql_dump .= "-- --------------------------------------------------------\n";
  $sql_dump .= "DROP TABLE IF EXISTS `" . $table . "`;\n";

  $create_res = mysqli_query($mysqli, "SHOW CREATE TABLE `" . $table . "`");
  if ($create_res && $create_row = mysqli_fetch_row($create_res)) {
    $sql_dump .= $create_row[1] . ";\n\n";
  }

  // Dump data baris
  $data_res = mysqli_query($mysqli, "SELECT * FROM `" . $table . "`");
  if ($data_res) {
    $column_count = mysqli_num_fields($data_res);
    $num_rows = mysqli_num_rows($data_res);

    if ($num_rows > 0) {
      $sql_dump .= "-- Data untuk tabel `" . $table . "` (" . $num_rows . " baris)\n";
      while ($row = mysqli_fetch_row($data_res)) {
        $sql_dump .= "INSERT INTO `" . $table . "` VALUES(";
        for ($j = 0; $j < $column_count; $j++) {
          if (isset($row[$j]) && $row[$j] !== null) {
            $escaped = mysqli_real_escape_string($mysqli, $row[$j]);
            $escaped = str_replace("\n", "\\n", $escaped);
            $escaped = str_replace("\r", "\\r", $escaped);
            $sql_dump .= "'" . $escaped . "'";
          } else {
            $sql_dump .= "NULL";
          }
          if ($j < ($column_count - 1)) {
            $sql_dump .= ", ";
          }
        }
        $sql_dump .= ");\n";
      }
      $sql_dump .= "\n";
    }
  }
}

$sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
$sql_dump .= "-- Akhir dari backup database --\n";

// Menyiapkan header response agar browser langsung mendownload file .sql
$filename = "INSTOCK_DB_BACKUP_" . date('Ymd_His') . ".sql";

header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($sql_dump));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');

echo $sql_dump;
exit;
