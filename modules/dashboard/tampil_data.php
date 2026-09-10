<?php
$id_cabang = (int)$_SESSION['id_cabang'];

// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
} else {
  // Pesan selamat datang SweetAlert2 Toast
  if (isset($_GET['pesan']) && $_GET['pesan'] == 1) {
    $nama_user = htmlspecialchars($_SESSION['nama_user']);
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('info', 'Hi! $nama_user', 'Selamat Datang di Aplikasi INSTOCK.'); });</script>";
  }

  $hak_akses = $_SESSION['hak_akses'];

  // Ambil nama cabang user jika bukan Super Admin / Administrator
  $nama_cabang_user = "";
  if ($hak_akses != 'Super Admin' && $hak_akses != 'Administrator') {
    $q_cb = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang = $id_cabang");
    $r_cb = mysqli_fetch_assoc($q_cb);
    $nama_cabang_user = $r_cb['nama_cabang'] ?? 'Cabang Saya';
  }

  $active_cabang_id = getActiveCabangId();
  $active_cabang_name = getActiveCabangName($mysqli);

  // 1. Ambil indikator ringkasan utama (KPI) & Data Grafik berdasarkan Cabang Aktif
  if ($active_cabang_id === 0) {
    // Data Akses Global (Seluruh Cabang)
    $res_barang = mysqli_query($mysqli, "SELECT COUNT(*) as total_item, SUM(stok) as total_unit FROM tbl_barang");
    $res_masuk = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_masuk");
    $res_keluar = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_keluar");
    $query_min = "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, b.nama_jenis, c.nama_satuan, d.nama_cabang
                  FROM tbl_barang as a 
                  INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                  INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan
                  INNER JOIN tbl_cabang as d ON a.id_cabang=d.id_cabang
                  WHERE a.stok<=a.stok_minimum 
                  ORDER BY a.stok ASC";

    // Donut Chart: Proporsi Stok per Jenis (Global)
    $q_jenis_chart = mysqli_query($mysqli, "SELECT j.nama_jenis, SUM(b.stok) as total FROM tbl_barang b JOIN tbl_jenis j ON b.jenis = j.id_jenis GROUP BY b.jenis ORDER BY total DESC");

    // Bar Chart: Total Stok per Area Cabang
    $q_bar_chart = mysqli_query($mysqli, "SELECT c.nama_cabang as label_name, SUM(b.stok) as total FROM tbl_barang b JOIN tbl_cabang c ON b.id_cabang = c.id_cabang GROUP BY b.id_cabang ORDER BY total DESC");
    $bar_chart_title = "Distribusi Stok per Area Cabang";
    $bar_chart_badge = "Total Unit per Cabang";
    $donut_chart_title = "Proporsi Stok per Kategori (Global)";
  } else {
    // Khusus Cabang Aktif
    $res_barang = mysqli_query($mysqli, "SELECT COUNT(*) as total_item, SUM(stok) as total_unit FROM tbl_barang WHERE id_cabang = $active_cabang_id");
    $res_masuk = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_masuk WHERE id_cabang = $active_cabang_id");
    $res_keluar = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_keluar WHERE id_cabang = $active_cabang_id");
    $query_min = "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, b.nama_jenis, c.nama_satuan, d.nama_cabang
                  FROM tbl_barang as a 
                  INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                  INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan
                  INNER JOIN tbl_cabang as d ON a.id_cabang=d.id_cabang
                  WHERE a.stok<=a.stok_minimum AND a.id_cabang=$active_cabang_id
                  ORDER BY a.stok ASC";

    // Donut Chart: Proporsi Stok per Jenis (Cabang Aktif)
    $q_jenis_chart = mysqli_query($mysqli, "SELECT j.nama_jenis, SUM(b.stok) as total FROM tbl_barang b JOIN tbl_jenis j ON b.jenis = j.id_jenis WHERE b.id_cabang = $active_cabang_id GROUP BY b.jenis ORDER BY total DESC");

    // Bar Chart: Top 6 Barang Stok Terbanyak di Cabang Ini
    $q_bar_chart = mysqli_query($mysqli, "SELECT b.nama_barang as label_name, b.stok as total FROM tbl_barang b WHERE b.id_cabang = $active_cabang_id ORDER BY b.stok DESC LIMIT 6");
    $bar_chart_title = "Stok Barang Terbanyak di " . htmlspecialchars($active_cabang_name);
    $bar_chart_badge = "Top Item Cabang";
    $donut_chart_title = "Proporsi Stok per Kategori (" . htmlspecialchars($active_cabang_name) . ")";
  }

  $row_barang = mysqli_fetch_assoc($res_barang);
  $jumlah_barang = (int)($row_barang['total_item'] ?? 0);
  $total_unit = (int)($row_barang['total_unit'] ?? 0);

  $row_masuk = mysqli_fetch_assoc($res_masuk);
  $jumlah_barang_masuk = (int)($row_masuk['total'] ?? 0);

  $row_keluar = mysqli_fetch_assoc($res_keluar);
  $jumlah_barang_keluar = (int)($row_keluar['total'] ?? 0);

  $result_min = mysqli_query($mysqli, $query_min);
  $jumlah_min = mysqli_num_rows($result_min);

  // Ambil Data Donut Chart
  $labels_jenis = [];
  $data_jenis = [];
  while ($r = mysqli_fetch_assoc($q_jenis_chart)) {
    $labels_jenis[] = $r['nama_jenis'];
    $data_jenis[] = (int)$r['total'];
  }

  // Ambil Data Bar Chart
  $labels_bar = [];
  $data_bar = [];
  while ($r = mysqli_fetch_assoc($q_bar_chart)) {
    $labels_bar[] = $r['label_name'];
    $data_bar[] = (int)$r['total'];
  }
  ?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Header Banner Original Bawaan Aplikasi -->
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row justify-content-between">
        <div class="page-header text-white">
          <!-- judul halaman -->
          <h4 class="page-title text-white"><i class="fas fa-home mr-2"></i> Dashboard</h4>
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item active"><span>Dashboard</span></li>
          </ul>
        </div>
        <?php if ($hak_akses != 'Super Admin' && $hak_akses != 'Administrator' && !empty($nama_cabang_user)): ?>
          <div>
            <span class="badge badge-light text-secondary font-weight-bold px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
              <i class="fas fa-map-marker-alt text-primary mr-1"></i> Area: <?= htmlspecialchars($nama_cabang_user) ?>
            </span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">

    <!-- Section 1: Ringkasan Indikator Utama (KPI Cards Sesuai Hak Akses) -->
    <div class="row">
      <!-- Card 1: Total Inventory -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round border-0 shadow-sm mb-3">
          <div class="card-body p-3">
            <div class="row align-items-center">
              <div class="col-4 text-center">
                <div class="icon-big text-center icon-primary bubble-shadow-small rounded-circle p-2">
                  <i class="flaticon-box-2 text-primary" style="font-size: 1.8rem;"></i>
                </div>
              </div>
              <div class="col-8 col-stats pl-0">
                <div class="numbers">
                  <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">
                    <?= ($hak_akses == 'Super Admin' || $hak_akses == 'Administrator') ? 'TOTAL INVENTORY' : 'STOK CABANG' ?>
                  </p>
                  <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($jumlah_barang, 0, '', '.') ?> <small class="text-muted" style="font-size: 0.75rem;">Item</small></h3>
                  <span class="text-primary font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-boxes mr-1"></i><?= number_format($total_unit, 0, '', '.') ?> Unit Total</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: Barang Masuk -->
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
                  <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">BARANG MASUK</p>
                  <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($jumlah_barang_masuk, 0, '', '.') ?> <small class="text-muted" style="font-size: 0.75rem;">Transaksi</small></h3>
                  <a href="main.php?module=barang_masuk" class="text-success font-weight-bold" style="font-size: 0.72rem;">Lihat Riwayat <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 3: Barang Keluar -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round border-0 shadow-sm mb-3">
          <div class="card-body p-3">
            <div class="row align-items-center">
              <div class="col-4 text-center">
                <div class="icon-big text-center icon-warning bubble-shadow-small rounded-circle p-2">
                  <i class="flaticon-archive text-warning" style="font-size: 1.8rem;"></i>
                </div>
              </div>
              <div class="col-8 col-stats pl-0">
                <div class="numbers">
                  <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">BARANG KELUAR</p>
                  <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($jumlah_barang_keluar, 0, '', '.') ?> <small class="text-muted" style="font-size: 0.75rem;">Transaksi</small></h3>
                  <a href="main.php?module=barang_keluar" class="text-warning font-weight-bold" style="font-size: 0.72rem;">Lihat Riwayat <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 4: Stok Kritis / Batas Minimum -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round border-0 shadow-sm mb-3 <?= $jumlah_min > 0 ? 'bg-danger-gradient text-white' : '' ?>">
          <div class="card-body p-3">
            <div class="row align-items-center">
              <div class="col-4 text-center">
                <div class="icon-big text-center <?= $jumlah_min > 0 ? 'icon-light text-white' : 'icon-danger bubble-shadow-small' ?> rounded-circle p-2">
                  <i class="fas fa-exclamation-triangle <?= $jumlah_min > 0 ? 'text-white' : 'text-danger' ?>" style="font-size: 1.8rem;"></i>
                </div>
              </div>
              <div class="col-8 col-stats pl-0">
                <div class="numbers">
                  <p class="card-category mb-0 font-weight-bold <?= $jumlah_min > 0 ? 'text-white-50' : 'text-muted' ?>" style="font-size: 0.75rem;">STOK KRITIS</p>
                  <h3 class="card-title font-weight-bold mb-0 <?= $jumlah_min > 0 ? 'text-white' : 'text-dark' ?>" style="font-size: 1.3rem;"><?= number_format($jumlah_min, 0, '', '.') ?> <small class="<?= $jumlah_min > 0 ? 'text-white-50' : 'text-muted' ?>" style="font-size: 0.75rem;">Item</small></h3>
                  <?php if ($jumlah_min > 0): ?>
                    <a href="#tabel-stok-minimum" class="text-white font-weight-bold" style="font-size: 0.72rem;">Butuh Restok <i class="fas fa-arrow-down ml-1"></i></a>
                  <?php else: ?>
                    <span class="text-success font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-check-circle mr-1"></i>Stok Aman</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 2: Visualisasi Ringkas Sesuai Peran -->
    <div class="row mt-2">
      <!-- Chart 1: Donut Chart Proporsi Stok per Kategori -->
      <div class="col-md-6 mb-4">
        <div class="card card-round border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 pt-3 pb-0">
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="card-title font-weight-bold text-dark" style="font-size: 1.02rem;">
                <i class="fas fa-chart-pie text-primary mr-2"></i><?= $donut_chart_title ?>
              </h4>
              <span class="badge badge-primary px-2 py-1" style="font-size: 0.75rem;">Proporsi</span>
            </div>
          </div>
          <div class="card-body p-3">
            <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
              <canvas id="jenisDonutChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Chart 2: Bar Chart Distribusi/Item Stok -->
      <div class="col-md-6 mb-4">
        <div class="card card-round border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 pt-3 pb-0">
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="card-title font-weight-bold text-dark" style="font-size: 1.02rem;">
                <i class="fas fa-chart-bar text-success mr-2"></i><?= $bar_chart_title ?>
              </h4>
              <span class="badge badge-success px-2 py-1" style="font-size: 0.75rem;"><?= $bar_chart_badge ?></span>
            </div>
          </div>
          <div class="card-body p-3">
            <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
              <canvas id="cabangBarChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      // 1. Donut Chart Kategori Barang
      var ctxDonut = document.getElementById('jenisDonutChart').getContext('2d');
      var jenisDonutChart = new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
          labels: <?= json_encode($labels_jenis); ?>,
          datasets: [{
            data: <?= json_encode($data_jenis); ?>,
            backgroundColor: [
              '#4f46e5', '#10b981', '#f59e0b', '#ec4899', '#06b6d4', '#8b5cf6', '#3b82f6'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            position: 'right',
            labels: {
              usePointStyle: true,
              boxWidth: 10,
              padding: 15,
              fontSize: 11,
              fontFamily: "'Public Sans', sans-serif"
            }
          },
          tooltips: {
            callbacks: {
              label: function(tooltipItem, data) {
                var dataset = data.datasets[tooltipItem.datasetIndex];
                var total = dataset.data.reduce(function(previousValue, currentValue) {
                  return previousValue + currentValue;
                }, 0);
                var currentValue = dataset.data[tooltipItem.index];
                var percentage = total > 0 ? Math.round((currentValue / total) * 100) : 0;
                return data.labels[tooltipItem.index] + ': ' + currentValue.toLocaleString() + ' Unit (' + percentage + '%)';
              }
            }
          },
          cutoutPercentage: 65
        }
      });

      // 2. Bar Chart Distribusi/Item Stok
      var ctxBar = document.getElementById('cabangBarChart').getContext('2d');
      var cabangBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: <?= json_encode($labels_bar); ?>,
          datasets: [{
            label: 'Total Unit Stok',
            data: <?= json_encode($data_bar); ?>,
            backgroundColor: [
              '#4f46e5', '#10b981', '#f59e0b', '#06b6d4', '#8b5cf6', '#ec4899', '#3b82f6'
            ],
            borderWidth: 0,
            borderRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false
          },
          tooltips: {
            backgroundColor: 'rgba(30, 41, 59, 0.9)',
            cornerRadius: 6,
            padding: 10,
            callbacks: {
              label: function(tooltipItem) {
                return ' Total Stok: ' + Number(tooltipItem.yLabel).toLocaleString() + ' Unit';
              }
            }
          },
          scales: {
            xAxes: [{
              gridLines: {
                display: false
              },
              ticks: {
                fontSize: 10,
                maxRotation: 25,
                minRotation: 0,
                autoSkip: false
              }
            }],
            yAxes: [{
              gridLines: {
                color: 'rgba(0, 0, 0, 0.05)',
                zeroLineColor: 'rgba(0, 0, 0, 0.1)'
              },
              ticks: {
                beginAtZero: true,
                fontSize: 11,
                precision: 0
              }
            }]
          }
        }
      });
    </script>

    <!-- Section 3: Tabel Peringatan Stok Minimum Sesuai Hak Akses -->
    <div id="tabel-stok-minimum" class="card card-round shadow-sm mt-2 border-0">
      <div class="card-header bg-white border-0 py-3">
        <div class="d-flex align-items-center justify-content-between">
          <div class="card-title text-dark font-weight-bold" style="font-size: 1.05rem;">
            <i class="fas fa-exclamation-triangle text-danger mr-2"></i> Peringatan Stok Mencapai Batas Minimum <?= ($hak_akses != 'Super Admin' && $hak_akses != 'Administrator') ? '(' . htmlspecialchars($nama_cabang_user) . ')' : '' ?>
          </div>
          <div>
            <span class="badge <?= $jumlah_min > 0 ? 'badge-danger' : 'badge-success' ?> px-3 py-2 font-weight-bold">
              <?= $jumlah_min ?> Item Kritis
            </span>
          </div>
        </div>
      </div>
      <div class="card-body">
        <?php if ($jumlah_min == 0): ?>
          <div class="text-center py-4">
            <div class="mb-3">
              <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
            </div>
            <h5 class="font-weight-bold text-dark">Seluruh Stok Barang Dalam Kondisi Aman</h5>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tidak ada persediaan barang yang berada di bawah atau sama dengan batas minimum saat ini.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-center" width="50">No.</th>
                  <th class="text-center" width="100">ID Barang</th>
                  <th class="text-center">Nama Barang</th>
                  <th class="text-center">Jenis Barang</th>
                  <?php if ($hak_akses == 'Super Admin' || $hak_akses == 'Administrator'): ?>
                    <th class="text-center">Area / Cabang</th>
                  <?php endif; ?>
                  <th class="text-center" width="90">Stok Saat Ini</th>
                  <th class="text-center" width="90">Stok Min.</th>
                  <th class="text-center" width="90">Satuan</th>
                  <th class="text-center" width="110">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                mysqli_data_seek($result_min, 0);
                while ($data = mysqli_fetch_assoc($result_min)):
                  ?>
                  <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center font-weight-bold"><?= htmlspecialchars($data['id_barang']); ?></td>
                    <td class="font-weight-bold text-dark"><?= htmlspecialchars($data['nama_barang']); ?></td>
                    <td><?= htmlspecialchars($data['nama_jenis']); ?></td>
                    <?php if ($hak_akses == 'Super Admin' || $hak_akses == 'Administrator'): ?>
                      <td class="text-center"><span class="badge badge-info px-2 py-1"><?= htmlspecialchars($data['nama_cabang']); ?></span></td>
                    <?php endif; ?>
                    <td class="text-right font-weight-bold text-danger"><?= number_format($data['stok'], 0, '', '.'); ?></td>
                    <td class="text-right text-muted"><?= number_format($data['stok_minimum'], 0, '', '.'); ?></td>
                    <td class="text-center"><?= htmlspecialchars($data['nama_satuan']); ?></td>
                    <td class="text-center">
                      <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-circle mr-1"></i> Perlu Restok</span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
<?php } ?>