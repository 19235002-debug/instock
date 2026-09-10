<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once "../../config/database.php";

if (isset($_POST['nama_jenis'])) {
    $nama_jenis = mysqli_real_escape_string($mysqli, trim($_POST['nama_jenis']));

    if (empty($nama_jenis)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama jenis tidak boleh kosong.']);
        exit;
    }

    // Cek duplikat
    $cek = mysqli_query($mysqli, "SELECT id_jenis FROM tbl_jenis WHERE nama_jenis = '$nama_jenis'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        echo json_encode(['status' => 'exists', 'id_jenis' => $row['id_jenis'], 'nama_jenis' => $nama_jenis]);
        exit;
    }

    $insert = mysqli_query($mysqli, "INSERT INTO tbl_jenis(nama_jenis) VALUES('$nama_jenis')");
    if ($insert) {
        $new_id = mysqli_insert_id($mysqli);
        echo json_encode(['status' => 'success', 'id_jenis' => $new_id, 'nama_jenis' => $nama_jenis]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan: ' . mysqli_error($mysqli)]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}
