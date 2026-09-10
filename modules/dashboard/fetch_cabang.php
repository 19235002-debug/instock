<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "gudang";

$koneksi = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Query untuk mengambil data cabang
$sql = "SELECT id_cabang, nama_cabang FROM tbl_cabang";
$result = $koneksi->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Kembalikan dalam format JSON
echo json_encode($data);

$koneksi->close();
