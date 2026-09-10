<?php
session_start();
require '../../vendor/autoload.php';
require '../../config/database.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['file']['error']) && $_FILES['file']['error'] == 0) {
    $spreadsheet = IOFactory::load($_FILES['file']['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    $result = $mysqli->query("SELECT id_transaksi FROM tbl_barang_masuk ORDER BY id_transaksi DESC LIMIT 1");
    $lastId = $result->fetch_assoc();
    $lastNumericPart = intval(substr($lastId['id_transaksi'], 3));

    $id_cabang = $_SESSION['id_cabang'];
    foreach ($rows as $index => $row) {
        if ($index == 0) continue;

        $lastNumericPart++;
        $id_transaksi = "TM-" . str_pad($lastNumericPart, 7, "0", STR_PAD_LEFT);

        $tanggal = $mysqli->real_escape_string($row[0]);
        $barang = $mysqli->real_escape_string($row[1]);
        $jumlah = $mysqli->real_escape_string($row[2]);
        $satuan_nama = $mysqli->real_escape_string($row[3]);

        // Dapatkan id_satuan berdasarkan nama_satuan
        $resultSatuan = $mysqli->query("SELECT id_satuan FROM tbl_satuan WHERE nama_satuan='$satuan_nama'");
        $dataSatuan = $resultSatuan->fetch_assoc();
        $id_satuan = $dataSatuan['id_satuan'];

        $query = "INSERT INTO tbl_barang_masuk (id_transaksi, tanggal, barang, jumlah, id_satuan) VALUES ('$id_transaksi', '$tanggal', '$barang', '$jumlah', '$id_satuan')";

        if (!$mysqli->query($query)) {
            die('Error: ' . $mysqli->error);
        }
    }

    $mysqli->close();
    header('Location: ../../main.php?module=barang_masuk&pesan=1');
} else {
    die('Error mengunggah file.');
}
