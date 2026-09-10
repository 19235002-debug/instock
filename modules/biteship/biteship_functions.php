<?php
// biteship_functions.php
require_once 'biteship_config.php';

/**
 * Fungsi untuk melacak resi pengiriman via API Biteship
 * @param string $waybill
 * @param string|null $courierCode
 * @return array
 */
function trackShipment($waybill, $courierCode = null)
{
    $waybill = trim($waybill);
    if (empty($waybill)) {
        return [
            'error' => true,
            'message' => 'Nomor resi tidak boleh kosong.'
        ];
    }

    $headers = [
        'Authorization: ' . BITESHIP_API_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init();

    if (!empty($courierCode)) {
        // GET /v1/trackings/{waybill}/couriers/{courier_code}
        $url = BITESHIP_BASE_URL . "/v1/trackings/" . urlencode($waybill) . "/couriers/" . urlencode($courierCode);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    } else {
        // POST /v1/trackings
        $url = BITESHIP_BASE_URL . "/v1/trackings";
        $postData = json_encode(['waybill' => $waybill]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    unset($ch);

    if ($curlError) {
        return [
            'error' => true,
            'message' => 'Koneksi API gagal: ' . $curlError
        ];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => true,
            'message' => 'Format tanggapan API tidak valid.'
        ];
    }

    if (isset($data['success']) && !$data['success']) {
        return [
            'error' => true,
            'message' => $data['error'] ?? $data['message'] ?? 'Nomor resi atau kurir tidak ditemukan.'
        ];
    }

    return [
        'error' => false,
        'data' => $data
    ];
}

/**
 * Fungsi untuk mengambil daftar kurir dari Biteship API
 * @return array
 */
function getCouriers()
{
    $url = BITESHIP_BASE_URL . '/v1/couriers';
    $headers = [
        'Authorization: ' . BITESHIP_API_KEY,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    unset($ch);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data['success']) || !$data['success'] || empty($data['couriers'])) {
        // Fallback kurir terpopuler jika API Biteship kurir offline/sandbox
        return [
            'error' => false,
            'data' => [
                ['courier_code' => 'jne', 'courier_name' => 'JNE Express', 'courier_service_name' => 'JNE Regular / YES'],
                ['courier_code' => 'sicepat', 'courier_name' => 'SiCepat Express', 'courier_service_name' => 'SiCepat REG / BEST'],
                ['courier_code' => 'jnt', 'courier_name' => 'J&T Express', 'courier_service_name' => 'J&T EZ'],
                ['courier_code' => 'anteraja', 'courier_name' => 'AnterAja', 'courier_service_name' => 'AnterAja Regular / Next Day'],
                ['courier_code' => 'pos', 'courier_name' => 'POS Indonesia', 'courier_service_name' => 'POS Kilat Khusus'],
                ['courier_code' => 'tiki', 'courier_name' => 'TIKI', 'courier_service_name' => 'TIKI REG / ONS'],
                ['courier_code' => 'ninja', 'courier_name' => 'Ninja Xpress', 'courier_service_name' => 'Ninja Regular'],
                ['courier_code' => 'lion', 'courier_name' => 'Lion Parcel', 'courier_service_name' => 'Lion Parcel REGPACK'],
                ['courier_code' => 'shopee', 'courier_name' => 'Shopee Express', 'courier_service_name' => 'SPX Standard']
            ]
        ];
    }

    $allowedCourierCodes = ['jne', 'tiki', 'pos', 'sicepat', 'anteraja', 'jnt', 'ninja', 'lion', 'shopee'];
    $filtered = array_filter($data['couriers'], function ($courier) use ($allowedCourierCodes) {
        return in_array(strtolower($courier['courier_code'] ?? ''), $allowedCourierCodes);
    });

    return [
        'error' => false,
        'data' => !empty($filtered) ? array_values($filtered) : $data['couriers']
    ];
}
