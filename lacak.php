<?php
// lacak.php

// Ganti dengan API Key Anda yang didapat dari Raja Ongkir
$apiKey = '62466a2316383c4b94fcd7745d7ccbef';
$result = "";

// Periksa apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['waybill']) && isset($_POST['courier'])) {
    $waybill = $_POST['waybill'];
    $courier = $_POST['courier'];

    // Endpoint untuk cek resi
    $url = 'https://api.rajaongkir.com/starter/waybill';

    // Data yang akan dikirim ke API Raja Ongkir
    $postData = array(
        'waybill' => $waybill,
        'courier' => $courier
    );

    // Header untuk request
    $headers = array(
        'key: ' . $apiKey,
        'content-type: application/x-www-form-urlencoded'
    );

    // Inisialisasi cURL
    $ch = curl_init();

    // Set opsi untuk cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Eksekusi cURL dan simpan responsenya
    $response = curl_exec($ch);

    // Cek apakah terjadi error
    if (curl_errno($ch)) {
        $result = 'Request Error:' . curl_error($ch);
    } else {
        // Tampilkan response dari Raja Ongkir
        $result = $response;
    }

    // Tutup sesi cURL
    curl_close($ch);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pesan error jika waybill atau courier tidak di-set
    $result = "Nomor resi dan kurir harus diisi.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lacak Resi</title>
</head>

<body>

    <!-- Jika ada hasil dari pemrosesan API, tampilkan di sini -->
    <?php if (!empty($result)) : ?>
        <div><?php echo $result; ?></div>
    <?php endif; ?>

    <form action="lacak.php" method="post">
        <label for="waybill">Nomor Resi:</label>
        <input type="text" id="waybill" name="waybill" required>

        <label for="courier">Kurir:</label>
        <select id="courier" name="courier" required>
            <option value="">-- Pilih Kurir --</option>
            <option value="jne">JNE</option>
            <option value="pos">POS Indonesia</option>
            <option value="tiki">TIKI</option>
            <!-- Tambahkan opsi kurir lain sesuai dengan yang didukung oleh Raja Ongkir -->
        </select>

        <button type="submit">Lacak Resi</button>
    </form>

</body>

</html>