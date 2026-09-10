<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

// Hanya Administrator / Super Admin yang dapat mengelola Pengaturan Umum
if ($_SESSION['hak_akses'] != 'Super Admin' && $_SESSION['hak_akses'] != 'Administrator') {
  echo "<script>window.location.href='403.html';</script>";
  exit;
}

require_once "helper/setting_helper.php";
require_once "helper/audit_helper.php";

$setting = getSystemSetting($mysqli);

// Proses Simpan Pengaturan
if (isset($_POST['simpan_pengaturan'])) {
  $nama_aplikasi         = mysqli_real_escape_string($mysqli, trim($_POST['nama_aplikasi']));
  $slogan_aplikasi       = mysqli_real_escape_string($mysqli, trim($_POST['slogan_aplikasi']));
  $nama_perusahaan       = mysqli_real_escape_string($mysqli, trim($_POST['nama_perusahaan']));
  $alamat_perusahaan     = mysqli_real_escape_string($mysqli, trim($_POST['alamat_perusahaan']));
  $telepon               = mysqli_real_escape_string($mysqli, trim($_POST['telepon']));
  $email                 = mysqli_real_escape_string($mysqli, trim($_POST['email']));
  $footer_text           = mysqli_real_escape_string($mysqli, trim($_POST['footer_text']));
  $stok_minimum_default  = (int)($_POST['stok_minimum_default'] ?? 5);

  $logo_path    = $setting['logo'];
  $favicon_path = $setting['favicon'];

  // Folder upload gambar
  $upload_dir = 'images/img/';
  if (!file_exists($upload_dir)) {
    @mkdir($upload_dir, 0777, true);
  }

  // 1. Process Logo Upload
  if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['logo']['tmp_name'];
    $file_name = $_FILES['logo']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    if (in_array($file_ext, $allowed)) {
      $new_logo_name = 'logo_' . time() . '.' . $file_ext;
      $target_file = $upload_dir . $new_logo_name;

      if (move_uploaded_file($file_tmp, $target_file)) {
        $logo_path = $target_file;
      }
    }
  }

  // 2. Process Favicon Upload
  if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['favicon']['tmp_name'];
    $file_name = $_FILES['favicon']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'ico', 'svg'];

    if (in_array($file_ext, $allowed)) {
      $new_fav_name = 'favicon_' . time() . '.' . $file_ext;
      $target_file = $upload_dir . $new_fav_name;

      if (move_uploaded_file($file_tmp, $target_file)) {
        $favicon_path = $target_file;
      }
    }
  }

  // Update Database
  $query_update = "UPDATE tbl_setting SET 
        nama_aplikasi = '$nama_aplikasi',
        slogan_aplikasi = '$slogan_aplikasi',
        nama_perusahaan = '$nama_perusahaan',
        alamat_perusahaan = '$alamat_perusahaan',
        telepon = '$telepon',
        email = '$email',
        logo = '$logo_path',
        favicon = '$favicon_path',
        footer_text = '$footer_text',
        stok_minimum_default = $stok_minimum_default
        WHERE id_setting = 1";

  if (mysqli_query($mysqli, $query_update)) {
    logActivity($mysqli, 'Ubah Pengaturan Sistem', 'pengaturan', "Administrator memperbarui logo ($logo_path) dan identitas aplikasi.");
    $setting = getSystemSetting($mysqli); // Refresh data
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('success', 'Berhasil Disimpan!', 'Pengaturan umum sistem dan logo telah diperbarui.');
            });
        </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('error', 'Gagal!', 'Gagal memperbarui pengaturan sistem.');
            });
        </script>";
  }
}
?>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-sliders-h mr-2"></i> Pengaturan Umum</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=pengaturan" class="text-white">Pengaturan</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="simpan_pengaturan" value="1">

    <div class="row">
      <!-- Form Input Utama -->
      <div class="col-md-8">
        <div class="card card-round">
          <div class="card-header">
            <ul class="nav nav-tabs nav-line nav-color-primary card-header-tabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="tab-branding" data-toggle="tab" href="#branding" role="tab"><i class="fas fa-palette mr-2"></i> Branding & Logo</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tab-perusahaan" data-toggle="tab" href="#perusahaan" role="tab"><i class="fas fa-building mr-2"></i> Identitas Perusahaan</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tab-parameter" data-toggle="tab" href="#parameter" role="tab"><i class="fas fa-sliders-h mr-2"></i> Parameter Gudang</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tab-backup" data-toggle="tab" href="#backup" role="tab"><i class="fas fa-database mr-2 text-warning"></i> Backup Database SQL</a>
              </li>
            </ul>
          </div>

          <div class="card-body">
            <div class="tab-content">
              <!-- TAB 1: BRANDING & LOGO -->
              <div class="tab-pane fade show active" id="branding" role="tabpanel">
                <div class="form-group">
                  <label class="font-weight-bold" for="nama_aplikasi">Nama Aplikasi / System Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="nama_aplikasi" id="nama_aplikasi" value="<?= htmlspecialchars($setting['nama_aplikasi']) ?>" required>
                  <small class="form-text text-muted">Ditampilkan pada baris judul browser dan header aplikasi.</small>
                </div>

                <div class="form-group">
                  <label class="font-weight-bold" for="slogan_aplikasi">Slogan / Deskripsi Aplikasi</label>
                  <input type="text" class="form-control" name="slogan_aplikasi" id="slogan_aplikasi" value="<?= htmlspecialchars($setting['slogan_aplikasi']) ?>">
                  <small class="form-text text-muted">Tagline singkat sistem fulfillment gudang.</small>
                </div>

                <hr class="my-4">

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold" for="logo">Upload Logo Utama Aplikasi</label>
                      <input type="file" class="form-control-file" name="logo" id="logo" accept="image/*" onchange="previewImage(this, 'previewLogo')">
                      <small class="form-text text-muted">Format: PNG, JPG, WEBP, SVG (Maks. 2MB). Rekomendasi latar transparan.</small>
                    </div>
                  </div>
                  <div class="col-md-6 text-center">
                    <label class="font-weight-bold d-block">Pratinjau Logo Utama</label>
                    <div class="p-3 bg-light rounded border d-inline-block">
                      <img id="previewLogo" src="<?= htmlspecialchars($setting['logo']) ?>" alt="Logo App" style="max-height: 80px; max-width: 200px; object-fit: contain;">
                    </div>
                  </div>
                </div>

                <hr class="my-4">

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold" for="favicon">Upload Favicon / Icon Tab</label>
                      <input type="file" class="form-control-file" name="favicon" id="favicon" accept="image/*" onchange="previewImage(this, 'previewFavicon')">
                      <small class="form-text text-muted">Format: ICO, PNG (Maks 1MB). Ikon kecil di tab browser.</small>
                    </div>
                  </div>
                  <div class="col-md-6 text-center">
                    <label class="font-weight-bold d-block">Pratinjau Favicon</label>
                    <div class="p-3 bg-light rounded border d-inline-block">
                      <img id="previewFavicon" src="<?= htmlspecialchars($setting['favicon']) ?>" alt="Favicon App" style="width: 32px; height: 32px; object-fit: contain;">
                    </div>
                  </div>
                </div>
              </div>

              <!-- TAB 2: IDENTITAS PERUSAHAAN -->
              <div class="tab-pane fade" id="perusahaan" role="tabpanel">
                <div class="form-group">
                  <label class="font-weight-bold" for="nama_perusahaan">Nama Perusahaan / Organisasi</label>
                  <input type="text" class="form-control" name="nama_perusahaan" id="nama_perusahaan" value="<?= htmlspecialchars($setting['nama_perusahaan']) ?>">
                </div>

                <div class="form-group">
                  <label class="font-weight-bold" for="alamat_perusahaan">Alamat Perusahaan</label>
                  <textarea class="form-control" name="alamat_perusahaan" id="alamat_perusahaan" rows="3"><?= htmlspecialchars($setting['alamat_perusahaan']) ?></textarea>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold" for="telepon">No. Telepon / WhatsApp Contact</label>
                      <input type="text" class="form-control" name="telepon" id="telepon" value="<?= htmlspecialchars($setting['telepon']) ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold" for="email">Email Resmi Perusahaan</label>
                      <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($setting['email']) ?>">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="font-weight-bold" for="footer_text">Teks Hak Cipta Footer (Copyright Text)</label>
                  <input type="text" class="form-control" name="footer_text" id="footer_text" value="<?= htmlspecialchars($setting['footer_text']) ?>">
                  <small class="form-text text-muted">Teks yang ditampilkan di bagian bawah (footer) setiap halaman aplikasi.</small>
                </div>
              </div>

              <!-- TAB 3: PARAMETER GUDANG -->
              <div class="tab-pane fade" id="parameter" role="tabpanel">
                <div class="form-group">
                  <label class="font-weight-bold" for="stok_minimum_default">Default Stok Minimum Global</label>
                  <div class="input-group" style="max-width: 250px;">
                    <input type="number" min="0" class="form-control" name="stok_minimum_default" id="stok_minimum_default" value="<?= (int)$setting['stok_minimum_default'] ?>">
                    <div class="input-group-append">
                      <span class="input-group-text">Unit</span>
                    </div>
                  </div>
                  <small class="form-text text-muted">Batas peringatan stok minimum jika pada master barang tidak diset secara khusus.</small>
                </div>
              </div>

              <!-- TAB 4: BACKUP DATABASE SQL -->
              <div class="tab-pane fade" id="backup" role="tabpanel">
                <div class="alert alert-info border-0 shadow-sm rounded-lg mb-4" role="alert">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-database fa-2x mr-3 text-info"></i>
                    <div>
                      <h5 class="alert-heading font-weight-bold mb-1">Backup Database SQL Sistem INSTOCK</h5>
                      <p class="mb-0 small">Fitur ini memungkinkan Super Admin mengunduh seluruh salinan struktur skema dan data database (*.sql) sebagai arsip cadangan keamanan data gudang.</p>
                    </div>
                  </div>
                </div>

                <?php
                // Hitung statistik database
                $q_tables = mysqli_query($mysqli, "SHOW TABLE STATUS");
                $total_tables = 0;
                $total_rows = 0;
                $total_bytes = 0;
                if ($q_tables) {
                  while ($r_t = mysqli_fetch_assoc($q_tables)) {
                    $total_tables++;
                    $total_rows += $r_t['Rows'] ?? 0;
                    $total_bytes += ($r_t['Data_length'] ?? 0) + ($r_t['Index_length'] ?? 0);
                  }
                }
                $size_mb = round($total_bytes / (1024 * 1024), 2);
                ?>

                <div class="row mb-4">
                  <div class="col-sm-4">
                    <div class="card card-stats card-round border shadow-none bg-light mb-2">
                      <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                          <div class="icon-big text-center icon-primary mr-3">
                            <i class="fas fa-table"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Jumlah Tabel</small>
                            <h4 class="font-weight-bold m-0"><?= number_format($total_tables) ?> Tabel</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-4">
                    <div class="card card-stats card-round border shadow-none bg-light mb-2">
                      <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                          <div class="icon-big text-center icon-info mr-3">
                            <i class="fas fa-list-ol"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Total Data Baris</small>
                            <h4 class="font-weight-bold m-0"><?= number_format($total_rows) ?> Baris</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-4">
                    <div class="card card-stats card-round border shadow-none bg-light mb-2">
                      <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                          <div class="icon-big text-center icon-success mr-3">
                            <i class="fas fa-hdd"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Ukuran Database</small>
                            <h4 class="font-weight-bold m-0"><?= $size_mb ?> MB</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="p-4 bg-light rounded border text-center my-3">
                  <h5 class="font-weight-bold text-dark mb-2">Unduh File Cadangan Database (.sql)</h5>
                  <p class="text-muted small mb-3">Klik tombol di bawah ini untuk men-generate file sql dump lengkap yang dapat di-import kembali sewaktu-waktu.</p>
                  <a href="modules/pengaturan/export_database.php" class="btn btn-primary btn-round px-4 py-2 font-weight-bold shadow-sm">
                    <i class="fas fa-download mr-2"></i> Download Backup Database SQL (.sql)
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer bg-light text-right">
            <button type="submit" class="btn btn-success btn-round">
              <i class="fas fa-save mr-2"></i> Simpan Perubahan Pengaturan
            </button>
          </div>
        </div>
      </div>

      <!-- Card Summary / Informasi Pengaturan -->
      <div class="col-md-4">
        <div class="card card-round card-secondary bg-secondary-gradient text-white">
          <div class="card-body">
            <h4 class="card-title text-white font-weight-bold mb-3"><i class="fas fa-info-circle mr-2"></i> Ringkasan Sistem</h4>
            <div class="mb-3">
              <small class="text-white-50 d-block">Nama Aplikasi:</small>
              <strong class="h5 font-weight-bold text-white"><?= htmlspecialchars($setting['nama_aplikasi']) ?></strong>
            </div>
            <div class="mb-3">
              <small class="text-white-50 d-block">Perusahaan:</small>
              <span class="text-white font-weight-bold"><?= htmlspecialchars($setting['nama_perusahaan']) ?></span>
            </div>
            <div class="mb-3">
              <small class="text-white-50 d-block">Kontak Support:</small>
              <span class="text-white"><i class="fas fa-phone mr-1"></i> <?= htmlspecialchars($setting['telepon']) ?></span><br>
              <span class="text-white"><i class="fas fa-envelope mr-1"></i> <?= htmlspecialchars($setting['email']) ?></span>
            </div>
            <hr class="border-white opacity-25">
            <div>
              <small class="text-white-50 d-block">Terakhir Diperbarui:</small>
              <span class="badge badge-light font-weight-bold"><?= date('d/m/Y H:i:s', strtotime($setting['updated_at'] ?? 'now')) ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById(previewId).src = e.target.result;
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
