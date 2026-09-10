<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

// Hanya Administrator / Super Admin yang dapat mengelola Permission
if ($_SESSION['hak_akses'] != 'Super Admin' && $_SESSION['hak_akses'] != 'Administrator') {
  echo "<script>window.location.href='403.html';</script>";
  exit;
}

require_once "helper/permission_helper.php";
initPermissionTable($mysqli);

// Proses Simpan Perubahan Permission
if (isset($_POST['simpan_permission'])) {
  $roles = ['Kepala Gudang', 'Staff Gudang', 'Supplier'];
  $all_modules = ['barang', 'jenis', 'satuan', 'location', 'lokasi_sto', 'barang_masuk', 'barang_keluar', 'adjustment', 'laporan_stok', 'laporan_barang_masuk', 'laporan_barang_keluar', 'user'];

  $perm_input = $_POST['perm'] ?? [];

  foreach ($roles as $role) {
    foreach ($all_modules as $mod) {
      $is_allowed = isset($perm_input[$role][$mod]) ? 1 : 0;
      $role_esc = mysqli_real_escape_string($mysqli, $role);
      $mod_esc = mysqli_real_escape_string($mysqli, $mod);

      mysqli_query($mysqli, "INSERT INTO tbl_permission (role, module, is_allowed) 
                             VALUES ('$role_esc', '$mod_esc', $is_allowed) 
                             ON DUPLICATE KEY UPDATE is_allowed = $is_allowed");
    }
  }

  echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('success', 'Sukses!', 'Pengaturan Hak Akses Role berhasil diperbarui.');
        });
    </script>";
}

// Ambil permission terkini dari database
$perm_matrix = [];
$res_perm = mysqli_query($mysqli, "SELECT role, module, is_allowed FROM tbl_permission");
while ($r = mysqli_fetch_assoc($res_perm)) {
  $perm_matrix[$r['role']][$r['module']] = (int)$r['is_allowed'];
}

// Modul yang dapat diatur
$modules_config = [
  'Master Data' => [
    'barang' => ['label' => 'Data Barang', 'icon' => 'fas fa-cubes', 'desc' => 'Daftar & Katalog Barang'],
    'jenis' => ['label' => 'Jenis Barang', 'icon' => 'fas fa-tags', 'desc' => 'Kategori / Jenis Produk'],
    'satuan' => ['label' => 'Satuan Barang', 'icon' => 'fas fa-balance-scale', 'desc' => 'Unit Satuan Ukur'],
    'location' => ['label' => 'Lokasi Barang', 'icon' => 'fas fa-tasks', 'desc' => 'Rak & Posisi Penyimpanan'],
    'lokasi_sto' => ['label' => 'Lokasi STO', 'icon' => 'fas fa-users', 'desc' => 'Titik Gudang STO'],
  ],
  'Transaksi' => [
    'barang_masuk' => ['label' => 'Barang Masuk', 'icon' => 'fas fa-sign-in-alt', 'desc' => 'Penerimaan Stok Barang'],
    'barang_keluar' => ['label' => 'Barang Keluar', 'icon' => 'fas fa-sign-out-alt', 'desc' => 'Pengeluaran Stok Barang'],
    'adjustment' => ['label' => 'Adjustment Stok', 'icon' => 'fas fa-cogs', 'desc' => 'Penyesuaian Selisih Stok'],
  ],
  'Laporan' => [
    'laporan_stok' => ['label' => 'Laporan Stok', 'icon' => 'fas fa-file-signature', 'desc' => 'Cetak & Ekspor Stok'],
    'laporan_barang_masuk' => ['label' => 'Laporan Barang Masuk', 'icon' => 'fas fa-file-import', 'desc' => 'Cetak Transaksi Masuk'],
    'laporan_barang_keluar' => ['label' => 'Laporan Barang Keluar', 'icon' => 'fas fa-file-export', 'desc' => 'Cetak Transaksi Keluar'],
  ],
  'Pengaturan' => [
    'user' => ['label' => 'Manajemen User', 'icon' => 'fas fa-user-plus', 'desc' => 'Pengelolaan Staf & Pengguna'],
  ]
];

