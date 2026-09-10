<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk delete
require_once "../../config/database.php";
require_once "../../helper/permission_helper.php";

protectProcessScript(['Super Admin', 'Administrator'], 'cabang');

    // mengecek data GET "id_cabang"
    if (isset($_GET['id'])) {
        // ambil data GET dari tombol hapus
        $id_cabang = mysqli_real_escape_string($mysqli, $_GET['id']);

        // Check if branch is used in tbl_barang or tbl_user to protect relational integrity
        $q_check = mysqli_query($mysqli, "SELECT id_barang FROM tbl_barang WHERE id_cabang='$id_cabang' UNION SELECT id_user FROM tbl_user WHERE id_cabang='$id_cabang'");
        if ($q_check && mysqli_num_rows($q_check) > 0) {
            header('location: ../../main.php?module=cabang&pesan=5');
            exit;
        }

        $delete = mysqli_query($mysqli, "DELETE FROM tbl_cabang WHERE id_cabang='$id_cabang'")
                or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));
                
        // cek query
        // jika proses delete berhasil
        if ($delete) {
            // alihkan ke halaman cabang dan tampilkan pesan berhasil hapus data
            header('location: ../../main.php?module=cabang&pesan=3');
            exit;
        }
    }
