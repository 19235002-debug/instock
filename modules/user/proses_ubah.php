<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
  exit;
}
// jika user sudah login, maka jalankan perintah untuk update
require_once "../../config/database.php";
require_once "../../helper/permission_helper.php";

protectProcessScript(['Super Admin', 'Administrator', 'Kepala Gudang'], 'user');

  // mengecek data hasil submit dari form
  if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $id_user   = mysqli_real_escape_string($mysqli, $_POST['id_user']);
    $nama_user = mysqli_real_escape_string($mysqli, trim($_POST['nama_user']));
    $username  = mysqli_real_escape_string($mysqli, trim($_POST['username']));
    $password  = mysqli_real_escape_string($mysqli, $_POST['password']);
    $hak_akses = mysqli_real_escape_string($mysqli, trim($_POST['hak_akses']));
    $id_cabang = mysqli_real_escape_string($mysqli, $_POST['id_cabang']);

    $current_user_role = $_SESSION['hak_akses'] ?? '';

    // Cek akun target yang diubah
    $query_target = mysqli_query($mysqli, "SELECT hak_akses FROM tbl_user WHERE id_user='$id_user'");
    if ($data_target = mysqli_fetch_assoc($query_target)) {
      $target_role = $data_target['hak_akses'];
      if (in_array($target_role, ['Super Admin', 'Administrator']) && !in_array($current_user_role, ['Super Admin', 'Administrator'])) {
        header("location: ../../main.php?module=user&pesan=5");
        exit;
      }
    }

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

    // mengecek "username" untuk mencegah data duplikat
    // sql statement untuk menampilkan data "username" dari tabel "tbl_user" berdasarkan input "username"
    $query = mysqli_query($mysqli, "SELECT username FROM tbl_user WHERE username='$username' AND id_user!='$id_user'")
                                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil jumlah baris data hasil query
    $rows = mysqli_num_rows($query);

    // cek hasil query
    // jika "username" sudah ada di tabel "tbl_user"
    if ($rows <> 0) {
      // alihkan ke halaman user dan tampilkan pesan gagal ubah data
      header("location: ../../main.php?module=user&pesan=4&username=$username");
    }
    // jika "username" belum ada di tabel "tbl_user"
    else {
      // cek password
      // jika password tidak diubah
      if (empty($password)) {
        // sql statement untuk update data di tabel "tbl_user" berdasarkan "id_user"
        $update = mysqli_query($mysqli, "UPDATE tbl_user
                                         SET nama_user='$nama_user', username='$username', hak_akses='$hak_akses', id_cabang='$id_cabang'
                                         WHERE id_user='$id_user'")
                                         or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
      }
      // jika password diubah
      else {
        // enkripsi password menggunakan password_hash()
        // tentukan cost (menentukan seberapa banyak proses hash dilakukan)
        $option = [
          'cost' => 12,
        ];
        // hashing password
        $password_hash = password_hash($password, PASSWORD_DEFAULT, $option);

        // sql statement untuk update data di tabel "tbl_user" berdasarkan "id_user"
        $update = mysqli_query($mysqli, "UPDATE tbl_user
                                         SET nama_user='$nama_user', username='$username', password='$password_hash', hak_akses='$hak_akses', id_cabang='$id_cabang'
                                         WHERE id_user='$id_user'")
                                         or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
      }

      // cek query
      // jika proses update berhasil
      if ($update) {
        // alihkan ke halaman user dan tampilkan pesan berhasil ubah data
        header('location: ../../main.php?module=user&pesan=2');
      }
    }
  }
