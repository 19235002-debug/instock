<?php
require_once __DIR__ . '/../config/database.php';

// Cek rak_id yang dipakai di tbl_barang
$used_raks = [];
$res = mysqli_query($mysqli, "SELECT DISTINCT rak_id FROM tbl_barang WHERE rak_id IS NOT NULL AND rak_id != ''");
while ($row = mysqli_fetch_assoc($res)) {
    $used_raks[] = $row['rak_id'];
}

// Update rak lama (misal A-20-01) yang tidak dipakai ke format GUD-01-01 atau hapus jika duplikat
$old_res = mysqli_query($mysqli, "SELECT id, no_rak FROM location WHERE no_rak LIKE 'A-%'");
$migrated = 0;
while ($row = mysqli_fetch_assoc($old_res)) {
    $id = $row['id'];
    $old_no = $row['no_rak'];
    if (!in_array($id, $used_raks)) {
        // Hapus rak sampel lama A-xx-xx yang tidak dipakai
        mysqli_query($mysqli, "DELETE FROM location WHERE id = '$id'");
    } else {
        // Ganti awalan A- dengan GUD-
        $new_no = str_replace('A-', 'GUD-', $old_no);
        mysqli_query($mysqli, "UPDATE location SET no_rak = '$new_no' WHERE id = '$id'");
        $migrated++;
    }
}

echo "Cleaned old racks. Migrated used old racks: $migrated\n";
?>
