<?php
session_start();
require_once "../../config/database.php";

if (!isset($_SESSION['id_user'])) {
  header('location: ../../login.php');
  exit;
}

if (isset($_POST['simpan'])) {
  $id_user = (int)$_POST['id_user'];
  $nama_user = mysqli_real_escape_string($mysqli, trim($_POST['nama_user']));
  $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));
  $password_baru = trim($_POST['password_baru'] ?? '');
  $konfirmasi_password = trim($_POST['konfirmasi_password'] ?? '');

  // 1. Cek apakah username sudah dipakai user lain
  $check_user = mysqli_query($mysqli, "SELECT id_user FROM tbl_user WHERE username = '$username' AND id_user != '$id_user'");
  if (mysqli_num_rows($check_user) > 0) {
    header("location: ../../main.php?module=profile&pesan=1");
    exit;
  }

  // 2. Ambil data foto lama
  $query_old = mysqli_query($mysqli, "SELECT foto FROM tbl_user WHERE id_user = '$id_user'");
  $data_old = mysqli_fetch_assoc($query_old);
  $foto_lama = $data_old['foto'] ?? '';

  $foto_sql = "";
  $new_foto_name = null;

  // 3. Cek apakah ada file foto yang diunggah
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK && $_FILES['foto']['size'] > 0) {
    $nama_file   = $_FILES['foto']['name'];
    $ukuran_file = $_FILES['foto']['size'];
    $tmp_file    = $_FILES['foto']['tmp_name'];

    $extension_izinkan = array('jpg', 'jpeg', 'png', 'webp');
    $extension         = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    // Validasi format dan ukuran (maks 2MB)
    if (in_array($extension, $extension_izinkan) && $ukuran_file <= 2097152) {
      $new_foto_name = 'user_' . $id_user . '_' . time() . '.' . $extension;
      $target_dir = "../../assets/img/user/";

      if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      if (move_uploaded_file($tmp_file, $target_dir . $new_foto_name)) {
        // Hapus foto lama jika ada
        if (!empty($foto_lama) && file_exists($target_dir . $foto_lama)) {
          unlink($target_dir . $foto_lama);
        }
        $foto_sql = ", foto = '$new_foto_name'";
      } else {
        header("location: ../../main.php?module=profile&pesan=5");
        exit;
      }
    } else {
      header("location: ../../main.php?module=profile&pesan=5");
      exit;
    }
  }

  // 4. Update query database
  if (!empty($password_baru)) {
    if ($password_baru !== $konfirmasi_password) {
      header("location: ../../main.php?module=profile&pesan=2");
      exit;
    }
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $query_update = "UPDATE tbl_user SET nama_user = '$nama_user', username = '$username', password = '$password_hash' $foto_sql WHERE id_user = '$id_user'";
  } else {
    $query_update = "UPDATE tbl_user SET nama_user = '$nama_user', username = '$username' $foto_sql WHERE id_user = '$id_user'";
  }

  $update = mysqli_query($mysqli, $query_update);

  if ($update) {
    // Update session agar perubahan langsung tercermin di Topbar dan Sidebar
    $_SESSION['nama_user'] = $nama_user;
    $_SESSION['username']  = $username;
    if ($new_foto_name !== null) {
      $_SESSION['foto'] = $new_foto_name;
    }

    // Jika user terikat dengan cabang tertentu, perbarui nama_fm di tbl_cabang
    $id_cabang = (int)($_SESSION['id_cabang'] ?? 0);
    if ($id_cabang > 0) {
      mysqli_query($mysqli, "UPDATE tbl_cabang SET nama_fm = '$nama_user' WHERE id_cabang = '$id_cabang'");
    }

    header("location: ../../main.php?module=profile&pesan=3");
  } else {
    header("location: ../../main.php?module=profile&pesan=4");
  }
}

