<?php
// rajaongkir_request.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kota_asal = $_POST['alamatAsal'] ?? null;
    $kota_tujuan = $_POST['alamatTujuan'] ?? null;
    $berat = $_POST['berat'] ?? null;
    $courier = $_POST['pilihanKurir'] ?? null;

    if (!$kota_asal || !$kota_tujuan || !$berat || !$courier) {
        echo '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>Semua kolom wajib diisi dengan benar.</div>';
        exit;
    }

    if (!is_numeric($berat) || $berat <= 0) {
        echo '<div class="alert alert-danger"><i class="fas fa-times-circle mr-2"></i>Berat barang harus berupa angka positif.</div>';
        exit;
    }

    // Fungsi helper untuk mengambil info kota dari cache lokal
    function getCityInfoFromCache($cityId) {
        $cacheFile = __DIR__ . '/assets/cities_cache.json';
        if (file_exists($cacheFile)) {
            $cities = json_decode(file_get_contents($cacheFile), true);
            if (is_array($cities)) {
                foreach ($cities as $c) {
                    if (($c['city_id'] ?? null) == $cityId) {
                        return $c;
                    }
                }
            }
        }
        return null;
    }

    // Fungsi helper untuk menghasilkan tarif estimasi jika API offline
    function getFallbackResult($courier, $originId, $destId, $weightGrams) {
        $weightKg = max(1, ceil($weightGrams / 1000));
        $isSameCity = ($originId == $destId);
        $baseRate = $isSameCity ? 9000 : 18000;
        
        $courier = strtolower($courier);
        $fullName = strtoupper($courier);
        $costs = [];

        if ($courier === 'jne') {
            $fullName = 'JNE Express';
            $costs = [
                [
                    'service' => 'REG',
                    'description' => 'Layanan Reguler',
                    'cost' => [['value' => $baseRate * $weightKg, 'etd' => '2-3']]
                ],
                [
                    'service' => 'YES',
                    'description' => 'Yakin Esok Sampai',
                    'cost' => [['value' => round($baseRate * 1.6) * $weightKg, 'etd' => '1-1']]
                ],
                [
                    'service' => 'OKE',
                    'description' => 'Ongkos Kirim Ekonomis',
                    'cost' => [['value' => round($baseRate * 0.8) * $weightKg, 'etd' => '3-5']]
                ]
            ];
        } else if ($courier === 'tiki') {
            $fullName = 'TIKI (Titipan Kilat)';
            $costs = [
                [
                    'service' => 'REG',
                    'description' => 'Regular Service',
                    'cost' => [['value' => round($baseRate * 0.95) * $weightKg, 'etd' => '2-3']]
                ],
                [
                    'service' => 'ONS',
                    'description' => 'Over Night Service',
                    'cost' => [['value' => round($baseRate * 1.5) * $weightKg, 'etd' => '1-1']]
                ],
                [
                    'service' => 'ECO',
                    'description' => 'Economy Service',
                    'cost' => [['value' => round($baseRate * 0.75) * $weightKg, 'etd' => '3-4']]
                ]
            ];
        } else if ($courier === 'pos') {
            $fullName = 'POS Indonesia';
            $costs = [
                [
                    'service' => 'Pos Kilat Khusus',
                    'description' => 'Pos Kilat Khusus',
                    'cost' => [['value' => round($baseRate * 0.9) * $weightKg, 'etd' => '2-4']]
                ],
                [
                    'service' => 'Pos Express',
                    'description' => 'Pos Express',
                    'cost' => [['value' => round($baseRate * 1.45) * $weightKg, 'etd' => '1-1']]
                ]
            ];
        }

        return [
            'code' => $courier,
            'name' => $fullName,
            'costs' => $costs
        ];
    }

    $apiKey = '62466a2316383c4b94fcd7745d7ccbef';
    $url = 'https://api.rajaongkir.com/starter/cost';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query(array(
            "origin" => $kota_asal,
            "destination" => $kota_tujuan,
            "weight" => $berat,
            "courier" => strtolower($courier)
        )),
        CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded",
            "key: " . $apiKey
        ),
        CURLOPT_SSL_VERIFYPEER => false
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $isOfflineFallback = false;
    $result = null;
    $originText = 'Asal';
    $destText = 'Tujuan';

    if ($err || empty($response)) {
        // Fallback jika API tidak dapat dijangkau
        $isOfflineFallback = true;
        $result = getFallbackResult($courier, $kota_asal, $kota_tujuan, $berat);
        
        $originCity = getCityInfoFromCache($kota_asal);
        $destCity = getCityInfoFromCache($kota_tujuan);

        if ($originCity) {
            $originText = ($originCity['type'] ?? '') . ' ' . ($originCity['city_name'] ?? '') . ', ' . ($originCity['province'] ?? '');
        }
        if ($destCity) {
            $destText = ($destCity['type'] ?? '') . ' ' . ($destCity['city_name'] ?? '') . ', ' . ($destCity['province'] ?? '');
        }
    } else {
        $data = json_decode($response, true);
        if (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] != 200) {
            $isOfflineFallback = true;
            $result = getFallbackResult($courier, $kota_asal, $kota_tujuan, $berat);
            $originCity = getCityInfoFromCache($kota_asal);
            $destCity = getCityInfoFromCache($kota_tujuan);
            if ($originCity) $originText = ($originCity['type'] ?? '') . ' ' . ($originCity['city_name'] ?? '') . ', ' . ($originCity['province'] ?? '');
            if ($destCity) $destText = ($destCity['type'] ?? '') . ' ' . ($destCity['city_name'] ?? '') . ', ' . ($destCity['province'] ?? '');
        } else {
            $result = $data['rajaongkir']['results'][0] ?? null;
            $originDetails = $data['rajaongkir']['origin_details'] ?? null;
            $destDetails = $data['rajaongkir']['destination_details'] ?? null;
            if ($originDetails) $originText = $originDetails['city_name'] . ', ' . $originDetails['province'];
            if ($destDetails) $destText = $destDetails['city_name'] . ', ' . $destDetails['province'];
        }
    }

    if ($result && !empty($result['costs'])) {
        $courierName = strtoupper($result['code']);
        $fullName = $result['name'] ?? $courierName;

        echo '<div class="p-3">';

        if ($isOfflineFallback) {
            echo '<div class="alert alert-info py-2 px-3 mb-3 border-0 rounded" style="background: rgba(23, 162, 184, 0.1); color: #0c5460;">';
            echo '<i class="fas fa-info-circle mr-2 text-info"></i><strong>Mode Estimasi Lokal:</strong> API RajaOngkir tidak dapat dijangkau. Menampilkan estimasi tarif standar pengiriman.';
            echo '</div>';
        }

        echo '<div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border">';
        echo '<div>';
        echo '<h5 class="mb-1 font-weight-bold text-secondary"><i class="fas fa-truck mr-2"></i>' . htmlspecialchars($fullName) . '</h5>';
        echo '<small class="text-muted"><i class="fas fa-map-marker-alt mr-1 text-danger"></i> ' . htmlspecialchars($originText) . ' &rarr; <i class="fas fa-map-marked-alt mr-1 text-success"></i> ' . htmlspecialchars($destText) . ' (' . number_format($berat / 1000, 2) . ' kg)</small>';
        echo '</div>';
        echo '<span class="badge badge-secondary p-2 font-weight-bold"><i class="fas fa-list mr-1"></i>' . count($result['costs']) . ' Layanan Tersedia</span>';
        echo '</div>';

        echo '<div class="table-responsive">';
        echo '<table class="table table-hover table-striped border mb-0" style="border-radius: 8px; overflow: hidden;">';
        echo '<thead class="bg-secondary text-white">';
        echo '<tr>';
        echo '<th>Layanan</th>';
        echo '<th>Deskripsi</th>';
        echo '<th class="text-center">Estimasi Pengiriman (ETD)</th>';
        echo '<th class="text-right">Tarif / Biaya</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($result['costs'] as $item) {
            $service = $item['service'];
            $description = $item['description'];
            $costVal = $item['cost'][0]['value'] ?? 0;
            $etd = $item['cost'][0]['etd'] ?? '-';
            
            // Format ETD jika berisi angka jam/hari
            if (!empty($etd) && is_numeric(str_replace(['-', ' '], '', $etd))) {
                $etdText = $etd . ' Hari';
            } else {
                $etdText = !empty($etd) ? $etd : '-';
            }

            echo '<tr>';
            echo '<td class="font-weight-bold text-secondary"><span class="badge badge-secondary mr-2">' . htmlspecialchars($service) . '</span></td>';
            echo '<td>' . htmlspecialchars($description) . '</td>';
            echo '<td class="text-center"><i class="far fa-clock text-info mr-1"></i> ' . htmlspecialchars($etdText) . '</td>';
            echo '<td class="text-right font-weight-bold text-success" style="font-size: 1.1rem;">Rp ' . number_format($costVal, 0, ',', '.') . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning text-center py-4 mb-0"><i class="fas fa-search fa-2x mb-2 d-block"></i>Tidak ada opsi tarif pengiriman yang ditemukan untuk rute ini.</div>';
    }
}

