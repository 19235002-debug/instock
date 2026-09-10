<?php
session_start(); // mengaktifkan session


// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk insert
else {
    // panggil file "database.php" untuk koneksi ke database
    require_once "../../config/database.php";

    // mengecek data hasil submit dari form
    if (isset($_POST['simpan'])) {
        // ambil data hasil submit dari form
        $nama_lokasi = mysqli_real_escape_string($mysqli, trim($_POST['nama_lokasi']));
        $id_teritory = mysqli_real_escape_string($mysqli, trim($_POST['id_teritory']));
        $kode_wilayah = mysqli_real_escape_string($mysqli, trim($_POST['kode_wilayah']));
        $id_cabang = mysqli_real_escape_string($mysqli, trim($_POST['id_cabang']));


        // mengecek "nama_lokasi" untuk mencegah data duplikat
        // sql statement untuk menampilkan data "nama_lokasi" dari tabel "tbl_lokasi_sto" berdasarkan input "nama_lokasi"
        $query = mysqli_query($mysqli, "SELECT nama_lokasi FROM tbl_lokasi_sto WHERE nama_lokasi='$nama_lokasi'")
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
        // ambil jumlah baris data hasil query
        $rows = mysqli_num_rows($query);

        // cek hasil query
        // jika "nama_lokasi" sudah ada di tabel "tbl_lokasi_sto"
        if ($rows <> 0) {
            // alihkan ke halaman lokasi_sto dan tampilkan pesan gagal simpan data
            header("location: ../../main.php?module=lokasi_sto&pesan=4&lokasi_sto=$nama_lokasi");
        }
        // jika "nama_lokasi" belum ada di tabel "tbl_lokasi_sto"
        else {
            // sql statement untuk insert data ke tabel "tbl_lokasi_sto"
            $insert = mysqli_query($mysqli, "INSERT INTO tbl_lokasi_sto(nama_lokasi, kode_wilayah, id_teritory, id_cabang) VALUES('$nama_lokasi', '$kode_wilayah', '$id_teritory', '$id_cabang')")
                or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
            // cek query
            // jika proses insert berhasil
            if ($insert) {
                // alihkan ke halaman lokasi_sto dan tampilkan pesan berhasil simpan data
                header('location: ../../main.php?module=lokasi_sto&pesan=1');
            }
        }
    }
}
