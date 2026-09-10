<?php
session_start(); // Mengaktifkan session

// Pengecekan session login user
// Jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // Alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
  exit;
}

require_once "../../config/database.php";
require_once "../../helper/permission_helper.php";

protectProcessScript(['Super Admin', 'Administrator', 'Kepala Gudang'], 'user');

// Mengecek data hasil submit dari form
if (isset($_POST['simpan'])) {
  // Ambil data hasil submit dari form
  $nama_user = mysqli_real_escape_string($mysqli, trim($_POST['nama_user']));
  $username  = mysqli_real_escape_string($mysqli, trim($_POST['username']));
  $password  = mysqli_real_escape_string($mysqli, $_POST['password']);
  $hak_akses = mysqli_real_escape_string($mysqli, trim($_POST['hak_akses']));
  $id_cabang = mysqli_real_escape_string($mysqli, $_POST['id_cabang']);

  $current_user_role = $_SESSION['hak_akses'] ?? '';

  // Keamanan backend: Batasi peran yang dapat diberikan berdasarkan peran pengguna saat ini
  $disallowed_roles = [];
  if (!in_array($current_user_role, ['Super Admin', 'Administrator'])) {
    $disallowed_roles = ['Super Admin', 'Administrator'];
    if (!empty($_SESSION['id_cabang'])) {
      $id_cabang = $_SESSION['id_cabang'];
    }
  } elseif ($current_user_role === 'Administrator') {
    $disallowed_roles = ['Super Admin'];
  }

  if (in_array($hak_akses, $disallowed_roles)) {
    header("location: ../../main.php?module=user&pesan=5");
    exit;
  }

  // Lakukan validasi input
  if (empty($nama_user) || empty($username) || empty($password) || empty($hak_akses) || empty($id_cabang)) {
    // Alihkan kembali ke halaman entri user dengan pesan kesalahan
    header("location: ../../main.php?module=user&pesan=5");
    exit;
  }

  // Enkripsi password menggunakan password_hash()
  $option = [
    'cost' => 12,
  ];
  $password_hash = password_hash($password, PASSWORD_DEFAULT, $option);

  // Mengecek "username" untuk mencegah data duplikat
  $query = mysqli_query($mysqli, "SELECT username FROM tbl_user WHERE username='$username'")
    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
  $rows = mysqli_num_rows($query);

  // Cek hasil query
  if ($rows <> 0) {
    // Alihkan ke halaman user dan tampilkan pesan gagal simpan data
    header("location: ../../main.php?module=user&pesan=4&username=$username");
    exit;
  }

  // SQL statement untuk insert data ke tabel "tbl_user"
  $insert = mysqli_query($mysqli, "INSERT INTO tbl_user(nama_user, username, password, hak_akses, id_cabang)
                                   VALUES('$nama_user', '$username', '$password_hash', '$hak_akses', '$id_cabang')")
    or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));

  // Cek query
  if ($insert) {
    // Alihkan ke halaman user dan tampilkan pesan berhasil simpan data
    header('location: ../../main.php?module=user&pesan=1');
    exit;
  }
}
