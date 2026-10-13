<?php session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk insert
require_once "../../config/database.php";
require_once "../../helper/permission_helper.php";

protectProcessScript(['Super Admin', 'Administrator'], 'cabang');

    // mengecek data hasil submit dari form
    if (isset($_POST['simpan'])) {
        // ambil data hasil submit dari form
        $kode_cabang = strtoupper(mysqli_real_escape_string($mysqli, trim($_POST['kode_cabang'] ?? '')));
        $nama_cabang = mysqli_real_escape_string($mysqli, trim($_POST['nama_cabang']));
        $alamat = mysqli_real_escape_string($mysqli, trim($_POST['alamat'] ?? ''));
        $nama_fm = mysqli_real_escape_string($mysqli, trim($_POST['nama_fm']));
        $nomor_telepon = mysqli_real_escape_string($mysqli, trim($_POST['nomor_telepon']));
        $email = mysqli_real_escape_string($mysqli, trim($_POST['email'] ?? ''));

        // mengecek "nama_cabang" untuk mencegah data duplikat
        // sql statement untuk menampilkan data "nama_cabang" dari tabel "tbl_cabang" berdasarkan input "nama_cabang"
        $query = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE nama_cabang='$nama_cabang'")
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
        // ambil jumlah baris data hasil query
        $rows = mysqli_num_rows($query);

        // cek hasil query
        // jika "nama_cabang" sudah ada di tabel "tbl_cabang"
        if ($rows <> 0) {
            // alihkan ke halaman cabang dan tampilkan pesan gagal simpan data
            header("location: ../../main.php?module=cabang&pesan=4&cabang=$nama_cabang");
        }
        // jika "nama_cabang" belum ada di tabel "tbl_cabang"
        else {
            // sql statement untuk insert data ke tabel "tbl_cabang"
            $insert = mysqli_query($mysqli, "INSERT INTO tbl_cabang(kode_cabang, nama_cabang, alamat, nama_fm, nomor_telepon, email) 
                                            VALUES('$kode_cabang', '$nama_cabang', '$alamat', '$nama_fm', '$nomor_telepon', '$email')")
                or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
            // cek query
            // jika proses insert berhasil
            if ($insert) {
                // alihkan ke halaman cabang dan tampilkan pesan berhasil simpan data
                header('location: ../../main.php?module=cabang&pesan=1');
            }
        }
    }
