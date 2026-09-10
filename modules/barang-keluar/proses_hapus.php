<?php
session_start(); // Mengaktifkan session

// Pengecekan session login user
// Jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // Alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
} else {
    // Cek hak akses pengguna saat ini
    $hak_akses = $_SESSION['hak_akses'];

    // Cek apakah hak akses adalah "Admin Gudang"
    if ($hak_akses == 'Admin Gudang') {
        // Jika hak akses adalah "Admin Gudang," tampilkan pesan error atau alihkan pengguna ke halaman lain
        header('location: ../../main.php?module=barang_keluar&pesan=3');
        exit; // Hentikan eksekusi skrip
    }

    // Panggil file "database.php" untuk koneksi ke database
    require_once "../../config/database.php";

    // Mengecek data GET "id_transaksi"
    if (isset($_GET['id'])) {
        // Ambil data GET dari tombol hapus
        $id_transaksi = mysqli_real_escape_string($mysqli, $_GET['id']);

        // Check transaction status and rollback stock if Approved
        $q_trx = mysqli_query($mysqli, "SELECT barang, jumlah, status FROM tbl_barang_keluar WHERE id_transaksi='$id_transaksi'");
        if ($r_trx = mysqli_fetch_assoc($q_trx)) {
            if ($r_trx['status'] === 'Approved') {
                $b_id = mysqli_real_escape_string($mysqli, $r_trx['barang']);
                $b_qty = (int)$r_trx['jumlah'];
                mysqli_query($mysqli, "UPDATE tbl_barang SET stok = stok + $b_qty WHERE id_barang = '$b_id'");
            }
            $delete = mysqli_query($mysqli, "DELETE FROM tbl_barang_keluar WHERE id_transaksi='$id_transaksi'");
            if ($delete) {
                header('location: ../../main.php?module=barang_keluar&pesan=2');
                exit;
            }
        }

        header('location: ../../main.php?module=barang_keluar&pesan=3');
        exit;
    }
}
?>
