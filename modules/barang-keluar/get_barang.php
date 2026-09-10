<?php
session_start(); // Memulai sesi

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    require_once "../../config/database.php";

    $id_barang = $_GET['id_barang'] ?? null;

    // Filter berdasarkan cabang, kecuali untuk administrator
    $cabang_filter = "";
    if ($_SESSION['hak_akses'] != 'administrator') {
        $cabang_filter = " AND a.id_cabang = '$_SESSION[id_cabang]'";
    }

    if ($id_barang) {
        $query = mysqli_query($mysqli, "SELECT a.stok, b.nama_satuan, c.no_rak as rak_id 
        FROM tbl_barang as a 
        INNER JOIN tbl_satuan as b ON a.satuan=b.id_satuan 
        INNER JOIN location as c ON a.rak_id=c.id
        WHERE a.id_barang='$id_barang' $cabang_filter")
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

        $data  = mysqli_fetch_assoc($query);
        echo json_encode($data);
    }
} else {
    header('location: ../../404.html');
}
