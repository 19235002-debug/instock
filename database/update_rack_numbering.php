<?php
/**
 * Script Update/Migrasi Format Penomoran Rak Gudang
 * Format Lama: A-001, A-002
 * Format Baru Multi-Cabang: ZONA-BARIS-LEVEL (contoh: A-01-01, A-01-02, B-01-01)
 */

if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
} else {
    die("File koneksi database tidak ditemukan.\n");
}

echo "=== MENGUPDATE FORMAT PENOMORAN RAK LOKASI ===\n";

$res = mysqli_query($mysqli, "SELECT id, no_rak FROM location");
$updated_count = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $id = $row['id'];
        $old_no = $row['no_rak'];

        // Jika format lama seperti A-001 atau A-010 (satu strip, 3 angka dibelakang)
        if (preg_match('/^([A-Z])\-(\d+)$/i', $old_no, $matches)) {
            $zone = strtoupper($matches[1]);
            $num = (int)$matches[2];

            // Tentukan Baris dan Level berdasarkan nomor urut (5 level per baris)
            $baris = ceil($num / 4);
            $level = (($num - 1) % 4) + 1;

            $formatted_baris = sprintf('%02d', $baris);
            $formatted_level = sprintf('%02d', $level);

            $new_no = "$zone-$formatted_baris-$formatted_level";

            // Update ke database
            $upd = mysqli_query($mysqli, "UPDATE location SET no_rak = '$new_no' WHERE id = '$id'");
            if ($upd) {
                echo "Updated ID $id: $old_no => $new_no\n";
                $updated_count++;
            }
        }
    }
}

echo "Selesai! Total rak diperbarui: $updated_count\n";

// Jika data lokasi masih sedikit, jalankan generator rak standar
$cnt_res = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM location");
$cnt = mysqli_fetch_assoc($cnt_res)['total'] ?? 0;

if ($cnt < 20) {
    echo "Menambahkan rak standar multi-cabang (A-01-01 s/d D-05-04)...\n";
    $zones = ['A', 'B', 'C', 'D'];
    $added = 0;
    foreach ($zones as $z) {
        for ($b = 1; $b <= 5; $b++) {
            for ($l = 1; $l <= 4; $l++) {
                $fb = sprintf('%02d', $b);
                $fl = sprintf('%02d', $l);
                $rak_code = "$z-$fb-$fl";

                $chk = mysqli_query($mysqli, "SELECT id FROM location WHERE no_rak = '$rak_code'");
                if (mysqli_num_rows($chk) == 0) {
                    mysqli_query($mysqli, "INSERT INTO location (no_rak, status) VALUES ('$rak_code', 'not active')");
                    $added++;
                }
            }
        }
    }
    echo "Berhasil menambahkan $added lokasi rak baru.\n";
}

?>
