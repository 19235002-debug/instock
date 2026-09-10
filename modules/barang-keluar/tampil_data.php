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
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data barang keluar berhasil disimpan.'); });</script>";
    } elseif ($_GET['pesan'] == 2) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data barang keluar berhasil dihapus.'); });</script>";
    } elseif ($_GET['pesan'] == 3) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Anda tidak boleh menghapus data barang keluar ini.'); });</script>";
    }
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
      <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
        <div class="page-header text-white">
          <!-- judul halaman -->
          <h4 class="page-title text-white"><i class="fas fa-sign-out-alt mr-2"></i> Barang Keluar</h4>
          <!-- breadcrumbs -->
          <ul class="breadcrumbs">
            <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="?module=barang_keluar" class="text-white">Barang Keluar</a></li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a>Data</a></li>
          </ul>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
          <!-- tombol entri data -->
          <a href="?module=form_entri_barang_keluar" class="btn btn-secondary btn-round">
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
        <div class="card-title">Data Barang Keluar</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <!-- tabel untuk menampilkan data dari database -->
          <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center text-nowrap" style="white-space: nowrap;">No.</th>
                <th class="text-center text-nowrap" style="white-space: nowrap;">ID Transaksi</th>
                <th class="text-center text-nowrap" style="white-space: nowrap;">Tanggal</th>
                <th class="text-center text-nowrap" style="white-space: nowrap;">Barang</th>
                <th class="text-center text-nowrap" style="white-space: nowrap;">Lokasi STO</th>
                <th class="text-center text-nowrap" style="white-space: nowrap;">Jumlah Keluar</th>
                <th class="text-center text-nowrap" style="white-space: nowrap;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // variabel untuk nomor urut tabel
              $no = 1;
              // sql statement untuk menampilkan data dari tabel "tbl_barang_keluar", tabel "tbl_barang", dan tabel "tbl_satuan"
              // Mengecek hak akses dan id_cabang dari pengguna yang sudah login
              $hak_akses = $_SESSION['hak_akses'];
              $id_cabang = $_SESSION['id_cabang'];

              $active_cabang_id = getActiveCabangId();

              $sql = "SELECT a.id_transaksi, a.tanggal, a.barang, a.jumlah, b.nama_barang, c.nama_satuan, d.nama_lokasi
                      FROM tbl_barang_keluar as a 
                      INNER JOIN tbl_barang as b ON a.barang=b.id_barang 
                      INNER JOIN tbl_satuan as c ON b.satuan=c.id_satuan
                      LEFT JOIN tbl_lokasi_sto as d ON a.nama_lokasi=d.nama_lokasi";

              if ($active_cabang_id !== 0) {
                $sql .= " WHERE (a.id_cabang='$active_cabang_id' OR b.id_cabang='$active_cabang_id')";
              }

              $sql .= " ORDER BY a.id_transaksi DESC";
              $query = mysqli_query($mysqli, $sql) or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));


              // ambil data hasil query
              while ($data = mysqli_fetch_assoc($query)) { ?>
                <!-- tampilkan data -->
                <tr>
                  <td width="40" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $no++; ?></td>
                  <td width="120" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $data['id_transaksi']; ?></td>
                  <td width="110" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                  <td class="text-nowrap" style="white-space: nowrap;"><?php echo $data['barang']; ?> - <?php echo $data['nama_barang']; ?></td>
                  <td class="text-nowrap" style="white-space: nowrap;"><?php echo $data['nama_lokasi']; ?></td>
                  <td width="120" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo number_format($data['jumlah'], 0, '', '.'); ?></td>
                  <td width="80" class="text-center text-nowrap" style="white-space: nowrap;">
                    <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                      <!-- tombol hapus data -->
                      <a href="javascript:void(0)" onclick="return confirmDelete('modules/barang-keluar/proses_hapus.php?id=<?php echo $data['id_transaksi']; ?>', 'Anda yakin ingin menghapus data barang keluar <?php echo htmlspecialchars($data['id_transaksi'], ENT_QUOTES); ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
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