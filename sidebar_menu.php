<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
} else {
  require_once "config/database.php";
  require_once "helper/permission_helper.php";

  // Helper fungsi untuk menentukan apakah menu aktif
  if (!function_exists('isMenuActive')) {
    function isMenuActive($module)
    {
      $current = isset($_GET['module']) && !empty($_GET['module']) ? $_GET['module'] : 'dashboard';
      return $current == $module;
    }
  }

  $hak_akses = $_SESSION['hak_akses'] ?? '';

  // 1. MENU UNTUK SUPER ADMIN
  if ($hak_akses == 'Super Admin' || $hak_akses == 'Administrator') {
    $isMasterActive = isMenuActive('barang') || isMenuActive('jenis') || isMenuActive('satuan') || isMenuActive('location') || isMenuActive('lokasi_sto') || isMenuActive('cabang');
    $isLogistikActive = isMenuActive('transfer_stok') || isMenuActive('raja_ongkir') || isMenuActive('biteship_tracking');
    $isTransaksiActive = isMenuActive('barang_masuk') || isMenuActive('barang_keluar') || isMenuActive('adjustment') || isMenuActive('approval');
    $isLaporanActive = isMenuActive('laporan_stok') || isMenuActive('laporan_barang_masuk') || isMenuActive('laporan_barang_keluar');
    $isPengaturanActive = isMenuActive('pengaturan') || isMenuActive('user') || isMenuActive('permission') || isMenuActive('audit_log');
?>

    <!-- Dashboard -->
    <li class="nav-item <?= isMenuActive('dashboard') ? 'active' : '' ?>">
      <a href="?module=dashboard">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <!-- Section Master & Lokasi -->
    <li class="nav-section">
      <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
      <h4 class="text-section">Operasional & Master</h4>
    </li>

    <!-- Dropdown Master Data & Lokasi -->
    <li class="nav-item <?= $isMasterActive ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuMasterAdmin" aria-expanded="<?= $isMasterActive ? 'true' : 'false' ?>">
        <i class="fas fa-cubes"></i>
        <p>Master Data & Lokasi</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isMasterActive ? 'show' : '' ?>" id="menuMasterAdmin">
        <ul class="nav nav-collapse">
          <li class="<?= isMenuActive('barang') ? 'active' : '' ?>">
            <a href="?module=barang">
              <span class="sub-item"><i class="fas fa-box-open mr-2"></i>Data Barang</span>
            </a>
          </li>
          <li class="<?= isMenuActive('jenis') ? 'active' : '' ?>">
            <a href="?module=jenis">
              <span class="sub-item"><i class="fas fa-tags mr-2"></i>Jenis Barang</span>
            </a>
          </li>
          <li class="<?= isMenuActive('satuan') ? 'active' : '' ?>">
            <a href="?module=satuan">
              <span class="sub-item"><i class="fas fa-balance-scale mr-2"></i>Satuan Barang</span>
            </a>
          </li>
          <li class="<?= isMenuActive('location') ? 'active' : '' ?>">
            <a href="?module=location">
              <span class="sub-item"><i class="fas fa-tasks mr-2"></i>Lokasi Barang (Rak)</span>
            </a>
          </li>
          <li class="<?= isMenuActive('lokasi_sto') ? 'active' : '' ?>">
            <a href="?module=lokasi_sto">
              <span class="sub-item"><i class="fas fa-map-marker-alt mr-2"></i>Lokasi STO</span>
            </a>
          </li>
          <li class="<?= isMenuActive('cabang') ? 'active' : '' ?>">
            <a href="?module=cabang">
              <span class="sub-item"><i class="fas fa-building mr-2"></i>Lokasi Area / Cabang</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Dropdown Logistik & Pengiriman -->
    <li class="nav-item <?= $isLogistikActive ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuLogistikAdmin" aria-expanded="<?= $isLogistikActive ? 'true' : 'false' ?>">
        <i class="fas fa-truck-loading"></i>
        <p>Logistik & Pengiriman</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isLogistikActive ? 'show' : '' ?>" id="menuLogistikAdmin">
        <ul class="nav nav-collapse">
          <li class="<?= isMenuActive('transfer_stok') ? 'active' : '' ?>">
            <a href="?module=transfer_stok">
              <span class="sub-item"><i class="fas fa-exchange-alt mr-2"></i>Transfer Stok</span>
            </a>
          </li>
          <li class="<?= isMenuActive('raja_ongkir') ? 'active' : '' ?>">
            <a href="?module=raja_ongkir">
              <span class="sub-item"><i class="fas fa-calculator mr-2"></i>Cek Ongkir</span>
            </a>
          </li>
          <li class="<?= isMenuActive('biteship_tracking') ? 'active' : '' ?>">
            <a href="?module=biteship_tracking">
              <span class="sub-item"><i class="fas fa-shipping-fast mr-2"></i>Track Shipment</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Dropdown Transaksi -->
    <li class="nav-item <?= $isTransaksiActive ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuTransaksiAdmin" aria-expanded="<?= $isTransaksiActive ? 'true' : 'false' ?>">
        <i class="fas fa-receipt"></i>
        <p>Transaksi Inventaris</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isTransaksiActive ? 'show' : '' ?>" id="menuTransaksiAdmin">
        <ul class="nav nav-collapse">
          <li class="<?= isMenuActive('barang_masuk') ? 'active' : '' ?>">
            <a href="?module=barang_masuk">
              <span class="sub-item"><i class="fas fa-sign-in-alt mr-2"></i>Barang Masuk</span>
            </a>
          </li>
          <li class="<?= isMenuActive('barang_keluar') ? 'active' : '' ?>">
            <a href="?module=barang_keluar">
              <span class="sub-item"><i class="fas fa-sign-out-alt mr-2"></i>Barang Keluar</span>
            </a>
          </li>
          <li class="<?= isMenuActive('adjustment') ? 'active' : '' ?>">
            <a href="?module=adjustment">
              <span class="sub-item"><i class="fas fa-sliders-h mr-2"></i>Adjustment Stok</span>
            </a>
          </li>
          <li class="<?= isMenuActive('approval') ? 'active' : '' ?>">
            <a href="?module=approval">
              <span class="sub-item"><i class="fas fa-check-double text-success mr-2"></i>Persetujuan (Approval)</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Section Laporan & Sistem -->
    <li class="nav-section">
      <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
      <h4 class="text-section">Laporan & Pengaturan</h4>
    </li>

    <!-- Dropdown Laporan -->
    <li class="nav-item <?= $isLaporanActive ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuLaporanAdmin" aria-expanded="<?= $isLaporanActive ? 'true' : 'false' ?>">
        <i class="fas fa-chart-bar"></i>
        <p>Laporan & Analytics</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isLaporanActive ? 'show' : '' ?>" id="menuLaporanAdmin">
        <ul class="nav nav-collapse">
          <li class="<?= isMenuActive('laporan_stok') ? 'active' : '' ?>">
            <a href="?module=laporan_stok">
              <span class="sub-item"><i class="fas fa-file-signature mr-2"></i>Laporan Stok</span>
            </a>
          </li>
          <li class="<?= isMenuActive('laporan_barang_masuk') ? 'active' : '' ?>">
            <a href="?module=laporan_barang_masuk">
              <span class="sub-item"><i class="fas fa-file-import mr-2"></i>Laporan Barang Masuk</span>
            </a>
          </li>
          <li class="<?= isMenuActive('laporan_barang_keluar') ? 'active' : '' ?>">
            <a href="?module=laporan_barang_keluar">
              <span class="sub-item"><i class="fas fa-file-export mr-2"></i>Laporan Barang Keluar</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Dropdown Pengaturan & Keamanan -->
    <li class="nav-item <?= $isPengaturanActive ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuPengaturanAdmin" aria-expanded="<?= $isPengaturanActive ? 'true' : 'false' ?>">
        <i class="fas fa-cogs"></i>
        <p>Pengaturan & Sistem</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isPengaturanActive ? 'show' : '' ?>" id="menuPengaturanAdmin">
        <ul class="nav nav-collapse">
          <li class="<?= isMenuActive('pengaturan') ? 'active' : '' ?>">
            <a href="?module=pengaturan">
              <span class="sub-item"><i class="fas fa-sliders-h mr-2"></i>Pengaturan Umum</span>
            </a>
          </li>
          <li class="<?= isMenuActive('permission') ? 'active' : '' ?>">
            <a href="?module=permission">
              <span class="sub-item"><i class="fas fa-user-shield mr-2"></i>Hak Akses (Permission)</span>
            </a>
          </li>
          <li class="<?= isMenuActive('user') ? 'active' : '' ?>">
            <a href="?module=user">
              <span class="sub-item"><i class="fas fa-users-cog mr-2"></i>Manajemen User</span>
            </a>
          </li>
          <li class="<?= isMenuActive('audit_log') ? 'active' : '' ?>">
            <a href="?module=audit_log">
              <span class="sub-item"><i class="fas fa-history mr-2"></i>Audit Log System</span>
            </a>
          </li>
          <li>
            <a href="modules/pengaturan/export_database.php">
              <span class="sub-item"><i class="fas fa-database text-warning mr-2"></i>Backup Database SQL</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

  <?php
  // 2. MENU UNTUK KEPALA GUDANG
  } elseif ($hak_akses == 'Kepala Gudang') {
    $isMasterKG = (isModuleAllowed($hak_akses, 'barang', $mysqli) && isMenuActive('barang')) ||
      (isModuleAllowed($hak_akses, 'jenis', $mysqli) && isMenuActive('jenis')) ||
      (isModuleAllowed($hak_akses, 'satuan', $mysqli) && isMenuActive('satuan')) ||
      (isModuleAllowed($hak_akses, 'location', $mysqli) && isMenuActive('location')) ||
      (isModuleAllowed($hak_akses, 'lokasi_sto', $mysqli) && isMenuActive('lokasi_sto'));

    $isTransaksiKG = (isModuleAllowed($hak_akses, 'barang_masuk', $mysqli) && isMenuActive('barang_masuk')) ||
      (isModuleAllowed($hak_akses, 'barang_keluar', $mysqli) && isMenuActive('barang_keluar')) ||
      (isModuleAllowed($hak_akses, 'adjustment', $mysqli) && isMenuActive('adjustment')) ||
      isMenuActive('approval');

    $isLaporanKG = (isModuleAllowed($hak_akses, 'laporan_stok', $mysqli) && isMenuActive('laporan_stok')) ||
      (isModuleAllowed($hak_akses, 'laporan_barang_masuk', $mysqli) && isMenuActive('laporan_barang_masuk')) ||
      (isModuleAllowed($hak_akses, 'laporan_barang_keluar', $mysqli) && isMenuActive('laporan_barang_keluar'));
  ?>

    <li class="nav-item <?= isMenuActive('dashboard') ? 'active' : '' ?>">
      <a href="?module=dashboard">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <li class="nav-section">
      <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
      <h4 class="text-section">Menu Operasional</h4>
    </li>

    <!-- Master Data KG -->
    <li class="nav-item <?= $isMasterKG ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuMasterKG" aria-expanded="<?= $isMasterKG ? 'true' : 'false' ?>">
        <i class="fas fa-cubes"></i>
        <p>Master Data & Lokasi</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isMasterKG ? 'show' : '' ?>" id="menuMasterKG">
        <ul class="nav nav-collapse">
          <?php if (isModuleAllowed($hak_akses, 'barang', $mysqli)): ?>
            <li class="<?= isMenuActive('barang') ? 'active' : '' ?>"><a href="?module=barang"><span class="sub-item"><i class="fas fa-box-open mr-2"></i>Data Barang</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'jenis', $mysqli)): ?>
            <li class="<?= isMenuActive('jenis') ? 'active' : '' ?>"><a href="?module=jenis"><span class="sub-item"><i class="fas fa-tags mr-2"></i>Jenis Barang</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'satuan', $mysqli)): ?>
            <li class="<?= isMenuActive('satuan') ? 'active' : '' ?>"><a href="?module=satuan"><span class="sub-item"><i class="fas fa-balance-scale mr-2"></i>Satuan Barang</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'location', $mysqli)): ?>
            <li class="<?= isMenuActive('location') ? 'active' : '' ?>"><a href="?module=location"><span class="sub-item"><i class="fas fa-tasks mr-2"></i>Lokasi Barang (Rak)</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'lokasi_sto', $mysqli)): ?>
            <li class="<?= isMenuActive('lokasi_sto') ? 'active' : '' ?>"><a href="?module=lokasi_sto"><span class="sub-item"><i class="fas fa-map-marker-alt mr-2"></i>Lokasi STO</span></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <!-- Transaksi & Approval KG -->
    <li class="nav-item <?= $isTransaksiKG ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuTransaksiKG" aria-expanded="<?= $isTransaksiKG ? 'true' : 'false' ?>">
        <i class="fas fa-receipt"></i>
        <p>Transaksi & Persetujuan</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isTransaksiKG ? 'show' : '' ?>" id="menuTransaksiKG">
        <ul class="nav nav-collapse">
          <li class="<?= isMenuActive('approval') ? 'active' : '' ?>">
            <a href="?module=approval"><span class="sub-item"><i class="fas fa-check-double text-success mr-2"></i>Persetujuan (Approval)</span></a>
          </li>
          <?php if (isModuleAllowed($hak_akses, 'barang_masuk', $mysqli)): ?>
            <li class="<?= isMenuActive('barang_masuk') ? 'active' : '' ?>"><a href="?module=barang_masuk"><span class="sub-item"><i class="fas fa-sign-in-alt mr-2"></i>Barang Masuk</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'barang_keluar', $mysqli)): ?>
            <li class="<?= isMenuActive('barang_keluar') ? 'active' : '' ?>"><a href="?module=barang_keluar"><span class="sub-item"><i class="fas fa-sign-out-alt mr-2"></i>Barang Keluar</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'adjustment', $mysqli)): ?>
            <li class="<?= isMenuActive('adjustment') ? 'active' : '' ?>"><a href="?module=adjustment"><span class="sub-item"><i class="fas fa-sliders-h mr-2"></i>Adjustment Stok</span></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <!-- Laporan & User KG -->
    <li class="nav-item <?= $isLaporanKG ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuLaporanKG" aria-expanded="<?= $isLaporanKG ? 'true' : 'false' ?>">
        <i class="fas fa-chart-bar"></i>
        <p>Laporan Gudang</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isLaporanKG ? 'show' : '' ?>" id="menuLaporanKG">
        <ul class="nav nav-collapse">
          <?php if (isModuleAllowed($hak_akses, 'laporan_stok', $mysqli)): ?>
            <li class="<?= isMenuActive('laporan_stok') ? 'active' : '' ?>"><a href="?module=laporan_stok"><span class="sub-item"><i class="fas fa-file-signature mr-2"></i>Laporan Stok</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'laporan_barang_masuk', $mysqli)): ?>
            <li class="<?= isMenuActive('laporan_barang_masuk') ? 'active' : '' ?>"><a href="?module=laporan_barang_masuk"><span class="sub-item"><i class="fas fa-file-import mr-2"></i>Laporan Barang Masuk</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'laporan_barang_keluar', $mysqli)): ?>
            <li class="<?= isMenuActive('laporan_barang_keluar') ? 'active' : '' ?>"><a href="?module=laporan_barang_keluar"><span class="sub-item"><i class="fas fa-file-export mr-2"></i>Laporan Barang Keluar</span></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <?php if (isModuleAllowed($hak_akses, 'user', $mysqli)): ?>
      <li class="nav-item <?= isMenuActive('user') ? 'active' : '' ?>">
        <a href="?module=user">
          <i class="fas fa-users-cog"></i>
          <p>Manajemen User Cabang</p>
        </a>
      </li>
    <?php endif; ?>

  <?php
  // 3. MENU UNTUK STAFF GUDANG
  } elseif ($hak_akses == 'Staff Gudang') {
    $isMasterAG = (isModuleAllowed($hak_akses, 'barang', $mysqli) && isMenuActive('barang')) ||
      (isModuleAllowed($hak_akses, 'jenis', $mysqli) && isMenuActive('jenis')) ||
      (isModuleAllowed($hak_akses, 'satuan', $mysqli) && isMenuActive('satuan')) ||
      (isModuleAllowed($hak_akses, 'location', $mysqli) && isMenuActive('location')) ||
      (isModuleAllowed($hak_akses, 'lokasi_sto', $mysqli) && isMenuActive('lokasi_sto'));

    $isTransaksiAG = (isModuleAllowed($hak_akses, 'barang_masuk', $mysqli) && isMenuActive('barang_masuk')) ||
      (isModuleAllowed($hak_akses, 'barang_keluar', $mysqli) && isMenuActive('barang_keluar'));

    $isLaporanAG = (isModuleAllowed($hak_akses, 'laporan_stok', $mysqli) && isMenuActive('laporan_stok')) ||
      (isModuleAllowed($hak_akses, 'laporan_barang_keluar', $mysqli) && isMenuActive('laporan_barang_keluar'));
  ?>

    <li class="nav-item <?= isMenuActive('dashboard') ? 'active' : '' ?>">
      <a href="?module=dashboard">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <li class="nav-section">
      <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
      <h4 class="text-section">Tugas Lapangan</h4>
    </li>

    <!-- Master Data AG -->
    <li class="nav-item <?= $isMasterAG ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuMasterAG" aria-expanded="<?= $isMasterAG ? 'true' : 'false' ?>">
        <i class="fas fa-cubes"></i>
        <p>Master Data & Rak</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isMasterAG ? 'show' : '' ?>" id="menuMasterAG">
        <ul class="nav nav-collapse">
          <?php if (isModuleAllowed($hak_akses, 'barang', $mysqli)): ?>
            <li class="<?= isMenuActive('barang') ? 'active' : '' ?>"><a href="?module=barang"><span class="sub-item"><i class="fas fa-box-open mr-2"></i>Data Barang</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'jenis', $mysqli)): ?>
            <li class="<?= isMenuActive('jenis') ? 'active' : '' ?>"><a href="?module=jenis"><span class="sub-item"><i class="fas fa-tags mr-2"></i>Jenis Barang</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'satuan', $mysqli)): ?>
            <li class="<?= isMenuActive('satuan') ? 'active' : '' ?>"><a href="?module=satuan"><span class="sub-item"><i class="fas fa-balance-scale mr-2"></i>Satuan Barang</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'location', $mysqli)): ?>
            <li class="<?= isMenuActive('location') ? 'active' : '' ?>"><a href="?module=location"><span class="sub-item"><i class="fas fa-tasks mr-2"></i>Lokasi Barang (Rak)</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'lokasi_sto', $mysqli)): ?>
            <li class="<?= isMenuActive('lokasi_sto') ? 'active' : '' ?>"><a href="?module=lokasi_sto"><span class="sub-item"><i class="fas fa-map-marker-alt mr-2"></i>Lokasi STO</span></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <!-- Transaksi AG -->
    <li class="nav-item <?= $isTransaksiAG ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuTransaksiAG" aria-expanded="<?= $isTransaksiAG ? 'true' : 'false' ?>">
        <i class="fas fa-receipt"></i>
        <p>Transaksi Lapangan</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isTransaksiAG ? 'show' : '' ?>" id="menuTransaksiAG">
        <ul class="nav nav-collapse">
          <?php if (isModuleAllowed($hak_akses, 'barang_masuk', $mysqli)): ?>
            <li class="<?= isMenuActive('barang_masuk') ? 'active' : '' ?>"><a href="?module=barang_masuk"><span class="sub-item"><i class="fas fa-sign-in-alt mr-2"></i>Barang Masuk</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'barang_keluar', $mysqli)): ?>
            <li class="<?= isMenuActive('barang_keluar') ? 'active' : '' ?>"><a href="?module=barang_keluar"><span class="sub-item"><i class="fas fa-sign-out-alt mr-2"></i>Barang Keluar</span></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <!-- Laporan AG -->
    <li class="nav-item <?= $isLaporanAG ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuLaporanAG" aria-expanded="<?= $isLaporanAG ? 'true' : 'false' ?>">
        <i class="fas fa-chart-bar"></i>
        <p>Laporan Operasional</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isLaporanAG ? 'show' : '' ?>" id="menuLaporanAG">
        <ul class="nav nav-collapse">
          <?php if (isModuleAllowed($hak_akses, 'laporan_stok', $mysqli)): ?>
            <li class="<?= isMenuActive('laporan_stok') ? 'active' : '' ?>"><a href="?module=laporan_stok"><span class="sub-item"><i class="fas fa-file-signature mr-2"></i>Laporan Stok</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'laporan_barang_keluar', $mysqli)): ?>
            <li class="<?= isMenuActive('laporan_barang_keluar') ? 'active' : '' ?>"><a href="?module=laporan_barang_keluar"><span class="sub-item"><i class="fas fa-file-export mr-2"></i>Laporan Barang Keluar</span></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

  <?php
  // 4. MENU UNTUK SUPPLIER
  } elseif ($hak_akses == 'Supplier') {
    $isMasterSup = (isModuleAllowed($hak_akses, 'barang', $mysqli) && isMenuActive('barang')) ||
      (isModuleAllowed($hak_akses, 'barang_masuk', $mysqli) && isMenuActive('barang_masuk'));

    $isLaporanSup = (isModuleAllowed($hak_akses, 'laporan_stok', $mysqli) && isMenuActive('laporan_stok')) ||
      (isModuleAllowed($hak_akses, 'laporan_barang_masuk', $mysqli) && isMenuActive('laporan_barang_masuk'));
  ?>

    <li class="nav-item <?= isMenuActive('dashboard') ? 'active' : '' ?>">
      <a href="?module=dashboard">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <li class="nav-section">
      <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
      <h4 class="text-section">Suplai & Laporan</h4>
    </li>

    <!-- Menu Suplai Supplier -->
    <li class="nav-item <?= $isMasterSup ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuSupplier" aria-expanded="<?= $isMasterSup ? 'true' : 'false' ?>">
        <i class="fas fa-truck"></i>
        <p>Katalog & Pengiriman</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isMasterSup ? 'show' : '' ?>" id="menuSupplier">
        <ul class="nav nav-collapse">
          <?php if (isModuleAllowed($hak_akses, 'barang', $mysqli)): ?>
            <li class="<?= isMenuActive('barang') ? 'active' : '' ?>"><a href="?module=barang"><span class="sub-item"><i class="fas fa-box-open mr-2"></i>Data Barang</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'barang_masuk', $mysqli)): ?>
            <li class="<?= isMenuActive('barang_masuk') ? 'active' : '' ?>"><a href="?module=barang_masuk"><span class="sub-item"><i class="fas fa-sign-in-alt mr-2"></i>Entri Surat Jalan / Masuk</span></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <!-- Laporan Supplier -->
    <li class="nav-item <?= $isLaporanSup ? 'active' : '' ?>">
      <a data-toggle="collapse" href="#menuLaporanSup" aria-expanded="<?= $isLaporanSup ? 'true' : 'false' ?>">
        <i class="fas fa-chart-bar"></i>
        <p>Laporan Suplai</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?= $isLaporanSup ? 'show' : '' ?>" id="menuLaporanSup">
        <ul class="nav nav-collapse">
          <?php if (isModuleAllowed($hak_akses, 'laporan_stok', $mysqli)): ?>
            <li class="<?= isMenuActive('laporan_stok') ? 'active' : '' ?>"><a href="?module=laporan_stok"><span class="sub-item"><i class="fas fa-file-signature mr-2"></i>Laporan Stok</span></a></li>
          <?php endif; ?>
          <?php if (isModuleAllowed($hak_akses, 'laporan_barang_masuk', $mysqli)): ?>
            <li class="<?= isMenuActive('laporan_barang_masuk') ? 'active' : '' ?>"><a href="?module=laporan_barang_masuk"><span class="sub-item"><i class="fas fa-file-import mr-2"></i>Laporan Barang Masuk</span></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

<?php
  }
}
?>