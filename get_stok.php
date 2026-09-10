<?php 
// get_stok.php
include 'koneksi.php';

$nama_barang = $_POST['nama_barang'];
$id_cabang = $_POST['id_cabang']; // Terima ID cabang

// Gunakan ID cabang dalam query
$query_stok = mysqli_query($mysqli, "SELECT stok FROM tbl_barang WHERE nama_barang='$nama_barang' AND id_cabang='$id_cabang'");
$data_stok = mysqli_fetch_assoc($query_stok);

echo $data_stok['stok'];
