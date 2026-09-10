<?php

// Sertakan file koneksi database Anda
include '../../config/database.php';



// Mulai proses inser
for ($i = 1; $i <= 100; $i++) {
    // Format nomor agar memiliki 3 digit
    $formatted_number = sprintf('%03d', $i);
    $rak_number = "A-$formatted_number";

    // Masukkan ke database
    $query = "INSERT INTO location (no_rak, status) VALUES ('$rak_number', 'not active')";
    mysqli_query($mysqli, $query) or die("Error: " . mysqli_error($mysqli));
}

echo "Data telah dimasukkan!";

?>