$managed_roles = [
  'Kepala Gudang' => ['color' => 'primary', 'badge' => 'badge-primary', 'icon' => 'fas fa-user-tie'],
  'Staff Gudang'  => ['color' => 'info', 'badge' => 'badge-info', 'icon' => 'fas fa-user-cog'],
  'Supplier'      => ['color' => 'success', 'badge' => 'badge-success', 'icon' => 'fas fa-truck'],
];
?>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-user-shield mr-2"></i> Hak Akses (Permission)</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=permission" class="text-white">Hak Akses</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <span class="badge badge-light text-secondary font-weight-bold px-3 py-2">
          <i class="fas fa-lock text-warning mr-1"></i> Khusus Hak Akses Administrator
        </span>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <form action="" method="POST">
    <div class="card card-round shadow-sm border-0">
      <div class="card-header bg-white border-0 py-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h4 class="card-title text-dark font-weight-bold mb-1">
              <i class="fas fa-sliders-h text-primary mr-2"></i>Matriks Izin Modul Per Peran Pengguna
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.88rem;">Atur centang/tombol untuk mengaktifkan atau menonaktifkan fitur bagi masing-masing peran.</p>
          </div>
          <div>
            <button type="submit" name="simpan_permission" class="btn btn-primary btn-round font-weight-bold px-4">
              <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover mb-0" style="vertical-align: middle;">
            <thead class="bg-light">
              <tr>
                <th class="text-center" width="50">No.</th>
                <th>Fitur / Modul Sistem</th>
                <?php foreach ($managed_roles as $role_name => $role_meta): ?>
                  <th class="text-center" width="180">
                    <span class="badge <?= $role_meta['badge'] ?> px-3 py-2 font-weight-bold" style="font-size: 0.85rem;">
                      <i class="<?= $role_meta['icon'] ?> mr-1"></i> <?= $role_name ?>
                    </span>
                  </th>
                <?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              foreach ($modules_config as $group_name => $group_modules):
                ?>
                <tr class="bg-primary text-white">
                  <td colspan="5" class="font-weight-bold py-2 px-3" style="font-size: 0.92rem; background: #1572e8;">
                    <i class="fas fa-layer-group mr-2"></i> KATEGORI: <?= strtoupper($group_name) ?>
                  </td>
                </tr>
                <?php foreach ($group_modules as $mod_key => $mod_info): ?>
                  <tr>
                    <td class="text-center font-weight-bold text-muted"><?= $no++; ?></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar-sm mr-3">
                          <span class="avatar-title bg-light text-primary rounded-circle border">
                            <i class="<?= $mod_info['icon'] ?>"></i>
                          </span>
                        </div>
                        <div>
                          <span class="font-weight-bold text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($mod_info['label']) ?></span>
                          <br>
                          <small class="text-muted"><?= htmlspecialchars($mod_info['desc']) ?></small>
                        </div>
                      </div>
                    </td>
                    <?php foreach ($managed_roles as $role_name => $role_meta):
                      $checked = isset($perm_matrix[$role_name][$mod_key]) ? ($perm_matrix[$role_name][$mod_key] == 1 ? 'checked' : '') : 'checked';
                      ?>
                      <td class="text-center align-middle">
                        <label class="custom-toggle" style="position: relative; display: inline-block; width: 50px; height: 26px;">
                          <input type="checkbox" name="perm[<?= $role_name ?>][<?= $mod_key ?>]" value="1" <?= $checked ?> style="opacity: 0; width: 0; height: 0;">
                          <span class="toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .3s; border-radius: 34px;"></span>
                        </label>
                      </td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer bg-white border-0 py-3 text-right">
        <button type="submit" name="simpan_permission" class="btn btn-primary btn-round font-weight-bold px-4">
          <i class="fas fa-save mr-2"></i> Simpan Perubahan Hak Akses
        </button>
      </div>
    </div>
  </form>
</div>

<style>
  /* Custom Toggle Switch Styling */
  .custom-toggle input:checked + .toggle-slider {
    background-color: #1572e8 !important;
  }
  .custom-toggle input:focus + .toggle-slider {
    box-shadow: 0 0 1px #1572e8;
  }
  .custom-toggle .toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
  }
  .custom-toggle input:checked + .toggle-slider:before {
    transform: translateX(23px);
  }
</style>
