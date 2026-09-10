<?php
require '../../config/database.php'; // Sesuaikan dengan file konfigurasi database Anda
require '../../vendor/autoload.php'; // Pastikan Anda sudah menginstall library PhpSpreadsheet dengan composer

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['file']['error']) && $_FILES['file']['error'] == 0) {
    $spreadsheet = IOFactory::load($_FILES['file']['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Ambil id_barang terakhir dari tabel tbl_barang
    $result = $mysqli->query("SELECT id_barang FROM tbl_barang ORDER BY id_barang DESC LIMIT 1");
    $data = $result->fetch_assoc();
    $lastId = $data['id_barang']; // misal: B0023
    $nextNumber = (int)substr($lastId, 1); // mengambil angka dari id_barang, misal: 0023

    foreach ($rows as $index => $row) {
        if ($index == 0) continue; // Skip header

        $nextNumber++; // Tambahkan 1 ke nextNumber di setiap iterasi
        $barcode = "B" . str_pad($nextNumber, 4, "0", STR_PAD_LEFT); // hasil: B0024, B0025, ...

        $nama_barang = $mysqli->real_escape_string($row[0]);
        $stok = $mysqli->real_escape_string($row[2]);
        $jenis_nama = $mysqli->real_escape_string($row[4]);
        $id_cabang = $_SESSION['id_cabang'];

        // Cek jenis barang di tbl_jenis
        $resultJenis = $mysqli->query("SELECT id_jenis FROM tbl_jenis WHERE nama_jenis='$jenis_nama'");
        if ($resultJenis->num_rows > 0) {
            $dataJenis = $resultJenis->fetch_assoc();
            $id_jenis = $dataJenis['id_jenis'];
        } else {
            $mysqli->query("INSERT INTO tbl_jenis (nama_jenis) VALUES ('$jenis_nama')");
            $id_jenis = $mysqli->insert_id;
        }

        // Sisipkan data ke tbl_barang_masuk
        $queryInsert = "INSERT INTO tbl_barang_masuk (barcode, nama_barang, stok, jenis, id_cabang) VALUES ('$barcode', '$nama_barang', '$stok', '$id_jenis', '$id_cabang')";
        $mysqli->query($queryInsert);
    }

    header('Location: ../../main.php?module=barang_masuk&pesan=1');
} else {
    die('Error mengunggah file.');
}
