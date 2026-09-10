<?php
require_once '../../config/library.php';
require_once '../../config/authentication.php';

$lib = new Library();
$auth = new Authentication();

$get_user = $auth->getUser();

// Pastikan ada file yang diupload
if (isset($_FILES['importFile']['name'])) {
    $file = $_FILES['importFile']['tmp_name'];
    
    // Gunakan library PHPExcel untuk membaca data dari Excel
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Loop melalui semua baris data di file Excel
    for ($i = 1; $i < count($rows); $i++) {
        $id_transaksi = $rows[$i][0];
        $tanggal = $rows[$i][1];
        $barang = $rows[$i][2];
        $jumlah = $rows[$i][3];
        $satuan = $rows[$i][4];
        
        // Query untuk memasukkan data ke database
        $insert = $lib->importBarangMasuk($id_transaksi, $tanggal, $barang, $jumlah, $satuan);
    }

    // Redirect setelah selesai mengimpor
    header('Location: ../../main.php?module=barang_masuk&pesan=1');
} else {
    header('Location: ../../main.php?module=barang_masuk&pesan=3');
}
