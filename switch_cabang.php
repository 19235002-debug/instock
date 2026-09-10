<?php
session_start();
require_once "config/database.php";
require_once "helper/cabang_helper.php";

if (isset($_GET['id_cabang'])) {
    $id_cabang = (int)$_GET['id_cabang'];
    setActiveCabangId($id_cabang);

    // Redirect kembali ke halaman sebelumnya atau dashboard
    $referer = $_SERVER['HTTP_REFERER'] ?? 'main.php?module=dashboard';
    header("Location: " . $referer);
    exit;
} else if (isset($_POST['id_cabang'])) {
    $id_cabang = (int)$_POST['id_cabang'];
    setActiveCabangId($id_cabang);
    echo json_encode(['status' => 'success', 'id_cabang' => $id_cabang]);
    exit;
}
