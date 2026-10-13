<?php
/**
 * Migration Script: Tambah kolom kode_cabang pada tbl_cabang dan isi data awal
 */

if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
} else {
    die("File koneksi database tidak ditemukan.\n");
}

echo "=== MIGRASI KODE LOKASI AREA (TBL_CABANG) ===\n";

// 1. Tambah kolom kode_cabang jika belum ada
$check_col = mysqli_query($mysqli, "SHOW COLUMNS FROM tbl_cabang LIKE 'kode_cabang'");
if (mysqli_num_rows($check_col) == 0) {
    $alter = mysqli_query($mysqli, "ALTER TABLE tbl_cabang ADD COLUMN kode_cabang VARCHAR(20) AFTER id_cabang");
    if ($alter) {
        echo "Kolom 'kode_cabang' berhasil ditambahkan ke tbl_cabang.\n";
    } else {
        echo "Gagal menambahkan kolom 'kode_cabang': " . mysqli_error($mysqli) . "\n";
    }
} else {
    echo "Kolom 'kode_cabang' sudah ada.\n";
}

// 2. Update / Insert 4 data cabang default sesuai permintaan user
$cabang_data = [
    [
        'id' => 1,
        'kode' => 'GUD',
        'nama' => 'Gudang Utama (Pusat)',
        'pic' => '-',
        'alamat' => 'Jl. Jendral Sudirman No. 123, Jakarta Selatan',
        'telp' => '0812-3456-7890',
        'email' => 'admin@instock.co.id'
    ],
    [
        'id' => 2,
        'kode' => 'JKT',
        'nama' => 'Jakarta',
        'pic' => 'Jakarta Owner',
        'alamat' => 'tes alamat jakarta',
        'telp' => '123',
        'email' => 'jakarta@instock.co.id'
    ],
    [
        'id' => 3,
        'kode' => 'BDG',
        'nama' => 'Bandung',
        'pic' => 'Bandung Owner',
        'alamat' => 'Jl. Asia Afrika No. 45, Bandung',
        'telp' => '0812-3456-7891',
        'email' => 'bandung@instock.co.id'
    ],
    [
        'id' => 5,
        'kode' => 'SBY',
        'nama' => 'Surabaya',
        'pic' => 'Surabaya Owner',
        'alamat' => 'Jl. Pemuda No. 88, Surabaya',
        'telp' => '0812-3456-7892',
        'email' => 'surabaya@instock.co.id'
    ]
];

foreach ($cabang_data as $c) {
    $id = $c['id'];
    $kode = $c['kode'];
    $nama = mysqli_real_escape_string($mysqli, $c['nama']);
    $pic = mysqli_real_escape_string($mysqli, $c['pic']);
    $alamat = mysqli_real_escape_string($mysqli, $c['alamat']);
    $telp = mysqli_real_escape_string($mysqli, $c['telp']);
    $email = mysqli_real_escape_string($mysqli, $c['email']);

    $chk = mysqli_query($mysqli, "SELECT id_cabang FROM tbl_cabang WHERE id_cabang = '$id'");
    if (mysqli_num_rows($chk) > 0) {
        $upd = "UPDATE tbl_cabang SET 
                kode_cabang = '$kode',
                nama_cabang = '$nama',
                nama_fm = '$pic',
                alamat = '$alamat',
                nomor_telepon = '$telp',
                email = '$email'
                WHERE id_cabang = '$id'";
        mysqli_query($mysqli, $upd);
        echo "Updated Cabang ID $id ($kode - $nama)\n";
    } else {
        $ins = "INSERT INTO tbl_cabang (id_cabang, kode_cabang, nama_cabang, alamat, nama_fm, nomor_telepon, email)
                VALUES ('$id', '$kode', '$nama', '$alamat', '$pic', '$telp', '$email')";
        mysqli_query($mysqli, $ins);
        echo "Inserted Cabang ID $id ($kode - $nama)\n";
    }
}

echo "=== MIGRASI SELESAI ===\n";
?>
