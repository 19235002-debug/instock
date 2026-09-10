<?php
// Memasukkan file koneksi database
include 'koneksi.php';

header('Content-Type: application/json');

$cache_file = __DIR__ . '/assets/cities_cache.json';

// Gunakan cache jika file ada dan belum 7 hari
if (file_exists($cache_file) && (time() - filemtime($cache_file) < 86400 * 7)) {
    $cached_data = file_get_contents($cache_file);
    $decoded = json_decode($cached_data, true);
    if (!empty($decoded) && is_array($decoded)) {
        echo $cached_data;
        exit;
    }
}

// Mendapatkan data kota dari Raja Ongkir
$api_key = '62466a2316383c4b94fcd7745d7ccbef';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.rajaongkir.com/starter/city");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('key: '.$api_key));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$cities = [];

if (isset($data['rajaongkir']['results'])) {
    $cities = $data['rajaongkir']['results'];
    @file_put_contents($cache_file, json_encode($cities));
} else if (file_exists($cache_file)) {
    $cities = json_decode(file_get_contents($cache_file), true);
}

echo json_encode($cities);

