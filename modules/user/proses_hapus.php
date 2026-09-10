<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
  exit;
}
// jika user sudah login, maka jalankan perintah untuk delete
require_once "../../config/database.php";
require_once "../../helper/permission_helper.php";

protectProcessScript(['Super Admin', 'Administrator', 'Kepala Gudang'], 'user');

  // mengecek data GET "id_user"
  if (isset($_GET['id'])) {
    // ambil data GET dari tombol hapus
    $id_user = mysqli_real_escape_string($mysqli, $_GET['id']);
    $current_user_role = $_SESSION['hak_akses'] ?? '';
    $current_user_id   = $_SESSION['id_user'] ?? 0;
    $current_user_cabang = $_SESSION['id_cabang'] ?? 0;

    // Tidak boleh menghapus akun sendiri
    if ($id_user == $current_user_id) {
      header("location: ../../main.php?module=user&pesan=5");
      exit;
    }

    // Cek akun target sebelum menghapus
    $query_target = mysqli_query($mysqli, "SELECT hak_akses, id_cabang FROM tbl_user WHERE id_user='$id_user'");
    if ($data_target = mysqli_fetch_assoc($query_target)) {
      $target_role = $data_target['hak_akses'];

      // Jika target adalah Super Admin / Administrator, hanya Super Admin / Administrator yang boleh hapus
      if (in_array($target_role, ['Super Admin', 'Administrator']) && !in_array($current_user_role, ['Super Admin', 'Administrator'])) {
        header("location: ../../main.php?module=user&pesan=5");
        exit;
      }

      // Jika pengguna bukan admin global, cegah menghapus user di luar cabangnya
      if (!in_array($current_user_role, ['Super Admin', 'Administrator']) && $data_target['id_cabang'] != $current_user_cabang) {
        header("location: ../../main.php?module=user&pesan=5");
        exit;
      }
    }

    // sql statement untuk delete data dari tabel "tbl_user" berdasarkan "id_user"
    $delete = mysqli_query($mysqli, "DELETE FROM tbl_user WHERE id_user='$id_user'")
                                     or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));
    // cek query
    // jika proses delete berhasil
    if ($delete) {
      // alihkan ke halaman user dan tampilkan pesan berhasil hapus data
      header('location: ../../main.php?module=user&pesan=3');
      exit;
    }
  }
