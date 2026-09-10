<?php
session_start(); // mengaktifkan session

// pengecekan session login user
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
} else {
    // panggil file "database.php" untuk koneksi ke database
    require_once "../../config/database.php";

    // mengecek data GET "id_barang"
    if (isset($_GET['id'])) {
        // ambil data GET dari tombol hapus
        $id_barang = mysqli_real_escape_string($mysqli, $_GET['id']);

        // Ambil rak_id dari Barang yang Akan Dihapus
        $query = mysqli_query($mysqli, "SELECT rak_id FROM tbl_barang WHERE id_barang='$id_barang'");
        $data = mysqli_fetch_assoc($query);
        $rak_id = $data['rak_id'];

        // mengecek apakah data barang sudah digunakan dalam tabel "tbl_barang_keluar" atau "tbl_barang_masuk"
        $query_check_referensi = mysqli_query($mysqli, "SELECT 
            (SELECT COUNT(*) FROM tbl_barang_keluar WHERE barang='$id_barang') +
            (SELECT COUNT(*) FROM tbl_barang_masuk WHERE barang='$id_barang') as total")
            or die('Ada kesalahan pada query cek referensi : ' . mysqli_error($mysqli));

        $data_check_referensi = mysqli_fetch_assoc($query_check_referensi);
        $total_referensi = $data_check_referensi['total'];




        // cek apakah ada referensi yang menggunakan barang ini
        if ($total_referensi > 0) {
            // Jika ada referensi yang menggunakan barang ini, maka Anda tidak dapat menghapusnya.
            // Alihkan ke halaman barang dan tampilkan pesan gagal hapus data
            header('location: ../../main.php?module=barang&pesan=4');
        } else {
            // Data barang tidak memiliki referensi, maka Anda dapat melanjutkan proses penghapusan.

            // mengecek data foto barang
            // sql statement untuk menampilkan data "foto" dari tabel "tbl_barang" berdasarkan "id_barang"
            $query_foto_barang = mysqli_query($mysqli, "SELECT foto FROM tbl_barang WHERE id_barang='$id_barang'")
                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

            // ambil data hasil query
            $data_foto_barang = mysqli_fetch_assoc($query_foto_barang);
            $foto = $data_foto_barang['foto'];

            // jika data "foto" tidak kosong
            if (!empty($foto)) {
                // hapus file foto dari folder images
                $hapus_file = unlink("../../images/$foto");
            }

            // sql statement untuk delete data dari tabel "tbl_barang" berdasarkan "id_barang"
            $delete = mysqli_query($mysqli, "DELETE FROM tbl_barang WHERE id_barang='$id_barang'")
                or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));

            // cek query
            // jika proses delete berhasil
            if ($delete) {
                // Periksa Barang Lain dengan Rak yang Sama
                $check = mysqli_query($mysqli, "SELECT id_barang FROM tbl_barang WHERE rak_id='$rak_id'");
                $count = mysqli_num_rows($check);

                // Perbarui Status Rak
                if ($count == 0) {
                    $updateStatusQuery = "UPDATE location SET status='not active' WHERE id='$rak_id'";
                    $updateStatusResult = mysqli_query($mysqli, $updateStatusQuery);

                    if (!$updateStatusResult) {
                        die('Ada kesalahan pada query update status rak: ' . mysqli_error($mysqli));
                    }
                }

                // alihkan ke halaman barang dan tampilkan pesan berhasil hapus data
                header('location: ../../main.php?module=barang&pesan=3');
            }
        }
    }
}
