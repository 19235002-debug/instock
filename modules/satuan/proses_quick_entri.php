<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once "../../config/database.php";

if (isset($_POST['nama_satuan'])) {
    $nama_satuan = mysqli_real_escape_string($mysqli, trim($_POST['nama_satuan']));

    if (empty($nama_satuan)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama satuan tidak boleh kosong.']);
        exit;
    }

    // Cek duplikat
    $cek = mysqli_query($mysqli, "SELECT id_satuan FROM tbl_satuan WHERE nama_satuan = '$nama_satuan'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        echo json_encode(['status' => 'exists', 'id_satuan' => $row['id_satuan'], 'nama_satuan' => $nama_satuan]);
        exit;
    }

    $insert = mysqli_query($mysqli, "INSERT INTO tbl_satuan(nama_satuan) VALUES('$nama_satuan')");
    if ($insert) {
        $new_id = mysqli_insert_id($mysqli);
        echo json_encode(['status' => 'success', 'id_satuan' => $new_id, 'nama_satuan' => $nama_satuan]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan: ' . mysqli_error($mysqli)]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}
