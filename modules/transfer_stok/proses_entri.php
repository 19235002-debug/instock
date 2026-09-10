<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    header('location: ../../login.php?pesan=2');
    exit;
}

require_once "../../config/database.php";
require_once "../../helper/permission_helper.php";

protectProcessScript(['Super Admin', 'Administrator', 'Kepala Gudang'], 'transfer_stok');

if (isset($_POST['simpan'])) {
    $tanggal_transfer = mysqli_real_escape_string($mysqli, $_POST['tanggal_transfer']);
    $nama_barang = mysqli_real_escape_string($mysqli, trim($_POST['nama_barang']));
    $jumlah_transfer = (int)$_POST['jumlah_transfer'];
    $id_cabang_asal = (int)$_POST['id_cabang_asal'];
    $id_cabang_tujuan = (int)$_POST['id_cabang_tujuan'];

    $tanggal_transfer = date('Y-m-d', strtotime($tanggal_transfer));

    if ($id_cabang_asal === $id_cabang_tujuan) {
        header('location: ../../main.php?module=transfer_stok&pesan=2');
        exit;
    }

    // Cek ketersediaan stok cabang asal
    $q_stok_asal = mysqli_query($mysqli, "SELECT stok FROM tbl_barang WHERE nama_barang = '$nama_barang' AND id_cabang = '$id_cabang_asal'");
    $r_stok_asal = mysqli_fetch_assoc($q_stok_asal);
    $stok_asal = (int)($r_stok_asal['stok'] ?? 0);

    if ($jumlah_transfer <= 0 || $jumlah_transfer > $stok_asal) {
        header('location: ../../main.php?module=transfer_stok&pesan=3');
        exit;
    }

    // Mengurangi stok dari cabang asal berdasarkan nama barang
    $reduce_stock_query = "UPDATE tbl_barang SET stok = stok - $jumlah_transfer WHERE nama_barang = '$nama_barang' AND id_cabang = '$id_cabang_asal'";
    mysqli_query($mysqli, $reduce_stock_query);

    // Menambahkan stok ke cabang tujuan
    $check_query = mysqli_query($mysqli, "SELECT * FROM tbl_barang WHERE nama_barang = '$nama_barang' AND id_cabang = '$id_cabang_tujuan'");
    if ($check_query && mysqli_num_rows($check_query) > 0) {
        $add_stock_query = "UPDATE tbl_barang SET stok = stok + $jumlah_transfer WHERE nama_barang = '$nama_barang' AND id_cabang = '$id_cabang_tujuan'";
        mysqli_query($mysqli, $add_stock_query);
    } else {
        // Copy atribut barang jika belum ada di cabang tujuan
        $q_src = mysqli_query($mysqli, "SELECT jenis, stok_minimum, satuan, foto, rak_id FROM tbl_barang WHERE nama_barang = '$nama_barang' AND id_cabang = '$id_cabang_asal' LIMIT 1");
        $src_b = mysqli_fetch_assoc($q_src);
        $jenis_src = (int)($src_b['jenis'] ?? 1);
        $stok_min_src = (int)($src_b['stok_minimum'] ?? 1);
        $satuan_src = (int)($src_b['satuan'] ?? 1);
        $foto_src = mysqli_real_escape_string($mysqli, $src_b['foto'] ?? '');
        $rak_src = mysqli_real_escape_string($mysqli, $src_b['rak_id'] ?? '');

        // Generate ID barang baru (B00XX)
        $q_max = mysqli_query($mysqli, "SELECT MAX(id_barang) as max_id FROM tbl_barang");
        $r_max = mysqli_fetch_assoc($q_max);
        $last_id = $r_max['max_id'] ?? 'B0000';
        $num = (int)substr($last_id, 1) + 1;
        $new_id = 'B' . sprintf("%04d", $num);

        $insert_stock_query = "INSERT INTO tbl_barang (id_barang, nama_barang, jenis, stok_minimum, stok, satuan, foto, rak_id, id_cabang) 
                               VALUES ('$new_id', '$nama_barang', '$jenis_src', '$stok_min_src', $jumlah_transfer, '$satuan_src', '$foto_src', '$rak_src', '$id_cabang_tujuan')";
        mysqli_query($mysqli, $insert_stock_query);
    }

    // Menyimpan data ke tabel tbl_transfer_stok
    $query = "INSERT INTO tbl_transfer_stok(tanggal_transfer, nama_barang, jumlah_transfer, id_cabang_asal, id_cabang_tujuan)
              VALUES('$tanggal_transfer', '$nama_barang', '$jumlah_transfer', '$id_cabang_asal', '$id_cabang_tujuan')";
    $result = mysqli_query($mysqli, $query);

    if ($result) {
        header('location: ../../main.php?module=transfer_stok&pesan=1');
        exit;
    } else {
        echo "Gagal menyimpan data. " . mysqli_error($mysqli);
    }
} else {
    header('location: ../../main.php?module=transfer_stok');
    exit;
}
