<?php
// Koneksi ke database Anda (asumsi Anda telah mengatur koneksi)
$mysqli = new mysqli("localhost", "root", "", "gudang");

// Query untuk mengambil data dari tabel tbl_cabang
$query = mysqli_query($mysqli, "SELECT id_cabang, nama_cabang FROM tbl_cabang")
    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

// Loop melalui hasil query dan membuat pilihan dropdown untuk setiap cabang
while ($row = mysqli_fetch_assoc($query)) {
    echo '<option value="' . $row['id_cabang'] . '">' . $row['nama_cabang'] . '</option>';
}

// Tutup koneksi database
mysqli_close($mysqli);
