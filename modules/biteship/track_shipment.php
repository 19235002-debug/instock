<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
    exit;
}

require_once 'biteship_functions.php';

$trackingInfo = null;
$hasError = false;
$errorMsg = '';
$searchedWaybill = '';
$searchedCourier = '';

// Ambil daftar kurir
$couriersResult = getCouriers();
$couriers = !$couriersResult['error'] ? $couriersResult['data'] : [];

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['waybill'])) {
    $searchedWaybill = trim($_POST['waybill']);
    $searchedCourier = $_POST['courier_code'] ?? null;

    if (!empty($searchedWaybill)) {
        $result = trackShipment($searchedWaybill, $searchedCourier);

        if (is_array($result)) {
            $hasError = $result['error'];
            $errorMsg = $result['message'] ?? 'Terjadi kesalahan tidak terduga saat melacak paket.';
            $trackingInfo = !$hasError ? $result['data'] : null;
        } else {
            $hasError = true;
            $errorMsg = 'Terjadi kesalahan pada sistem.';
        }
    } else {
        $hasError = true;
        $errorMsg = 'Silakan masukkan nomor resi yang valid.';
    }
}
?>

<!-- Header Halaman -->
<div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
        <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
            <div class="page-header text-white">
                <h4 class="page-title text-white"><i class="fas fa-shipping-fast mr-2"></i> Track Shipment</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=biteship_tracking" class="text-white">Lacak Pengiriman</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="row">
        <!-- Form Lacak Resi -->
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-search mr-2 text-secondary"></i> Cari Status Resi Paket</div>
                </div>
                <div class="card-body">
                    <form action="main.php?module=biteship_tracking" method="post" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="waybill">Nomor Resi / Waybill <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    </div>
                                    <input type="text" id="waybill" name="waybill" class="form-control" placeholder="Masukkan nomor resi (cth: JNE123456789)" value="<?php echo htmlspecialchars($searchedWaybill); ?>" autocomplete="off" required>
                                </div>
                                <div class="invalid-feedback">Nomor resi tidak boleh kosong.</div>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="courier_code">Pilih Kurir Expedisi</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-truck"></i></span>
                                    </div>
                                    <select id="courier_code" name="courier_code" class="form-control">
                                        <option value="">-- Deteksi Otomatis / Pilih Kurir --</option>
                                        <?php foreach ($couriers as $courier) : ?>
                                            <?php 
                                                $code = strtolower($courier['courier_code'] ?? '');
                                                $name = $courier['courier_name'] ?? strtoupper($code);
                                                $service = $courier['courier_service_name'] ?? '';
                                                $selected = ($searchedCourier === $code) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo htmlspecialchars($code); ?>" <?php echo $selected; ?>>
                                                <?php echo htmlspecialchars(strtoupper($code) . ' - ' . ($name ?: $service)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2 form-group d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary btn-round btn-block">
                                    <i class="fas fa-search mr-1"></i> Lacak
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hasil Lacak Resi -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
            <div class="col-md-12">
                <?php if ($hasError) : ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('error', 'Lacak Paket Gagal', '<?php echo addslashes($errorMsg); ?>');
                        });
                    </script>
                    <div class="card card-round border-danger">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <h4 class="text-danger font-weight-bold">Pengiriman Tidak Ditemukan</h4>
                            <p class="text-muted"><?php echo htmlspecialchars($errorMsg); ?></p>
                        </div>
                    </div>
                <?php else : ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('success', 'Resi Ditemukan!', 'Status terkini pengiriman berhasil dimuat.');
                        });
                    </script>
                    <?php
                        $statusRaw = strtolower($trackingInfo['status'] ?? 'unknown');
                        $badgeClass = 'badge-secondary';
                        $statusLabel = strtoupper($statusRaw);

                        if (in_array($statusRaw, ['delivered', 'success', 'completed', 'selesai'])) {
                            $badgeClass = 'badge-success';
                            $statusLabel = 'DELIVERED / TERKIRIM';
                        } elseif (in_array($statusRaw, ['on_process', 'in_transit', 'delivering', 'picking_up', 'out_for_delivery'])) {
                            $badgeClass = 'badge-info';
                            $statusLabel = 'IN TRANSIT / DALAM PERJALANAN';
                        } elseif (in_array($statusRaw, ['dropped_off', 'picked_up', 'allocated'])) {
                            $badgeClass = 'badge-warning';
                            $statusLabel = 'PICKED UP / DITERIMA KURIR';
                        } elseif (in_array($statusRaw, ['cancelled', 'rejected', 'failed', 'gagal'])) {
                            $badgeClass = 'badge-danger';
                            $statusLabel = 'GAGAL / DIBATALKAN';
                        }
                    ?>
                    <div class="row">
                        <!-- Ringkasan Informasi Paket -->
                        <div class="col-md-5">
                            <div class="card card-round">
                                <div class="card-header bg-secondary text-white">
                                    <div class="card-title text-white"><i class="fas fa-info-circle mr-2"></i> Detail Pengiriman</div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless table-striped">
                                        <tr>
                                            <th width="40%">Status Terkini</th>
                                            <td><span class="badge <?php echo $badgeClass; ?> p-2 font-weight-bold"><?php echo $statusLabel; ?></span></td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Resi</th>
                                            <td class="font-weight-bold text-secondary"><?php echo htmlspecialchars($trackingInfo['waybill_id'] ?? $searchedWaybill); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Kurir Expidisi</th>
                                            <td>
                                                <i class="fas fa-box mr-1"></i>
                                                <?php echo htmlspecialchars(strtoupper($trackingInfo['courier']['company'] ?? $trackingInfo['courier']['name'] ?? $searchedCourier ?: 'Ekspedisi')); ?>
                                            </td>
                                        </tr>
                                        <?php if (!empty($trackingInfo['courier']['driver_name'])) : ?>
                                            <tr>
                                                <th>Kurir / Pengantar</th>
                                                <td><?php echo htmlspecialchars($trackingInfo['courier']['driver_name']); ?> <?php echo !empty($trackingInfo['courier']['driver_phone']) ? '(' . htmlspecialchars($trackingInfo['courier']['driver_phone']) . ')' : ''; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($trackingInfo['origin']['contact_name']) || !empty($trackingInfo['origin']['address'])) : ?>
                                            <tr>
                                                <th>Pengirim / Asal</th>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($trackingInfo['origin']['contact_name'] ?? '-'); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($trackingInfo['origin']['address'] ?? '-'); ?></small>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($trackingInfo['destination']['contact_name']) || !empty($trackingInfo['destination']['address'])) : ?>
                                            <tr>
                                                <th>Penerima / Tujuan</th>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($trackingInfo['destination']['contact_name'] ?? '-'); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($trackingInfo['destination']['address'] ?? '-'); ?></small>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Riwayat Perjalanan Paket -->
                        <div class="col-md-7">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-title"><i class="fas fa-history mr-2 text-secondary"></i> Riwayat Perjalanan Paket (Tracking History)</div>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($trackingInfo['history']) && is_array($trackingInfo['history'])) : ?>
                                        <div class="timeline-container pl-2">
                                            <ul class="timeline">
                                                <?php foreach ($trackingInfo['history'] as $index => $historyItem) : ?>
                                                    <?php 
                                                        $isFirst = ($index === 0);
                                                        $itemNote = $historyItem['note'] ?? $historyItem['status'] ?? 'Update Status';
                                                        $itemDate = isset($historyItem['updated_at']) ? date('d M Y, H:i', strtotime($historyItem['updated_at'])) : (isset($historyItem['date']) ? $historyItem['date'] : '-');
                                                        $itemLocation = $historyItem['location'] ?? '';
                                                    ?>
                                                    <li class="timeline-item <?php echo $isFirst ? 'active' : ''; ?> mb-4">
                                                        <div class="d-flex align-items-top">
                                                            <div class="timeline-badge <?php echo $isFirst ? 'bg-success text-white' : 'bg-light text-muted'; ?> rounded-circle p-2 mr-3" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas <?php echo $isFirst ? 'fa-map-marker-alt' : 'fa-circle-notch'; ?>"></i>
                                                            </div>
                                                            <div class="timeline-content">
                                                                <h6 class="mb-1 font-weight-bold <?php echo $isFirst ? 'text-success' : 'text-dark'; ?>">
                                                                    <?php echo htmlspecialchars($itemNote); ?>
                                                                </h6>
                                                                <?php if (!empty($itemLocation)) : ?>
                                                                    <p class="mb-1 text-muted small"><i class="fas fa-building mr-1"></i> <?php echo htmlspecialchars($itemLocation); ?></p>
                                                                <?php endif; ?>
                                                                <small class="text-secondary"><i class="far fa-clock mr-1"></i> <?php echo htmlspecialchars($itemDate); ?></small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else : ?>
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-box-open fa-2x mb-2"></i>
                                            <p>Belum ada rincian riwayat perjalanan untuk paket ini.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>