<?php session_start(); // Mengaktifkan session

// Pengecekan session login user
// Jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // Alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
} 

require_once "../../config/database.php";
require_once "../../helper/permission_helper.php";

protectProcessScript(['Super Admin', 'Administrator'], 'cabang');

    // Mengecek data hasil submit dari form
    if (isset($_POST['simpan'])) {
        // Ambil data hasil submit dari form
        $id_cabang = mysqli_real_escape_string($mysqli, $_POST['id_cabang']);
        $kode_cabang = strtoupper(mysqli_real_escape_string($mysqli, trim($_POST['kode_cabang'] ?? '')));
        $nama_cabang = mysqli_real_escape_string($mysqli, trim($_POST['nama_cabang']));
        $alamat = mysqli_real_escape_string($mysqli, trim($_POST['alamat'] ?? ''));
        $nama_fm = mysqli_real_escape_string($mysqli, trim($_POST['nama_fm']));
        $nomor_telepon = mysqli_real_escape_string($mysqli, trim($_POST['nomor_telepon']));
        $email = mysqli_real_escape_string($mysqli, trim($_POST['email'] ?? ''));

        // Mengecek "nama_cabang" untuk mencegah data duplikat
        // SQL statement untuk menampilkan data "nama_cabang" dari tabel "tbl_cabang" berdasarkan input "nama_cabang"
        $query = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE nama_cabang='$nama_cabang' AND id_cabang!='$id_cabang'")
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
        // Ambil jumlah baris data hasil query
        $rows = mysqli_num_rows($query);

        // Cek hasil query
        // Jika "nama_cabang" sudah ada di tabel "tbl_cabang"
        if ($rows <> 0) {
            // Alihkan ke halaman cabang dan tampilkan pesan gagal ubah data
            header("location: ../../main.php?module=cabang&pesan=4&cabang=$nama_cabang");
        }
        // Jika "nama_cabang" belum ada di tabel "tbl_cabang"
        else {
            // SQL statement untuk update data di tabel "tbl_cabang" berdasarkan "id_cabang"
            $update = mysqli_query($mysqli, "UPDATE tbl_cabang
                                       SET kode_cabang='$kode_cabang', nama_cabang='$nama_cabang', alamat='$alamat', nama_fm='$nama_fm', nomor_telepon='$nomor_telepon', email='$email'
                                       WHERE id_cabang='$id_cabang'")
                or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
            // Cek query
            // Jika proses update berhasil
            if ($update) {
                // Alihkan ke halaman cabang dan tampilkan pesan berhasil ubah data
                header('location: ../../main.php?module=cabang&pesan=2');
            }
        }
    }
