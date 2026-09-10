<?php
session_start(); // Mengaktifkan session

// Pengecekan session login user
// Jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // Alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
} else {
    // Panggil file "database.php" untuk koneksi ke database
    require_once "../../config/database.php";

    // Mengecek data GET "id"
    if (isset($_GET['id'])) {
        // Ambil data GET dari parameter URL
        $id_adjustment = mysqli_real_escape_string($mysqli, $_GET['id']);

        // Query untuk mengambil data penyesuaian
        $query_get_adjustment = mysqli_query($mysqli, "SELECT id_barang, jumlah_adjustment FROM adjustment WHERE id_adjustment = '$id_adjustment'");
        $data_adjustment = mysqli_fetch_assoc($query_get_adjustment);

        // Cek apakah data penyesuaian ditemukan
        if ($data_adjustment) {
            // Ambil ID barang dan jumlah penyesuaian dari hasil query
            $id_barang = $data_adjustment['id_barang'];
            $jumlah_adjustment = $data_adjustment['jumlah_adjustment'];

            // Update stok pada tabel tbl_barang dengan menambahkan jumlah penyesuaian
            $query_update_stok = mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok + '$jumlah_adjustment' WHERE id_barang = '$id_barang'");

            // Cek apakah query update berhasil
            if ($query_update_stok) {
                // Jika berhasil, hapus data penyesuaian dari tabel adjustment
                $query_delete_adjustment = mysqli_query($mysqli, "DELETE FROM adjustment WHERE id_adjustment = '$id_adjustment'");
                if ($query_delete_adjustment) {
                    // Alihkan ke halaman adjustment dan tampilkan pesan sukses
                    header('location: ../../main.php?module=adjustment&pesan=3');
                } else {
                    // Jika gagal menghapus data penyesuaian, tampilkan pesan error
                    header('location: ../../main.php?module=adjustment&pesan=4');
                }
            } else {
                // Jika gagal mengupdate stok, tampilkan pesan error
                header('location: ../../main.php?module=adjustment&pesan=5');
            }
        } else {
            // Jika data penyesuaian tidak ditemukan, alihkan ke halaman adjustment dengan pesan error
            header('location: ../../main.php?module=adjustment&pesan=6');
        }
    }
}
?>
