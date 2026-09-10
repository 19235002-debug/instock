<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

// Hanya Administrator / Super Admin yang dapat mengakses Audit Log
if ($_SESSION['hak_akses'] != 'Super Admin' && $_SESSION['hak_akses'] != 'Administrator') {
  echo "<script>window.location.href='403.html';</script>";
  exit;
}

require_once "helper/audit_helper.php";
initAuditLogTable($mysqli);

// Clear Audit Log (hanya jika diminta oleh admin)
if (isset($_POST['clear_log'])) {
  mysqli_query($mysqli, "TRUNCATE TABLE tbl_audit_log");
  logActivity($mysqli, 'Pembersihan Log', 'audit_log', 'Administrator membersihkan seluruh riwayat audit log.');
  echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('info', 'Berhasil', 'Riwayat Audit Log telah dibersihkan.');
        });
    </script>";
}
?>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-history mr-2"></i> Audit Log System</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=audit_log" class="text-white">Audit Log</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh log audit?');">
          <button type="submit" name="clear_log" class="btn btn-danger btn-round">
            <i class="fas fa-trash-alt mr-1"></i> Bersihkan Log
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-list-alt mr-2 text-primary"></i> Rekam Aktivitas Pengguna</div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="basic-datatables" class="display table table-striped table-hover w-100">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Waktu</th>
                  <th>Pengguna</th>
                  <th>Role</th>
                  <th>Aksi</th>
                  <th>Modul</th>
                  <th>Rincian Aktivitas</th>
                  <th>IP Address</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($mysqli, "SELECT * FROM tbl_audit_log ORDER BY id_log DESC LIMIT 500");
                while ($data = mysqli_fetch_assoc($query)):
                  $role_badge = 'badge-secondary';
                  if ($data['role'] === 'Administrator') $role_badge = 'badge-danger';
                  elseif ($data['role'] === 'Kepala Gudang') $role_badge = 'badge-primary';
                  elseif ($data['role'] === 'Admin Gudang') $role_badge = 'badge-info';
                  elseif ($data['role'] === 'Supplier') $role_badge = 'badge-warning';
                ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><small class="font-weight-bold text-muted"><?= date('d/m/Y H:i:s', strtotime($data['timestamp'])) ?></small></td>
                    <td><strong><?= htmlspecialchars($data['username'] ?? 'System') ?></strong></td>
                    <td><span class="badge <?= $role_badge ?>"><?= htmlspecialchars($data['role'] ?? 'Guest') ?></span></td>
                    <td><span class="badge badge-outline-dark font-weight-bold"><?= htmlspecialchars($data['action']) ?></span></td>
                    <td><code><?= htmlspecialchars($data['module']) ?></code></td>
                    <td><?= htmlspecialchars($data['details'] ?? '-') ?></td>
                    <td><small class="text-muted"><i class="fas fa-network-wired mr-1"></i><?= htmlspecialchars($data['ip_address'] ?? '127.0.0.1') ?></small></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
