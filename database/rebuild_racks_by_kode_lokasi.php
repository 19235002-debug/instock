<?php
/**
 * Script untuk memperbarui / memasukkan penomoran rak berdasarkan Kode Lokasi Area (kode_cabang)
 * Format Baru: [KODE_LOKASI]-[BARIS]-[LEVEL] (misal JKT-01-01, BDG-01-01, SBY-01-01, GUD-01-01)
 */

if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
} else {
    die("File koneksi database tidak ditemukan.\n");
}

echo "=== MENGUPDATE PENOMORAN RAK DENGAN KODE LOKASI AREA ===\n";

// Ambil MAX ID numeric
$max_res = mysqli_query($mysqli, "SELECT MAX(CAST(id AS UNSIGNED)) as max_id FROM location");
$max_id = (int)(mysqli_fetch_assoc($max_res)['max_id'] ?? 0);

// Ambil semua cabang yang memiliki kode_cabang
$q_cab = mysqli_query($mysqli, "SELECT id_cabang, kode_cabang, nama_cabang FROM tbl_cabang WHERE kode_cabang IS NOT NULL AND kode_cabang != ''");
$cabang_list = [];
while ($row = mysqli_fetch_assoc($q_cab)) {
    $cabang_list[] = $row;
}

$added_count = 0;

foreach ($cabang_list as $cab) {
    $kode = strtoupper($cab['kode_cabang']);
    echo "Memproses Lokasi Area: {$cab['nama_cabang']} (Kode: $kode)...\n";

    for ($baris = 1; $baris <= 5; $baris++) {
        for ($level = 1; $level <= 4; $level++) {
            $fb = sprintf('%02d', $baris);
            $fl = sprintf('%02d', $level);
            $no_rak = "$kode-$fb-$fl";

            $chk = mysqli_query($mysqli, "SELECT id FROM location WHERE no_rak = '$no_rak'");
            if (mysqli_num_rows($chk) == 0) {
                $max_id++;
                $new_id = (string)$max_id;
                mysqli_query($mysqli, "INSERT INTO location (id, no_rak, status) VALUES ('$new_id', '$no_rak', 'not active')");
                $added_count++;
            }
        }
    }
}

echo "Selesai! Total rak lokasi area baru ditambahkan: $added_count\n";
?>
