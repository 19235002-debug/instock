<?php
// generate_barcode.php - Handler Pembuat Gambar Barcode
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

$id = $_GET['id'] ?? '';

if (!empty($id)) {
    try {
        $generator = new BarcodeGeneratorPNG();
        // Hasilkan data barcode format Code 128
        $barcodeData = $generator->getBarcode($id, $generator::TYPE_CODE_128, 2, 40);
        
        if (!empty($barcodeData)) {
            header('Content-Type: image/png');
            header('Content-Length: ' . strlen($barcodeData));
            header('Cache-Control: public, max-age=86400');
            echo $barcodeData;
            exit;
        }
    } catch (\Throwable $e) {
        // Fallback jika terjadi galat saat generate
    }
}

// Fallback gambar jika ID kosong atau gagal
header('Content-Type: image/png');
if (function_exists('imagecreate')) {
    $im = imagecreate(140, 40);
    $bg = imagecolorallocate($im, 245, 245, 245);
    $textcolor = imagecolorallocate($im, 120, 120, 120);
    imagestring($im, 3, 25, 12, "NO BARCODE", $textcolor);
    imagepng($im);
    imagedestroy($im);
} else {
    // 1x1 transparent PNG fallback
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
}
