<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

$id_user = (int)$_SESSION['id_user'];

// Query data user terbaru dari database
$query = mysqli_query($mysqli, "SELECT u.id_user, u.nama_user, u.username, u.hak_akses, u.id_cabang, u.foto, c.nama_cabang 
                                FROM tbl_user u 
                                LEFT JOIN tbl_cabang c ON u.id_cabang = c.id_cabang 
                                WHERE u.id_user = '$id_user'")
  or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
$data = mysqli_fetch_assoc($query);

$user_foto = $data['foto'] ?? '';
$user_avatar = (!empty($user_foto) && file_exists("assets/img/user/" . $user_foto)) 
  ? "assets/img/user/" . htmlspecialchars($user_foto) 
  : "assets/img/avatar-2.png";

// Menampilkan pesan SweetAlert2 Toast sesuai dengan proses yang dijalankan
if (isset($_GET['pesan'])) {
  if ($_GET['pesan'] == 1) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Username sudah digunakan oleh pengguna lain.'); });</script>";
  } elseif ($_GET['pesan'] == 2) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Password Baru dan Konfirmasi Password tidak cocok.'); });</script>";
  } elseif ($_GET['pesan'] == 3) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Profil Anda berhasil diperbarui.'); });</script>";
  } elseif ($_GET['pesan'] == 4) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Terjadi kesalahan saat memperbarui profil.'); });</script>";
  } elseif ($_GET['pesan'] == 5) {
    echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Format foto profil harus JPG, JPEG, PNG, atau WEBP (Maks 2MB).'); });</script>";
  }
}
?>

<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-user-alt mr-2"></i> Profil Saya</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Pengguna</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Edit Profil</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="row">
    <!-- Card Info Profil -->
    <div class="col-md-4">
      <div class="card card-profile card-round border-0 shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #6861ce 0%, #4941be 100%); min-height: 110px;"></div>
        <div class="card-body text-center pt-0">
          <div class="user-profile mb-3" style="margin-top: -55px;">
            <div class="mb-3 position-relative d-inline-block">
              <img id="avatar-preview" src="<?= $user_avatar ?>" alt="Avatar" class="rounded-circle shadow-sm" style="width: 110px; height: 110px; min-width: 110px; min-height: 110px; max-width: 110px; max-height: 110px; object-fit: cover; border: 4px solid #ffffff; background-color: #ffffff;">
            </div>
            <h3 class="font-weight-bold text-dark mb-1"><?= htmlspecialchars($data['nama_user']) ?></h3>
            <p class="text-muted font-weight-bold mb-2">@<?= htmlspecialchars($data['username']) ?></p>
            <span class="badge badge-primary px-3 py-2 font-weight-bold mb-3"><?= htmlspecialchars($data['hak_akses']) ?></span>
          </div>
          <hr class="my-3">
          <div class="text-left px-2">
            <div class="d-flex align-items-center mb-2">
              <i class="fas fa-building text-primary mr-3" style="width: 18px;"></i>
              <div>
                <small class="text-muted d-block">Cabang / Area</small>
                <span class="font-weight-bold text-dark"><?= htmlspecialchars($data['nama_cabang'] ?? 'Seluruh Cabang (Global)') ?></span>
              </div>
            </div>
            <div class="d-flex align-items-center mb-2">
              <i class="fas fa-camera text-info mr-3" style="width: 18px;"></i>
              <div>
                <small class="text-muted d-block">Foto Profil</small>
                <span class="font-weight-bold text-dark"><?= !empty($data['foto']) ? 'Foto Kustom' : 'Default Avatar' ?></span>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <i class="fas fa-shield-alt text-success mr-3" style="width: 18px;"></i>
              <div>
                <small class="text-muted d-block">Status Akun</small>
                <span class="badge badge-success px-2 py-1">Aktif</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Edit Profil -->
    <div class="col-md-8">
      <div class="card card-round border-0 shadow-sm">
        <div class="card-header bg-white py-3">
          <h4 class="card-title text-dark font-weight-bold mb-0">
            <i class="fas fa-user-edit text-primary mr-2"></i> Perbarui Data Profil
          </h4>
        </div>
        <form action="modules/profile/proses_ubah.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          <div class="card-body">
            <input type="hidden" name="id_user" value="<?= $data['id_user'] ?>">

            <div class="form-group">
              <label>Foto Profil Baru</label>
              <div class="custom-file">
                <input type="file" name="foto" class="custom-file-input" id="customFileFoto" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewAvatar(this);">
                <label class="custom-file-label" for="customFileFoto">Pilih file foto (JPG, PNG, WEBP, Maks 2MB)...</label>
              </div>
              <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto profil.</small>
            </div>

            <div class="form-group">
              <label>Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="nama_user" class="form-control" value="<?= htmlspecialchars($data['nama_user']) ?>" required autocomplete="off">
              <div class="invalid-feedback">Nama lengkap tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Username Login <span class="text-danger">*</span></label>
              <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required autocomplete="off">
              <div class="invalid-feedback">Username tidak boleh kosong.</div>
            </div>

            <div class="form-group">
              <label>Role / Hak Akses</label>
              <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($data['hak_akses']) ?>" readonly>
              <small class="form-text text-muted">Hak akses ditentukan oleh Administrator sistem.</small>
            </div>

            <div class="form-group">
              <label>Lokasi Cabang</label>
              <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($data['nama_cabang'] ?? 'Seluruh Cabang (Administrator Global)') ?>" readonly>
            </div>

            <hr class="my-4">
            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-key text-warning mr-2"></i> Ubah Password (Opsional)</h5>

            <div class="form-group">
              <label>Password Baru</label>
              <input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" autocomplete="off">
            </div>

            <div class="form-group">
              <label>Konfirmasi Password Baru</label>
              <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password baru" autocomplete="off">
            </div>
          </div>

          <div class="card-action bg-light py-3 px-4 text-right">
            <a href="?module=dashboard" class="btn btn-danger btn-round px-4 mr-2">Batal</a>
            <button type="submit" name="simpan" class="btn btn-secondary btn-round px-4">
              <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function previewAvatar(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('avatar-preview').src = e.target.result;
    }
    reader.readAsDataURL(input.files[0]);
    var fileName = input.files[0].name;
    $(input).next('.custom-file-label').html(fileName);
  }
}
</script>
