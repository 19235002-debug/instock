<?php
/**
 * Script untuk membuat/memperbarui rak berdasarkan OPSI B:
 * Format: [KODE_LOKASI]-[BARIS]-[LEVEL]-[SLOT/BIN]
 * Contoh: JKT-01-01-A, JKT-01-01-B, JKT-01-01-C, GUD-01-02-A, BDG-02-01-B, SBY-03-02-C
 */

if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
} else {
    die("File koneksi database tidak ditemukan.\n");
}

echo "=== MENGUPDATE PENOMORAN RAK OPSI B (KODE_LOKASI-BARIS-LEVEL-SLOT) ===\n";

// Kosongkan FK di tbl_barang jika ada yang menunjuk ke location lama, agar bisa direbuild bersih
mysqli_query($mysqli, "UPDATE tbl_barang SET rak_id = NULL WHERE rak_id IS NOT NULL");
mysqli_query($mysqli, "UPDATE tbl_barang_masuk_detail SET rak_id = NULL WHERE rak_id IS NOT NULL");
mysqli_query($mysqli, "UPDATE tbl_stok_batch SET rak_id = NULL WHERE rak_id IS NOT NULL");

// Truncate / reset tabel location
mysqli_query($mysqli, "DELETE FROM location");

// Ambil semua cabang yang memiliki kode_cabang
$q_cab = mysqli_query($mysqli, "SELECT id_cabang, kode_cabang, nama_cabang FROM tbl_cabang WHERE kode_cabang IS NOT NULL AND kode_cabang != ''");
$cabang_list = [];
while ($row = mysqli_fetch_assoc($q_cab)) {
    $cabang_list[] = $row;
}

$added_count = 0;
$id_counter = 1;

$slots = ['A', 'B', 'C'];

foreach ($cabang_list as $cab) {
    $kode = strtoupper($cab['kode_cabang']);
    echo "Memproses Lokasi Area: {$cab['nama_cabang']} (Kode: $kode)...\n";

    for ($baris = 1; $baris <= 4; $baris++) {
        for ($level = 1; $level <= 3; $level++) {
            foreach ($slots as $slot) {
                $fb = sprintf('%02d', $baris);
                $fl = sprintf('%02d', $level);
                $no_rak = "$kode-$fb-$fl-$slot";

                $id_str = (string)$id_counter++;
                mysqli_query($mysqli, "INSERT INTO location (id, no_rak, status) VALUES ('$id_str', '$no_rak', 'not active')");
                $added_count++;
            }
        }
    }
}

// Hubungkan kembali barang-barang yang ada di tbl_barang ke rak_id baru di cabangnya
$q_barang = mysqli_query($mysqli, "SELECT id_barang, id_cabang FROM tbl_barang");
while ($b = mysqli_fetch_assoc($q_barang)) {
    $id_b = $b['id_barang'];
    $id_c = $b['id_cabang'];

    // Cari kode_cabang
    $q_c = mysqli_query($mysqli, "SELECT kode_cabang FROM tbl_cabang WHERE id_cabang = '$id_c'");
    $r_c = mysqli_fetch_assoc($q_c);
    $kode_c = strtoupper($r_c['kode_cabang'] ?? 'GUD');

    // Cari 1 rak yang belum terpakai di cabang tersebut
    $q_avail = mysqli_query($mysqli, "SELECT l.id FROM location l 
                                       LEFT JOIN tbl_barang tb ON l.id = tb.rak_id AND tb.id_cabang = '$id_c' 
                                       WHERE l.no_rak LIKE '$kode_c-%' AND tb.rak_id IS NULL 
                                       ORDER BY l.no_rak ASC LIMIT 1");
    if ($r_avail = mysqli_fetch_assoc($q_avail)) {
        $rak_id_new = $r_avail['id'];
        mysqli_query($mysqli, "UPDATE tbl_barang SET rak_id = '$rak_id_new' WHERE id_barang = '$id_b'");
        mysqli_query($mysqli, "UPDATE location SET status = 'active' WHERE id = '$rak_id_new'");
    }
}

echo "Selesai! Total rak lokasi OPSI B ditambahkan: $added_count\n";
?>
