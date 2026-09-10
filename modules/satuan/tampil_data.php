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
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data satuan berhasil disimpan.'); });</script>";
    } elseif ($_GET['pesan'] == 2) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data satuan berhasil diubah.'); });</script>";
    } elseif ($_GET['pesan'] == 3) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data satuan berhasil dihapus.'); });</script>";
    } elseif ($_GET['pesan'] == 4) {
      $satuan = htmlspecialchars($_GET['satuan']);
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('warning', 'Gagal!', 'Satuan $satuan sudah ada. Silahkan ganti nama satuan.'); });</script>";
    } elseif ($_GET['pesan'] == 5) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Data satuan tidak bisa dihapus karena sudah tercatat pada Data Barang.'); });</script>";
    }
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <!-- judul halaman -->
          <h4 class="page-title text-white"><i class="fas fa-balance-scale mr-2"></i> Satuan Barang</h4>
          <!-- breadcrumbs -->
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=satuan" class="text-white">Satuan</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <!-- tombol entri data -->
          <a href="?module=form_entri_satuan" class="btn btn-secondary btn-round">
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
        <div class="card-title">Data Satuan</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <!-- tabel untuk menampilkan data dari database -->
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // variabel untuk nomor urut tabel
              $no = 1;
              // sql statement untuk menampilkan data dari tabel "tbl_satuan"
              $query = mysqli_query($mysqli, "SELECT * FROM tbl_satuan ORDER BY id_satuan DESC")
                                              or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
              // ambil data hasil query
              while ($data = mysqli_fetch_assoc($query)) { ?>
                <!-- tampilkan data -->
                <tr>
                  <td width="30" class="text-center"><?php echo $no++; ?></td>
                  <td width="300"><?php echo $data['nama_satuan']; ?></td>
                  <td width="100" class="text-center text-nowrap" style="white-space: nowrap;">
                    <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                      <!-- tombol ubah data -->
                      <a href="?module=form_ubah_satuan&id=<?php echo $data['id_satuan']; ?>" class="btn btn-icon btn-round btn-secondary btn-sm mr-1" data-toggle="tooltip" data-placement="top" title="Ubah">
                        <i class="fas fa-pencil-alt fa-sm"></i>
                      </a>
                      <!-- tombol hapus data -->
                      <a href="javascript:void(0)" onclick="return confirmDelete('modules/satuan/proses_hapus.php?id=<?php echo $data['id_satuan']; ?>', 'Anda yakin ingin menghapus data satuan <?php echo htmlspecialchars($data['nama_satuan'], ENT_QUOTES); ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
                        <i class="fas fa-trash fa-sm"></i>
                      </a>
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