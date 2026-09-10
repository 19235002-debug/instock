<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
} else {
  if (isset($_GET['id'])) {
    $id_user = mysqli_real_escape_string($mysqli, $_GET['id']);
    $query = mysqli_query($mysqli, "SELECT id_user, nama_user, username, hak_akses, id_cabang FROM tbl_user WHERE id_user='$id_user'")
                                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    $data = mysqli_fetch_assoc($query);
    if (!$data) {
      echo "<script>window.location.href='?module=user';</script>";
      exit;
    }

    $current_user_role = $_SESSION['hak_akses'] ?? '';
    $is_target_protected = in_array($data['hak_akses'], ['Super Admin', 'Administrator']);
    if ($is_target_protected && !in_array($current_user_role, ['Super Admin', 'Administrator'])) {
      echo "<script>window.location.href='?module=user&pesan=5';</script>";
      exit;
    }
  }
?>
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
          <li class="nav-item"><a>Ubah</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul form -->
        <div class="card-title">Ubah Data User</div>
      </div>
      <!-- form ubah data -->
      <form action="modules/user/proses_ubah.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <input type="hidden" name="id_user" value="<?php echo $data['id_user']; ?>">

          <div class="form-group">
            <label>Nama User <span class="text-danger">*</span></label>
            <input type="text" name="nama_user" class="form-control col-lg-5" autocomplete="off" value="<?php echo htmlspecialchars($data['nama_user']); ?>" required>
            <div class="invalid-feedback">Nama user tidak boleh kosong.</div>
          </div>

          <div class="form-group">
            <label>Username <span class="text-danger">*</span></label>
            <input type="text" name="username" class="form-control col-lg-5" autocomplete="off" value="<?php echo htmlspecialchars($data['username']); ?>" required>
            <div class="invalid-feedback">Username tidak boleh kosong.</div>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control col-lg-5" placeholder="Kosongkan password jika tidak diubah" autocomplete="off">
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
                <option value="">-- Pilih Cabang --</option>
                <?php
                $query_cabang = mysqli_query($mysqli, "SELECT id_cabang, nama_cabang FROM tbl_cabang ORDER BY nama_cabang ASC");
                while ($c = mysqli_fetch_assoc($query_cabang)) {
                  $selected = ($c['id_cabang'] == $data['id_cabang']) ? 'selected' : '';
                  echo "<option value='".$c['id_cabang']."' $selected>".htmlspecialchars($c['nama_cabang'])."</option>";
                }
                ?>
              </select>
            <?php else: ?>
              <?php
              $user_cabang_id = $_SESSION['id_cabang'] ?? $data['id_cabang'];
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
              <?php
              $roles_allowed = [];
              $current_user_role = $_SESSION['hak_akses'] ?? '';
              if ($current_user_role === 'Super Admin') {
                $roles_allowed = ['Super Admin', 'Administrator', 'Kepala Gudang', 'Staff Gudang', 'Supplier'];
              } elseif ($current_user_role === 'Administrator') {
                $roles_allowed = ['Administrator', 'Kepala Gudang', 'Staff Gudang', 'Supplier'];
              } else {
                $roles_allowed = ['Kepala Gudang', 'Staff Gudang', 'Supplier'];
              }

              foreach ($roles_allowed as $r) {
                $selected = ($r === $data['hak_akses']) ? 'selected' : '';
                echo "<option value='".htmlspecialchars($r)."' $selected>".htmlspecialchars($r)."</option>";
              }
              ?>
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