<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
} else {
    require_once "../../config/database.php";

    if (isset($_GET['id'])) {
        $id_lokasi = mysqli_real_escape_string($mysqli, $_GET['id']);

        // Hapus data dari tabel "tbl_lokasi_sto" berdasarkan "id_lokasi"
        $delete = mysqli_query($mysqli, "DELETE FROM tbl_lokasi_sto WHERE id_lokasi='$id_lokasi'")
            or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));

        if ($delete) {
            header('location: ../../main.php?module=lokasi_sto&pesan=3');
        } else {
            header('location: ../../main.php?module=lokasi_sto&pesan=5');
        }
    }
}
