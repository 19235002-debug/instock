<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

// Hanya Super Admin, Administrator & Kepala Gudang yang dapat melakukan approval
if ($_SESSION['hak_akses'] != 'Super Admin' && $_SESSION['hak_akses'] != 'Administrator' && $_SESSION['hak_akses'] != 'Kepala Gudang') {
  echo "<script>window.location.href='403.html';</script>";
  exit;
}

require_once "helper/approval_helper.php";
initApprovalColumns($mysqli);

// Proses Aksi Approve / Reject
if (isset($_POST['aksi_approval'])) {
  $type = mysqli_real_escape_string($mysqli, $_POST['type']);
  $id_val = mysqli_real_escape_string($mysqli, $_POST['id_val']);
  $action = $_POST['action_type']; // 'approve' atau 'reject'
  $catatan = mysqli_real_escape_string($mysqli, $_POST['catatan'] ?? '');
  $approved_by = $_SESSION['nama_user'] ?? $_SESSION['username'];

  if ($action === 'approve') {
    if (approveTransaction($mysqli, $type, $id_val, $approved_by, $catatan)) {
      echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('success', 'Berhasil Disetujui!', 'Transaksi $id_val telah disetujui dan stok barang telah diperbarui.');
            });
        </script>";
    } else {
      echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('error', 'Gagal!', 'Transaksi gagal disetujui atau sudah tidak berstatus Pending.');
            });
        </script>";
    }
  } elseif ($action === 'reject') {
    if (rejectTransaction($mysqli, $type, $id_val, $approved_by, $catatan)) {
      echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('info', 'Transaksi Ditolak!', 'Transaksi $id_val telah ditolak.');
            });
        </script>";
    } else {
      echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('error', 'Gagal!', 'Transaksi gagal ditolak.');
            });
        </script>";
    }
  }
}

