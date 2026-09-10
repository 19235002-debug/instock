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
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data jenis barang berhasil disimpan.'); });</script>";
    } elseif ($_GET['pesan'] == 2) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data jenis barang berhasil diubah.'); });</script>";
    } elseif ($_GET['pesan'] == 3) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data jenis barang berhasil dihapus.'); });</script>";
    } elseif ($_GET['pesan'] == 4) {
      $jenis = htmlspecialchars($_GET['jenis']);
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('warning', 'Gagal!', 'Jenis barang $jenis sudah ada. Silahkan ganti nama jenis barang.'); });</script>";
    } elseif ($_GET['pesan'] == 5) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Data jenis barang tidak bisa dihapus karena sudah tercatat pada Data Barang.'); });</script>";
    }
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <!-- judul halaman -->
          <h4 class="page-title text-white"><i class="fas fa-tags mr-2"></i> Jenis Barang</h4>
          <!-- breadcrumbs -->
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=jenis" class="text-white">Jenis Barang</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <!-- tombol entri data -->
          <a href="?module=form_entri_jenis" class="btn btn-secondary btn-round">
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
        <div class="card-title">Data Jenis Barang</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <!-- tabel untuk menampilkan data dari database -->
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Jenis Barang</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // variabel untuk nomor urut tabel
              $no = 1;
              // sql statement untuk menampilkan data dari tabel "tbl_jenis"
              $query = mysqli_query($mysqli, "SELECT * FROM tbl_jenis ORDER BY id_jenis DESC")
                                              or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
              // ambil data hasil query
              while ($data = mysqli_fetch_assoc($query)) { ?>
                <!-- tampilkan data -->
                <tr>
                  <td width="30" class="text-center"><?php echo $no++; ?></td>
                  <td width="300"><?php echo $data['nama_jenis']; ?></td>
                  <td width="100" class="text-center text-nowrap" style="white-space: nowrap;">
                    <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                      <!-- tombol ubah data -->
                      <a href="?module=form_ubah_jenis&id=<?php echo $data['id_jenis']; ?>" class="btn btn-icon btn-round btn-secondary btn-sm mr-1" data-toggle="tooltip" data-placement="top" title="Ubah">
                        <i class="fas fa-pencil-alt fa-sm"></i>
                      </a>
                      <!-- tombol hapus data -->
                      <a href="javascript:void(0)" onclick="return confirmDelete('modules/jenis/proses_hapus.php?id=<?php echo $data['id_jenis']; ?>', 'Anda yakin ingin menghapus data jenis barang <?php echo htmlspecialchars($data['nama_jenis'], ENT_QUOTES); ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
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