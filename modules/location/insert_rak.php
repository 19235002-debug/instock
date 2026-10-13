<?php

// Sertakan file koneksi database Anda
include '../../config/database.php';

// Generator Rak Multicabang Opsi B: Kode Lokasi (GUD, JKT, BDG, SBY), Baris (01-04), Level (01-03), Slot (A-C)
$q_cabang = mysqli_query($mysqli, "SELECT kode_cabang FROM tbl_cabang WHERE kode_cabang IS NOT NULL AND kode_cabang != ''");
$kodes = [];
while ($row = mysqli_fetch_assoc($q_cabang)) {
    $kodes[] = strtoupper($row['kode_cabang']);
}
if (empty($kodes)) {
    $kodes = ['GUD', 'JKT', 'BDG', 'SBY'];
}

$inserted_count = 0;
$slots = ['A', 'B', 'C'];

// Ambil MAX ID numeric
$max_res = mysqli_query($mysqli, "SELECT MAX(CAST(id AS UNSIGNED)) as max_id FROM location");
$max_id = (int)(mysqli_fetch_assoc($max_res)['max_id'] ?? 0);

foreach ($kodes as $kode) {
    for ($baris = 1; $baris <= 4; $baris++) {
        for ($level = 1; $level <= 3; $level++) {
            foreach ($slots as $slot) {
                $formatted_baris = sprintf('%02d', $baris);
                $formatted_level = sprintf('%02d', $level);
                $rak_number = "$kode-$formatted_baris-$formatted_level-$slot";

                // Cek apakah rak sudah ada
                $check = mysqli_query($mysqli, "SELECT id FROM location WHERE no_rak = '$rak_number'");
                if (mysqli_num_rows($check) == 0) {
                    $max_id++;
                    $new_id = (string)$max_id;
                    $query = "INSERT INTO location (id, no_rak, status) VALUES ('$new_id', '$rak_number', 'not active')";
                    mysqli_query($mysqli, $query) or die("Error: " . mysqli_error($mysqli));
                    $inserted_count++;
                }
            }
        }
    }
}

echo "Berhasil memasukkan $inserted_count data rak lokasi baru (Format: KODE_LOKASI-BARIS-LEVEL-SLOT, contoh: JKT-01-01-A)!";
?>

