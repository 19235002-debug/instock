<?php
// Sambungkan ke database
include 'koneksi.php';

if (isset($_POST['jenis_id'])) {
  $jenis_id = $_POST['jenis_id'];

  // Query untuk mengambil nama_barang berdasarkan ID jenis
  $query_nama_barang = mysqli_query($mysqli, "SELECT nama_barang FROM temp_barang WHERE id_jenis = $jenis_id")
    or die('Ada kesalahan pada query tampil data: ' . mysqli_error($mysqli));

  // Membuat opsi dropdown "Nama Barang" berdasarkan hasil query di atas
  echo '<select name="nama_barang" class="form-control" autocomplete="off" required>';
  echo '<option selected disabled value="">-- Pilih --</option>';
  while ($data_nama_barang = mysqli_fetch_assoc($query_nama_barang)) {
    echo "<option value='$data_nama_barang[nama_barang]'>$data_nama_barang[nama_barang]</option>";
  }
  echo '</select>';
}