// Hitung Statistik Pending berdasarkan Cabang Aktif
$active_cabang_id = getActiveCabangId();
if ($active_cabang_id !== 0) {
  $cnt_masuk = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_masuk WHERE status = 'Pending' AND id_cabang = '$active_cabang_id'"))['total'] ?? 0;
  $cnt_keluar = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_keluar tk LEFT JOIN tbl_barang b ON tk.barang = b.id_barang WHERE tk.status = 'Pending' AND (tk.id_cabang = '$active_cabang_id' OR b.id_cabang = '$active_cabang_id')"))['total'] ?? 0;
  $cnt_adj = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) as total FROM adjustment a JOIN tbl_barang b ON a.id_barang = b.id_barang WHERE a.status = 'Pending' AND b.id_cabang = '$active_cabang_id'"))['total'] ?? 0;
} else {
  $cnt_masuk = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_masuk WHERE status = 'Pending'"))['total'] ?? 0;
  $cnt_keluar = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang_keluar WHERE status = 'Pending'"))['total'] ?? 0;
  $cnt_adj = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) as total FROM adjustment WHERE status = 'Pending'"))['total'] ?? 0;
}
$total_pending = $cnt_masuk + $cnt_keluar + $cnt_adj;
?>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-check-double mr-2"></i> Persetujuan (Approval)</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=approval" class="text-white">Persetujuan</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <!-- Section KPI Stat Cards Sesuai Dashboard -->
  <div class="row">
    <!-- Card 1: Total Menunggu -->
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round border-0 shadow-sm mb-3">
        <div class="card-body p-3">
          <div class="row align-items-center">
            <div class="col-4 text-center">
              <div class="icon-big text-center icon-warning bubble-shadow-small rounded-circle p-2">
                <i class="fas fa-hourglass-half text-warning" style="font-size: 1.8rem;"></i>
              </div>
            </div>
            <div class="col-8 col-stats pl-0">
              <div class="numbers">
                <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">TOTAL MENUNGGU</p>
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($total_pending) ?> <small class="text-muted" style="font-size: 0.75rem;">Transaksi</small></h3>
                <span class="text-warning font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-clock mr-1"></i>Persetujuan</span>
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
                <i class="fas fa-arrow-alt-circle-down text-success" style="font-size: 1.8rem;"></i>
              </div>
            </div>
            <div class="col-8 col-stats pl-0">
              <div class="numbers">
                <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">BARANG MASUK</p>
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($cnt_masuk) ?> <small class="text-muted" style="font-size: 0.75rem;">Pending</small></h3>
                <span class="text-success font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-arrow-down mr-1"></i>Persetujuan</span>
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
              <div class="icon-big text-center icon-danger bubble-shadow-small rounded-circle p-2">
                <i class="fas fa-arrow-alt-circle-up text-danger" style="font-size: 1.8rem;"></i>
              </div>
            </div>
            <div class="col-8 col-stats pl-0">
              <div class="numbers">
                <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">BARANG KELUAR</p>
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($cnt_keluar) ?> <small class="text-muted" style="font-size: 0.75rem;">Pending</small></h3>
                <span class="text-danger font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-arrow-up mr-1"></i>Persetujuan</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 4: Adjustment -->
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round border-0 shadow-sm mb-3">
        <div class="card-body p-3">
          <div class="row align-items-center">
            <div class="col-4 text-center">
              <div class="icon-big text-center icon-info bubble-shadow-small rounded-circle p-2">
                <i class="fas fa-sliders-h text-info" style="font-size: 1.8rem;"></i>
              </div>
            </div>
            <div class="col-8 col-stats pl-0">
              <div class="numbers">
                <p class="card-category text-muted mb-0 font-weight-bold" style="font-size: 0.75rem;">ADJUSTMENT</p>
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 1.3rem;"><?= number_format($cnt_adj) ?> <small class="text-muted" style="font-size: 0.75rem;">Pending</small></h3>
                <span class="text-info font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-adjust mr-1"></i>Persetujuan</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-header">
          <ul class="nav nav-tabs nav-line nav-color-primary card-header-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="tab-masuk" data-toggle="tab" href="#masuk" role="tab"><i class="fas fa-sign-in-alt mr-2"></i> Barang Masuk (<?= $cnt_masuk ?>)</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="tab-keluar" data-toggle="tab" href="#keluar" role="tab"><i class="fas fa-sign-out-alt mr-2"></i> Barang Keluar (<?= $cnt_keluar ?>)</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="tab-adjustment" data-toggle="tab" href="#adj" role="tab"><i class="fas fa-balance-scale mr-2"></i> Penyesuaian Adjustment (<?= $cnt_adj ?>)</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <!-- TAB BARANG MASUK -->
            <div class="tab-pane fade show active" id="masuk" role="tabpanel">
              <div class="table-responsive">
                <table class="table table-hover table-striped w-100">
                  <thead class="bg-light">
                    <tr>
                      <th>ID Transaksi</th>
                      <th>Tanggal</th>
                      <th>Barang</th>
                      <th>Jumlah Masuk</th>
                      <th>Penginput</th>
                      <th>Aksi Persetujuan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $where_m = ($active_cabang_id === 0) ? "" : " AND tm.id_cabang = '$active_cabang_id'";
                    $q_masuk = mysqli_query($mysqli, "SELECT tm.*, b.nama_barang, b.stok as stok_sekarang 
                                                      FROM tbl_barang_masuk tm 
                                                      JOIN tbl_barang b ON tm.barang = b.id_barang 
                                                      WHERE tm.status = 'Pending' $where_m
                                                      ORDER BY tm.tanggal DESC");
                    if (mysqli_num_rows($q_masuk) == 0):
                    ?>
                      <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                          <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                          Tidak ada pengajuan Barang Masuk yang pending.
                        </td>
                      </tr>
                    <?php
                    else:
                      while ($row = mysqli_fetch_assoc($q_masuk)):
                    ?>
                        <tr>
                          <td><span class="badge badge-primary"><?= htmlspecialchars($row['id_transaksi']) ?></span></td>
                          <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                          <td>
                            <strong><?= htmlspecialchars($row['nama_barang']) ?></strong>
                            <small class="d-block text-muted">ID: <?= htmlspecialchars($row['barang']) ?> (Stok Saat Ini: <?= $row['stok_sekarang'] ?>)</small>
                          </td>
                          <td><span class="badge badge-success font-weight-bold">+<?= number_format($row['jumlah']) ?></span></td>
                          <td><span class="badge badge-info"><?= htmlspecialchars($row['created_by_role'] ?? 'Staff') ?></span></td>
                          <td>
                            <button class="btn btn-sm btn-success mr-1" onclick="openApprovalModal('masuk', '<?= $row['id_transaksi'] ?>', 'approve', 'Setujui Barang Masuk <?= $row['id_transaksi'] ?> (+<?= $row['jumlah'] ?> unit ke <?= addslashes($row['nama_barang']) ?>)')">
                              <i class="fas fa-check mr-1"></i> Setujui
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="openApprovalModal('masuk', '<?= $row['id_transaksi'] ?>', 'reject', 'Tolak Barang Masuk <?= $row['id_transaksi'] ?>')">
                              <i class="fas fa-times mr-1"></i> Tolak
                            </button>
                          </td>
                        </tr>
                    <?php
                      endwhile;
                    endif;
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB BARANG KELUAR -->
            <div class="tab-pane fade" id="keluar" role="tabpanel">
              <div class="table-responsive">
                <table class="table table-hover table-striped w-100">
                  <thead class="bg-light">
                    <tr>
                      <th>ID Transaksi</th>
                      <th>Tanggal</th>
                      <th>Barang</th>
                      <th>Jumlah Keluar</th>
                      <th>Tujuan / Lokasi</th>
                      <th>Penginput</th>
                      <th>Aksi Persetujuan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $where_k = ($active_cabang_id === 0) ? "" : " AND (tk.id_cabang = '$active_cabang_id' OR b.id_cabang = '$active_cabang_id')";
                    $q_keluar = mysqli_query($mysqli, "SELECT tk.*, b.nama_barang, b.stok as stok_sekarang 
                                                       FROM tbl_barang_keluar tk 
                                                       JOIN tbl_barang b ON tk.barang = b.id_barang 
                                                       WHERE tk.status = 'Pending' $where_k
                                                       ORDER BY tk.tanggal DESC");
                    if (mysqli_num_rows($q_keluar) == 0):
                    ?>
                      <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                          <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                          Tidak ada pengajuan Barang Keluar yang pending.
                        </td>
                      </tr>
                    <?php
                    else:
                      while ($row = mysqli_fetch_assoc($q_keluar)):
                    ?>
                        <tr>
                          <td><span class="badge badge-primary"><?= htmlspecialchars($row['id_transaksi']) ?></span></td>
                          <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                          <td>
                            <strong><?= htmlspecialchars($row['nama_barang']) ?></strong>
                            <small class="d-block text-muted">ID: <?= htmlspecialchars($row['barang']) ?> (Stok Saat Ini: <?= $row['stok_sekarang'] ?>)</small>
                          </td>
                          <td><span class="badge badge-danger font-weight-bold">-<?= number_format($row['jumlah']) ?></span></td>
                          <td><?= htmlspecialchars($row['nama_lokasi'] ?? '-') ?></td>
                          <td><span class="badge badge-info"><?= htmlspecialchars($row['created_by_role'] ?? 'Staff') ?></span></td>
                          <td>
                            <button class="btn btn-sm btn-success mr-1" onclick="openApprovalModal('keluar', '<?= $row['id_transaksi'] ?>', 'approve', 'Setujui Barang Keluar <?= $row['id_transaksi'] ?> (-<?= $row['jumlah'] ?> unit dari <?= addslashes($row['nama_barang']) ?>)')">
                              <i class="fas fa-check mr-1"></i> Setujui
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="openApprovalModal('keluar', '<?= $row['id_transaksi'] ?>', 'reject', 'Tolak Barang Keluar <?= $row['id_transaksi'] ?>')">
                              <i class="fas fa-times mr-1"></i> Tolak
                            </button>
                          </td>
                        </tr>
                    <?php
                      endwhile;
                    endif;
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB ADJUSTMENT -->
            <div class="tab-pane fade" id="adj" role="tabpanel">
              <div class="table-responsive">
                <table class="table table-hover table-striped w-100">
                  <thead class="bg-light">
                    <tr>
                      <th>ID Adjustment</th>
                      <th>Tanggal</th>
                      <th>Barang</th>
                      <th>Hasil Penyesuaian (Stok Akhir)</th>
                      <th>Keterangan</th>
                      <th>Penginput</th>
                      <th>Aksi Persetujuan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $where_a = ($active_cabang_id === 0) ? "" : " AND b.id_cabang = '$active_cabang_id'";
                    $q_adj = mysqli_query($mysqli, "SELECT a.*, b.nama_barang, b.stok as stok_sekarang 
                                                    FROM adjustment a 
                                                    JOIN tbl_barang b ON a.id_barang = b.id_barang 
                                                    WHERE a.status = 'Pending' $where_a
                                                    ORDER BY a.tanggal_adjustment DESC");
                    if (mysqli_num_rows($q_adj) == 0):
                    ?>
                      <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                          <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                          Tidak ada pengajuan Adjustment yang pending.
                        </td>
                      </tr>
                    <?php
                    else:
                      while ($row = mysqli_fetch_assoc($q_adj)):
                    ?>
                        <tr>
                          <td><span class="badge badge-secondary">ADJ-<?= $row['id_adjustment'] ?></span></td>
                          <td><?= date('d-m-Y', strtotime($row['tanggal_adjustment'])) ?></td>
                          <td>
                            <strong><?= htmlspecialchars($row['nama_barang']) ?></strong>
                            <small class="d-block text-muted">Stok Sekarang: <?= $row['stok_sekarang'] ?></small>
                          </td>
                          <td>
                            <span class="badge badge-warning font-weight-bold"><?= number_format($row['jumlah_adjustment']) ?> Unit</span>
                            <small class="d-block text-muted">Selisih: <?= ($row['jumlah_adjustment'] - $row['stok_sekarang']) ?></small>
                          </td>
                          <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                          <td><span class="badge badge-info"><?= htmlspecialchars($row['created_by_role'] ?? 'Staff') ?></span></td>
                          <td>
                            <button class="btn btn-sm btn-success mr-1" onclick="openApprovalModal('adjustment', '<?= $row['id_adjustment'] ?>', 'approve', 'Setujui Adjustment ADJ-<?= $row['id_adjustment'] ?> (Set stok <?= addslashes($row['nama_barang']) ?> menjadi <?= $row['jumlah_adjustment'] ?> unit)')">
                              <i class="fas fa-check mr-1"></i> Setujui
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="openApprovalModal('adjustment', '<?= $row['id_adjustment'] ?>', 'reject', 'Tolak Adjustment ADJ-<?= $row['id_adjustment'] ?>')">
                              <i class="fas fa-times mr-1"></i> Tolak
                            </button>
                          </td>
                        </tr>
                    <?php
                      endwhile;
                    endif;
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Approval Action -->
<div class="modal fade" id="modalApproval" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title font-weight-bold" id="modalTitle"><i class="fas fa-check-circle mr-2"></i> Konfirmasi Persetujuan</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="aksi_approval" value="1">
          <input type="hidden" name="type" id="formType">
          <input type="hidden" name="id_val" id="formIdVal">
          <input type="hidden" name="action_type" id="formActionType">

          <p class="text-dark font-weight-bold mb-3" id="modalPromptText"></p>

          <div class="form-group p-0">
            <label for="catatan" class="font-weight-bold">Catatan Approval (Opsional):</label>
            <textarea class="form-control" name="catatan" id="catatan" rows="3" placeholder="Masukkan catatan atau alasan jika menolak..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSubmitApproval">Proses</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openApprovalModal(type, idVal, actionType, promptText) {
    document.getElementById('formType').value = type;
    document.getElementById('formIdVal').value = idVal;
    document.getElementById('formActionType').value = actionType;
    document.getElementById('modalPromptText').innerText = promptText;
    document.getElementById('catatan').value = '';

    var btn = document.getElementById('btnSubmitApproval');
    var title = document.getElementById('modalTitle');

    if (actionType === 'approve') {
      btn.className = 'btn btn-success';
      btn.innerHTML = '<i class="fas fa-check mr-1"></i> Ya, Setujui Sekarang';
      title.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Konfirmasi Setujui';
    } else {
      btn.className = 'btn btn-danger';
      btn.innerHTML = '<i class="fas fa-times mr-1"></i> Ya, Tolak Transaksi';
      title.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Konfirmasi Tolak';
    }

    $('#modalApproval').modal('show');
  }
</script>
