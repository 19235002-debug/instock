<?php
// Mulai sesi
session_start();

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../../config/database.php";

    $id_jenis = $_GET['jenis_id'] ?? null; // Mengambil jenis_id dari parameter GET

    // Filter berdasarkan cabang, kecuali untuk administrator
    $cabang_filter = "";
    if ($_SESSION['hak_akses'] != 'administrator') {
        $cabang_filter = " AND id_cabang = '$_SESSION[id_cabang]'";
    }

    if ($id_jenis) {
        // Query untuk mengambil data Nama Barang berdasarkan id_jenis
        $query = mysqli_query($mysqli, "SELECT id, nama_barang FROM temp_barang WHERE jenis_barang='$id_jenis' $cabang_filter")
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

        $data = array();

        // Ambil data Nama Barang dan id
        while ($row = mysqli_fetch_assoc($query)) {
            $data[$row['id']] = $row['nama_barang'];
        }

        echo json_encode($data);
    } else {
        echo 'Data id_jenis tidak ditemukan.';
    }
} else {
    header('location: ../../404.html');
}
