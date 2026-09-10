<?php
include 'koneksi.php';

$id_cabang = $_POST['id_cabang'];

$query_barang = mysqli_query($mysqli, "SELECT * FROM tbl_barang WHERE id_cabang='$id_cabang' ORDER BY nama_barang ASC");

echo '<option value="">-- Pilih Barang --</option>';
while ($data_barang = mysqli_fetch_assoc($query_barang)) {
    echo "<option value='" . $data_barang['nama_barang'] . "'>" . $data_barang['nama_barang'] . "</option>";
}
