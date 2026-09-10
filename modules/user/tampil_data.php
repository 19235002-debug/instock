<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
  // menampilkan pesan SweetAlert2 Toast sesuai dengan proses yang dijalankan
  if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 1) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data user berhasil disimpan.'); });</script>";
    } elseif ($_GET['pesan'] == 2) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data user berhasil diubah.'); });</script>";
    } elseif ($_GET['pesan'] == 3) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data user berhasil dihapus.'); });</script>";
    } elseif ($_GET['pesan'] == 4) {
      $username = htmlspecialchars($_GET['username']);
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('warning', 'Gagal!', 'Username $username sudah ada. Silahkan ganti username.'); });</script>";
    } elseif ($_GET['pesan'] == 5) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Akses Ditolak!', 'Anda tidak memiliki hak akses untuk mengubah atau menetapkan role tersebut.'); });</script>";
    }
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <!-- judul halaman -->
          <h4 class="page-title text-white"><i class="fas fa-users-cog mr-2"></i> Manajemen User</h4>
          <!-- breadcrumbs -->
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=user" class="text-white">User</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <!-- tombol entri data -->
          <a href="?module=form_entri_user" class="btn btn-secondary btn-round mr-2">
            <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul tabel -->
        <div class="card-title">Data User</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <!-- tabel untuk menampilkan data dari database -->
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Nama User</th>
                <th class="text-center">Username</th>
                <th class="text-center">Cabang</th>
                <th class="text-center">Hak Akses</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // variabel untuk nomor urut tabel
              $no = 1;
              $current_user_role = $_SESSION['hak_akses'] ?? '';
              // sql statement untuk menampilkan data dari tabel "tbl_user"
              $active_cabang_id = getActiveCabangId();
              if ($active_cabang_id !== 0) {
                $query = mysqli_query($mysqli, "SELECT u.id_user, u.nama_user, u.username, u.hak_akses, c.nama_cabang
                                            FROM tbl_user u
                                            LEFT JOIN tbl_cabang c ON u.id_cabang = c.id_cabang
                                            WHERE u.id_cabang='$active_cabang_id'
                                            ORDER BY u.id_user DESC")
                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
              } else {
                $query = mysqli_query($mysqli, "SELECT u.id_user, u.nama_user, u.username, u.hak_akses, c.nama_cabang
                                            FROM tbl_user u
                                            LEFT JOIN tbl_cabang c ON u.id_cabang = c.id_cabang
                                            ORDER BY u.id_user DESC")
                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
              }
              // ambil data hasil query
              while ($data = mysqli_fetch_assoc($query)) {
                $is_target_protected = in_array($data['hak_akses'], ['Super Admin', 'Administrator']);
                $can_manage = in_array($current_user_role, ['Super Admin', 'Administrator']) || !$is_target_protected;
              ?>
                <!-- tampilkan data -->
                <tr>
                  <td width="30" class="text-center"><?php echo $no++; ?></td>
                  <td width="180"><?php echo htmlspecialchars($data['nama_user']); ?></td>
                  <td width="180" class="text-center"><?php echo htmlspecialchars($data['username']); ?></td>
                  <td width="180" class="text-center"><?php echo !empty($data['nama_cabang']) ? htmlspecialchars($data['nama_cabang']) : '<span class="badge badge-light border text-muted">Semua / Global</span>'; ?></td>
                  <td width="140" class="text-center">
                    <?php
                    if ($data['hak_akses'] == 'Super Admin' || $data['hak_akses'] == 'Administrator') {
                      echo '<span class="badge badge-primary"><i class="fas fa-user-shield mr-1"></i> ' . htmlspecialchars($data['hak_akses']) . '</span>';
                    } elseif ($data['hak_akses'] == 'Kepala Gudang') {
                      echo '<span class="badge badge-info"><i class="fas fa-user-tie mr-1"></i> Kepala Gudang</span>';
                    } elseif ($data['hak_akses'] == 'Staff Gudang' || $data['hak_akses'] == 'Admin Gudang') {
                      echo '<span class="badge badge-secondary"><i class="fas fa-user-cog mr-1"></i> ' . htmlspecialchars($data['hak_akses']) . '</span>';
                    } elseif ($data['hak_akses'] == 'Supplier') {
                      echo '<span class="badge badge-warning text-dark"><i class="fas fa-truck mr-1"></i> Supplier</span>';
                    } else {
                      echo '<span class="badge badge-light">'.htmlspecialchars($data['hak_akses']).'</span>';
                    }
                    ?>
                  </td>

                  <td width="100" class="text-center text-nowrap" style="white-space: nowrap;">
                    <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                      <?php if ($can_manage): ?>
                        <!-- tombol ubah data -->
                        <a href="?module=form_ubah_user&id=<?php echo $data['id_user']; ?>" class="btn btn-icon btn-round btn-secondary btn-sm mr-1" data-toggle="tooltip" data-placement="top" title="Ubah">
                          <i class="fas fa-pencil-alt fa-sm"></i>
                        </a>
                        <!-- tombol hapus data -->
                        <a href="javascript:void(0)" onclick="return confirmDelete('modules/user/proses_hapus.php?id=<?php echo $data['id_user']; ?>', 'Anda yakin ingin menghapus data user dengan username <?php echo htmlspecialchars($data['username'], ENT_QUOTES); ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
                          <i class="fas fa-trash fa-sm"></i>
                        </a>
                      <?php else: ?>
                        <span class="badge badge-light border text-muted px-2 py-1"><i class="fas fa-lock mr-1"></i> Terkunci</span>
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
<?php } ?>