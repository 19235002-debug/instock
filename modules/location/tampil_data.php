<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
  require_once __DIR__ . '/../../vendor/autoload.php';
}

$active_cabang_id = getActiveCabangId();

// Ambil data penggunaan rak per cabang dari tbl_barang
$rak_usage = [];
$q_usage_sql = "SELECT b.rak_id, b.nama_barang, b.id_cabang, c.nama_cabang 
                FROM tbl_barang b 
                LEFT JOIN tbl_cabang c ON b.id_cabang = c.id_cabang 
                WHERE b.rak_id IS NOT NULL AND b.rak_id != ''";
if ($active_cabang_id !== 0) {
  $q_usage_sql .= " AND b.id_cabang = '$active_cabang_id'";
}
$q_usage = mysqli_query($mysqli, $q_usage_sql);
if ($q_usage) {
  while ($r_u = mysqli_fetch_assoc($q_usage)) {
    $rak_usage[$r_u['rak_id']][] = $r_u;
  }
}

// Mengambil Semua Status yang Unik dari Tabel location
$statusQuery = mysqli_query($mysqli, "SELECT DISTINCT status FROM location");
$statuses = [];
if ($statusQuery) {
  while ($statusRow = mysqli_fetch_assoc($statusQuery)) {
    $statuses[] = $statusRow['status'];
  }
}

// Toast Notification
if (isset($_GET['pesan'])) {
  if ($_GET['pesan'] == 1) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data lokasi berhasil disimpan.'); });</script>";
  }
}

// Statistik Rak per Cabang Aktif
$cnt_total_rak = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) as total FROM location"))['total'] ?? 0;
$cnt_active_rak = count($rak_usage);
$cnt_inactive_rak = max(0, $cnt_total_rak - $cnt_active_rak);
?>

<script>
  $(document).ready(function() {
    $('#checkAll').on('change', function() {
      $('.itemCheckbox').prop('checked', this.checked);
    });
    $('.itemCheckbox').on('change', function() {
      if ($('.itemCheckbox:checked').length == $('.itemCheckbox').length) {
        $('#checkAll').prop('checked', true);
      } else {
        $('#checkAll').prop('checked', false);
      }
    });

    $('#printSelected, #printSelectedCard').on('click', function() {
      var selectedItems = [];
      $('.itemCheckbox:checked').each(function() {
        selectedItems.push($(this).val());
      });

      if (selectedItems.length > 0) {
        var form = $('<form>', {
          'action': 'modules/location/print_label.php',
          'method': 'POST',
          'target': '_blank'
        });

        $.each(selectedItems, function(index, value) {
          form.append($('<input>', {
            'type': 'hidden',
            'name': 'selected_items[]',
            'value': value
          }));
        });

        $('body').append(form);
        form.submit();
      } else {
        showToast('warning', 'Peringatan!', 'Pilih minimal satu lokasi rak untuk dicetak.');
      }
    });
  });
