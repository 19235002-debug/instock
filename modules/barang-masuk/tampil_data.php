<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
} else {
  if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 1) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data pengiriman barang masuk berhasil disimpan.'); });</script>";
    } elseif ($_GET['pesan'] == 2) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data pengiriman barang masuk berhasil dihapus.'); });</script>";
    } elseif ($_GET['pesan'] == 3) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Anda tidak memiliki hak akses menghapus data ini.'); });</script>";
    }
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <h4 class="page-title text-white"><i class="fas fa-sign-in-alt mr-2"></i> Barang Masuk</h4>
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=barang_masuk" class="text-white">Barang Masuk</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>       
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <a href="?module=form_entri_barang_masuk" class="btn btn-secondary btn-round font-weight-bold">
            <span class="btn-label"><i class="fa fa-plus mr-1"></i></span> Entri Data
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <!-- Quick Stats Counter Barang Masuk -->
    <?php
    $active_cabang_id = getActiveCabangId();
    $w_c = ($active_cabang_id === 0) ? "" : "WHERE id_cabang = '$active_cabang_id'";
    $q_st1 = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_masuk $w_c");
    $r_st1 = mysqli_fetch_assoc($q_st1);
    
    $w_p = ($active_cabang_id === 0) ? "WHERE status = 'Pending'" : "WHERE id_cabang = '$active_cabang_id' AND status = 'Pending'";
    $q_st2 = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_masuk $w_p");
    $r_st2 = mysqli_fetch_assoc($q_st2);
    
    $w_q = ($active_cabang_id === 0) ? "WHERE status = 'Approved'" : "WHERE id_cabang = '$active_cabang_id' AND status = 'Approved'";
    $q_st3 = mysqli_query($mysqli, "SELECT SUM(jumlah) as total FROM tbl_barang_masuk $w_q");
    $r_st3 = mysqli_fetch_assoc($q_st3);
    ?>
    <div class="row mb-3">
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round border-0 shadow-sm mb-2 mb-md-0">
          <div class="card-body py-3">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-primary bubble-shadow-small">
                  <i class="fas fa-file-invoice"></i>
                </div>
              </div>
              <div class="col col-stats ml-3 ml-sm-0">
                <div class="numbers">
                  <p class="card-category text-muted mb-0 font-weight-bold">Total Transaksi Masuk</p>
                  <h4 class="card-title font-weight-bold text-dark mb-0"><?= number_format($r_st1['total'] ?? 0, 0, '', '.') ?> Dokumen</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round border-0 shadow-sm mb-2 mb-md-0">
          <div class="card-body py-3">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-warning bubble-shadow-small">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
              <div class="col col-stats ml-3 ml-sm-0">
                <div class="numbers">
                  <p class="card-category text-muted mb-0 font-weight-bold">Menunggu Persetujuan</p>
                  <h4 class="card-title font-weight-bold text-warning mb-0"><?= number_format($r_st2['total'] ?? 0, 0, '', '.') ?> Transaksi</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round border-0 shadow-sm mb-2 mb-md-0">
          <div class="card-body py-3">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-success bubble-shadow-small">
                  <i class="fas fa-boxes"></i>
                </div>
              </div>
              <div class="col col-stats ml-3 ml-sm-0">
                <div class="numbers">
                  <p class="card-category text-muted mb-0 font-weight-bold">Total Qty Approved</p>
                  <h4 class="card-title font-weight-bold text-success mb-0"><?= number_format($r_st3['total'] ?? 0, 0, '', '.') ?> Unit</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-white py-3">
        <div class="card-title font-weight-bold text-dark">
          Data Barang Masuk
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead class="bg-light">
              <tr class="text-center">
                <th style="width: 4%;">No.</th>
                <th style="width: 14%;">ID Transaksi / No. SJ</th>
                <th style="width: 11%;">Tanggal Penerimaan</th>
                <th style="width: 18%;">Supplier / Pemasok</th>
                <th style="width: 14%;">Cabang</th>
                <th style="width: 10%;">Total Item</th>
                <th style="width: 10%;">Total Qty</th>
                <th style="width: 9%;">Status</th>
                <th style="width: 10%;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $active_cabang_id = getActiveCabangId();
              $sqlWhere = ($active_cabang_id === 0) ? "" : "WHERE bm.id_cabang = '$active_cabang_id'";

              $query_sj = mysqli_query($mysqli, "
                SELECT 
                  bm.id_transaksi,
                  bm.no_surat_jalan,
                  bm.supplier,
                  bm.tanggal,
                  bm.id_cabang,
                  bm.status,
                  bm.created_by_role,
                  cb.nama_cabang,
                  COUNT(bmd.id_detail) AS total_item,
                  COALESCE(SUM(bmd.jumlah), bm.jumlah, 0) AS total_qty
                FROM tbl_barang_masuk bm
                LEFT JOIN tbl_cabang cb ON bm.id_cabang = cb.id_cabang
                LEFT JOIN tbl_barang_masuk_detail bmd ON bm.id_transaksi = bmd.id_transaksi
                $sqlWhere
                GROUP BY bm.id_transaksi, bm.no_surat_jalan, bm.supplier, bm.tanggal, bm.id_cabang, bm.status, bm.created_by_role, cb.nama_cabang
                ORDER BY bm.id_transaksi DESC
              ") or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

              $sj_list = [];
              $no = 1;

              while ($data = mysqli_fetch_assoc($query_sj)) {
                $sj_list[] = $data;

                $statusBadge = '<span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Pending</span>';
                if ($data['status'] == 'Approved') {
                  $statusBadge = '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Approved</span>';
                } elseif ($data['status'] == 'Rejected') {
                  $statusBadge = '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Rejected</span>';
                }
              ?>
                <tr>
                  <td class="text-center align-middle"><?= $no++ ?></td>
                  <td class="align-middle">
                    <strong class="text-primary"><?= htmlspecialchars($data['id_transaksi']) ?></strong>
                    <?php if (!empty($data['no_surat_jalan'])): ?>
                      <br><small class="text-muted"><i class="fas fa-receipt mr-1"></i> <?= htmlspecialchars($data['no_surat_jalan']) ?></small>
                    <?php endif; ?>
                  </td>
                  <td class="text-center align-middle"><?= date('d-m-Y', strtotime($data['tanggal'])) ?></td>
                  <td class="align-middle">
                    <i class="fas fa-building text-secondary mr-1"></i> <?= htmlspecialchars($data['supplier'] ?: '-') ?>
                  </td>
                  <td class="align-middle">
                    <?= htmlspecialchars($data['nama_cabang'] ?: 'Utama') ?>
                  </td>
                  <td class="text-center align-middle font-weight-bold">
                    <span class="badge badge-light border px-2 py-1"><?= number_format($data['total_item'], 0, '', '.') ?> Item</span>
                  </td>
                  <td class="text-center align-middle font-weight-bold">
                    <span class="badge badge-info px-2 py-1"><?= number_format($data['total_qty'], 0, '', '.') ?> Pcs</span>
                  </td>
                  <td class="text-center align-middle">
                    <?= $statusBadge ?>
                  </td>
                  <td class="text-center align-middle">
                    <div class="btn-group" role="group">
                      <!-- Tombol Detail Modal -->
                      <button type="button" class="btn btn-icon btn-round btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalDetail_<?= str_replace('-', '_', $data['id_transaksi']) ?>" title="Lihat Rincian Item">
                        <i class="fas fa-eye"></i>
                      </button>

                      <!-- Tombol Cetak Surat Jalan -->
                      <a href="modules/barang-masuk/cetak_surat_jalan.php?id=<?= urlencode($data['id_transaksi']) ?>" target="_blank" class="btn btn-icon btn-round btn-primary btn-sm mr-1" title="Cetak Surat Jalan / GRN">
                        <i class="fas fa-print"></i>
                      </a>

                      <!-- Tombol Hapus (jika berhak) -->
                      <?php if ($hak_akses == 'Super Admin' || $hak_akses == 'Administrator' || $hak_akses == 'Kepala Gudang'): ?>
                        <a href="javascript:void(0)" onclick="return confirmDelete('modules/barang-masuk/proses_hapus.php?id=<?= $data['id_transaksi']; ?>', 'Anda yakin ingin menghapus data pengiriman ini? Stok barang akan disesuaikan kembali.')" class="btn btn-icon btn-round btn-danger btn-sm" title="Hapus">
                          <i class="fas fa-trash"></i>
                        </a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modals Rendered OUTSIDE Table -->
  <?php foreach ($sj_list as $data): ?>
    <div class="modal fade" id="modalDetail_<?= str_replace('-', '_', $data['id_transaksi']) ?>" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title font-weight-bold text-white">
              <i class="fas fa-boxes mr-2"></i> Rincian Barang Masuk: <?= htmlspecialchars($data['id_transaksi']) ?>
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            <div class="row mb-3 bg-light p-3 rounded mx-0 border">
              <div class="col-md-4">
                <small class="text-muted d-block">No. Surat Jalan</small>
                <span class="font-weight-bold"><?= htmlspecialchars($data['no_surat_jalan'] ?: '-') ?></span>
              </div>
              <div class="col-md-4">
                <small class="text-muted d-block">Supplier / Vendor</small>
                <span class="font-weight-bold"><?= htmlspecialchars($data['supplier'] ?: '-') ?></span>
              </div>
              <div class="col-md-4">
                <small class="text-muted d-block">Tanggal Penerimaan</small>
                <span class="font-weight-bold"><?= date('d F Y', strtotime($data['tanggal'])) ?></span>
              </div>
            </div>

            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-list-alt mr-1"></i> Daftar Barang Dalam Pengiriman:</h6>
            <div class="table-responsive">
              <table class="table table-bordered table-sm table-striped mb-0">
                <thead class="bg-secondary text-white text-center">
                  <tr>
                    <th>#</th>
                    <th>Kode & Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>No. Batch / Lot</th>
                    <th>Expired Date</th>
                    <th>Lokasi Rak</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $id_trans_curr = mysqli_real_escape_string($mysqli, $data['id_transaksi']);
                  $q_item = mysqli_query($mysqli, "
                    SELECT 
                      bmd.*,
                      b.nama_barang,
                      s.nama_satuan,
                      r.no_rak
                    FROM tbl_barang_masuk_detail bmd
                    LEFT JOIN tbl_barang b ON bmd.id_barang = b.id_barang
                    LEFT JOIN tbl_satuan s ON bmd.id_satuan = s.id_satuan
                    LEFT JOIN location r ON bmd.rak_id = r.id
                    WHERE bmd.id_transaksi = '$id_trans_curr'
                    ORDER BY bmd.id_detail ASC
                  ");
                  $idx_det = 1;
                  if (mysqli_num_rows($q_item) > 0) {
                    while ($r_det = mysqli_fetch_assoc($q_item)) {
                  ?>
                      <tr>
                        <td class="text-center align-middle"><?= $idx_det++ ?></td>
                        <td class="align-middle font-weight-bold">
                          <?= htmlspecialchars($r_det['id_barang']) ?> - <?= htmlspecialchars($r_det['nama_barang']) ?>
                        </td>
                        <td class="text-center align-middle font-weight-bold text-primary">
                          <?= number_format($r_det['jumlah'], 0, '', '.') ?>
                        </td>
                        <td class="text-center align-middle"><?= htmlspecialchars($r_det['nama_satuan'] ?: '-') ?></td>
                        <td class="text-center align-middle font-weight-bold">
                          <span class="badge badge-light border text-dark"><?= htmlspecialchars($r_det['no_batch'] ?: '-') ?></span>
                        </td>
                        <td class="text-center align-middle">
                          <?php if (!empty($r_det['expired_date'])): ?>
                            <span class="badge badge-warning text-dark">
                              <i class="fas fa-calendar-times mr-1"></i> <?= date('d-m-Y', strtotime($r_det['expired_date'])) ?>
                            </span>
                          <?php else: ?>
                            <span class="text-muted">-</span>
                          <?php endif; ?>
                        </td>
                        <td class="text-center align-middle">
                          <span class="badge badge-secondary"><?= htmlspecialchars($r_det['no_rak'] ?: 'A-001') ?></span>
                        </td>
                      </tr>
                  <?php 
                    }
                  } else {
                    echo '<tr><td colspan="7" class="text-center text-muted py-3">Tidak ada rincian item barang.</td></tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <a href="modules/barang-masuk/cetak_surat_jalan.php?id=<?= urlencode($data['id_transaksi']) ?>" target="_blank" class="btn btn-primary font-weight-bold">
              <i class="fas fa-print mr-1"></i> Cetak Surat Jalan (GRN)
            </a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

<?php } ?>