<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
} else { ?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-user mr-2"></i> Manajemen User</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=user" class="text-white">User</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Entri</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul form -->
        <div class="card-title">Entri Data User</div>
      </div>
      <!-- form entri data -->
      <form action="modules/user/proses_entri.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <div class="form-group">
            <label>Nama User <span class="text-danger">*</span></label>
            <input type="text" name="nama_user" class="form-control col-lg-5" autocomplete="off" required>
            <div class="invalid-feedback">Nama user tidak boleh kosong.</div>
          </div>

          <div class="form-group">
            <label>Username <span class="text-danger">*</span></label>
            <input type="text" name="username" class="form-control col-lg-5" autocomplete="off" required>
            <div class="invalid-feedback">Username tidak boleh kosong.</div>
          </div>

          <div class="form-group">
            <label>Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control col-lg-5" autocomplete="off" required>
            <div class="invalid-feedback">Password tidak boleh kosong.</div>
          </div>

          <div class="form-group">
            <div class="d-flex align-items-center justify-content-between col-lg-5 p-0 mb-2">
              <label class="m-0 font-weight-bold">Cabang / Area <span class="text-danger">*</span></label>
              <?php if (in_array($_SESSION['hak_akses'] ?? '', ['Super Admin', 'Administrator'])): ?>
                <a href="?module=form_entri_cabang" class="btn btn-xs btn-primary btn-round px-2 py-1"><i class="fas fa-plus mr-1"></i> Tambah Cabang Baru</a>
              <?php endif; ?>
            </div>
            <?php if (in_array($_SESSION['hak_akses'] ?? '', ['Super Admin', 'Administrator'])): ?>
              <select name="id_cabang" class="form-control col-lg-5" autocomplete="off" required>
                <option selected disabled value="">-- Pilih Cabang --</option>
                <?php
                $query_cabang = mysqli_query($mysqli, "SELECT id_cabang, nama_cabang FROM tbl_cabang ORDER BY nama_cabang ASC");
                while ($c = mysqli_fetch_assoc($query_cabang)) {
                  echo "<option value='".$c['id_cabang']."'>".htmlspecialchars($c['nama_cabang'])."</option>";
                }
                ?>
              </select>
            <?php else: ?>
              <?php
              $user_cabang_id = $_SESSION['id_cabang'] ?? 0;
              $nama_cabang_locked = 'Gudang Utama';
              if ($user_cabang_id > 0) {
                $q_c = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang='$user_cabang_id'");
                if ($r_c = mysqli_fetch_assoc($q_c)) {
                  $nama_cabang_locked = $r_c['nama_cabang'];
                }
              }
              ?>
              <input type="text" class="form-control col-lg-5" value="<?= htmlspecialchars($nama_cabang_locked) ?>" readonly disabled>
              <input type="hidden" name="id_cabang" value="<?= $user_cabang_id ?>">
            <?php endif; ?>
            <div class="invalid-feedback">Cabang tidak boleh kosong.</div>
          </div>

          <div class="form-group">
            <label>Hak Akses <span class="text-danger">*</span></label>
            <select name="hak_akses" class="form-control col-lg-5" autocomplete="off" required>
              <option selected disabled value="">-- Pilih Hak Akses --</option>
              <?php 
              $current_user_role = $_SESSION['hak_akses'] ?? '';
              if ($current_user_role === 'Super Admin'): ?>
                <option value="Super Admin">Super Admin</option>
                <option value="Administrator">Administrator</option>
              <?php elseif ($current_user_role === 'Administrator'): ?>
                <option value="Administrator">Administrator</option>
              <?php endif; ?>
              <option value="Kepala Gudang">Kepala Gudang</option>
              <option value="Staff Gudang">Staff Gudang</option>
              <option value="Supplier">Supplier</option>
            </select>
            <div class="invalid-feedback">Hak akses tidak boleh kosong.</div>
          </div>
        </div>
        <div class="card-action">
          <!-- tombol simpan data -->
          <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <!-- tombol kembali ke halaman data user -->
          <a href="?module=user" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>
<?php } ?>