</script>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row justify-content-between">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-tasks mr-2"></i> Lokasi Barang (Rak)</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=location" class="text-white">Master Data</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item active"><span>Lokasi Barang (Rak)</span></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <!-- Section KPI Stat Cards Sesuai Dashboard -->
  <div class="row">
    <!-- Card 1: Total Lokasi Rak -->
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round border-0 shadow-sm mb-3">
        <div class="card-body p-3">
          <div class="row align-items-center">
            <div class="col-4 text-center">
              <div class="icon-big text-center icon-primary bubble-shadow-small rounded-circle p-2">
                <i class="flaticon-box-1 text-primary" style="font-size: 1.8rem;"></i>
              </div>
            </div>
            <div class="col-8 col-stats pl-0">
              <div class="numbers">
                <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">TOTAL MASTER RAK</p>
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($cnt_total_rak) ?> <small class="text-muted" style="font-size: 0.75rem;">Rak</small></h3>
                <span class="text-primary font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-cubes mr-1"></i>Sistem Gudang</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 2: Rak Active (Terpakai) -->
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round border-0 shadow-sm mb-3">
        <div class="card-body p-3">
          <div class="row align-items-center">
            <div class="col-4 text-center">
              <div class="icon-big text-center icon-success bubble-shadow-small rounded-circle p-2">
                <i class="flaticon-inbox text-success" style="font-size: 1.8rem;"></i>
              </div>
            </div>
            <div class="col-8 col-stats pl-0">
              <div class="numbers">
                <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">RAK TERPAKAI</p>
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($cnt_active_rak) ?> <small class="text-muted" style="font-size: 0.75rem;">Rak</small></h3>
                <span class="text-success font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-box mr-1"></i>Diisi Barang</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 3: Rak Tersedia -->
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round border-0 shadow-sm mb-3">
        <div class="card-body p-3">
          <div class="row align-items-center">
            <div class="col-4 text-center">
              <div class="icon-big text-center icon-info bubble-shadow-small rounded-circle p-2">
                <i class="flaticon-check text-info" style="font-size: 1.8rem;"></i>
              </div>
            </div>
            <div class="col-8 col-stats pl-0">
              <div class="numbers">
                <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">RAK TERSEDIA</p>
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($cnt_inactive_rak) ?> <small class="text-muted" style="font-size: 0.75rem;">Rak</small></h3>
                <span class="text-info font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-check-circle mr-1"></i>Siap Digunakan</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 4: Action Print Label -->
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round border-0 shadow-sm mb-3">
        <div class="card-body p-3">
          <div class="row align-items-center">
            <div class="col-4 text-center">
              <div class="icon-big text-center icon-secondary bubble-shadow-small rounded-circle p-2">
                <i class="flaticon-technology text-secondary" style="font-size: 1.8rem;"></i>
              </div>
            </div>
            <div class="col-8 col-stats pl-0">
              <div class="numbers">
                <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">CETAK LABEL</p>
                <button type="button" id="printSelectedCard" class="btn btn-primary btn-sm btn-round mt-1 font-weight-bold">
                  <i class="fa fa-print mr-1"></i> Print Label
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Data Table Card -->
  <div class="card border-0 shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between bg-white py-3">
      <div class="card-title font-weight-bold text-dark mb-0" style="font-size: 1rem;">
        <i class="fas fa-list mr-2 text-primary"></i> Data Lokasi Rak
      </div>
      <div>
        <form method="GET" action="" class="form-inline">
          <input type="hidden" name="module" value="location">
          <label class="mr-2 font-weight-bold text-muted" style="font-size: 12.5px;">Filter Status:</label>
          <select name="filter_status" class="form-control form-control-sm border" onchange="this.form.submit()" style="min-width: 140px; border-radius: 6px;">
            <option value="all">-- Semua Status --</option>
            <option value="terpakai" <?= (isset($_GET['filter_status']) && $_GET['filter_status'] == 'terpakai') ? 'selected' : '' ?>>Terpakai</option>
            <option value="tersedia" <?= (isset($_GET['filter_status']) && $_GET['filter_status'] == 'tersedia') ? 'selected' : '' ?>>Tersedia (Belum Digunakan)</option>
          </select>
        </form>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" width="40"><input type="checkbox" id="checkAll"></th>
              <th class="text-center">Nomor Rak</th>
              <th class="text-center">Barcode Label</th>
              <th class="text-center">Status Penggunaan per Cabang</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'all';
            $sql_query = "SELECT id, no_rak, status FROM location ORDER BY no_rak ASC";
            $query = mysqli_query($mysqli, $sql_query) or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

            $generator = null;
            if (class_exists('Picqer\Barcode\BarcodeGeneratorPNG')) {
              $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            }

            while ($data = mysqli_fetch_assoc($query)) {
              $noRak = htmlspecialchars($data['no_rak']);
              $is_used_here = !empty($rak_usage[$data['id']]);

              if ($filter_status == 'terpakai' && !$is_used_here) continue;
              if ($filter_status == 'tersedia' && $is_used_here) continue;

              $barcodeImg = '-';
              if ($generator) {
                try {
                  $barcodeData = base64_encode($generator->getBarcode($noRak, $generator::TYPE_CODE_128));
                  $barcodeImg = '<img src="data:image/png;base64,' . $barcodeData . '" alt="Barcode ' . $noRak . '" style="max-height: 40px;">';
                } catch (Exception $e) {
                  $barcodeImg = '<span class="text-muted small">' . $noRak . '</span>';
                }
              } else {
                $barcodeImg = '<span class="text-muted small">' . $noRak . '</span>';
              }
            ?>
              <tr>
                <td class="text-center"><input type="checkbox" class="itemCheckbox" value="<?php echo $noRak; ?>"></td>
                <td class="text-center font-weight-bold"><?php echo $noRak; ?></td>
                <td class="text-center"><?php echo $barcodeImg; ?></td>
                <td class="text-center">
                  <?php if ($is_used_here): ?>
                    <?php 
                    $u_info = $rak_usage[$data['id']][0];
                    $b_name = htmlspecialchars($u_info['nama_barang']);
                    $c_name = htmlspecialchars($u_info['nama_cabang'] ?? '');
                    ?>
                    <span class="badge badge-success font-weight-bold" data-toggle="tooltip" title="<?= $b_name ?> (<?= $c_name ?>)">
                      <i class="fa fa-box mr-1"></i> Terpakai <?= $active_cabang_id === 0 ? "($c_name)" : "" ?>
                    </span>
                    <small class="d-block text-dark font-weight-bold mt-1" style="font-size: 11.5px;"><i class="fas fa-tag text-primary mr-1"></i> <?= $b_name ?></small>
                  <?php else: ?>
                    <span class="badge badge-light border text-muted">
                      <i class="fa fa-check-circle mr-1 text-success"></i> Belum Digunakan (Tersedia)
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>