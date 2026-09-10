<?php
// Mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di-include oleh file lain
// Jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // Alihkan ke halaman error 404
  header('location: 404.html');
}

// Jika file di-include oleh file lain, tampilkan isi file
else {
  // Kode PHP untuk menyimpan data penyesuaian ke database jika form disubmit
  if (isset($_POST['simpan'])) {
    // Ambil data dari form
    $id_barang = $_POST['id_barang'];
    $tanggal_adjustment = $_POST['tanggal_adjustment'];
    $jumlah_adjustment = $_POST['jumlah_adjustment'];
    $keterangan = $_POST['keterangan'];

    // Lakukan validasi data (sesuai dengan kebutuhan Anda)

    // Ambil data stok barang berdasarkan ID Barang yang dipilih
    $query_stok = mysqli_query($mysqli, "SELECT stok FROM tbl_barang WHERE id_barang = '$id_barang'");
    $data_stok = mysqli_fetch_assoc($query_stok);
    $stok_barang = $data_stok['stok'];

    // Validasi apakah jumlah penyesuaian melebihi stok barang yang tersedia
    if ($jumlah_adjustment > $stok_barang) {
      // Jika jumlah penyesuaian melebihi stok, tampilkan pesan error
      echo '<div class="alert alert-danger">Jumlah penyesuaian melebihi stok barang yang tersedia (Stok: ' . $stok_barang . ').</div>';
    } else {
      // Ubah format tanggal_adjustment menjadi Tahun-Bulan-Hari (Y-m-d) sebelum disimpan ke database
      $tanggal_adjustment = date('Y-m-d', strtotime($tanggal_adjustment));

      // Lakukan penambahan data ke tabel "adjustment" pada database
      $query = mysqli_query($mysqli, "INSERT INTO adjustment (id_barang, tanggal_adjustment, jumlah_adjustment, keterangan)
                VALUES ('$id_barang', '$tanggal_adjustment', '$jumlah_adjustment', '$keterangan')");
      
      if ($query) {
        // Jika berhasil, alihkan ke halaman tampilan data adjustment dengan pesan sukses
        header('location: ?module=adjustment&pesan=1');
      } else {
        // Jika gagal, tampilkan pesan error
        echo '<div class="alert alert-danger">Gagal menyimpan data adjustment.</div>';
      }
    }
  }
}
?>

<!-- Kode HTML untuk form entri adjustment -->
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <!-- Judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-sliders-h mr-2"></i> Adjustment Stok</h4>
        <!-- Breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=adjustment" class="text-white">Adjustment</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Entri Data</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <!-- Judul form -->
      <div class="card-title">Form Entri Data Adjustment</div>
    </div>
    <!-- Form entri data adjustment -->
    <form action="modules/adjustment/proses_entri.php" method="post" class="needs-validation" novalidate>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>ID Barang <span class="text-danger">*</span></label>
              <!-- Tampilkan dropdown dengan data id_barang dari tabel tbl_barang -->
              <select name="id_barang" class="form-control" required>
                <option selected disabled value="">-- Pilih ID Barang --</option>
                <?php
                $active_cabang_id = getActiveCabangId();
                $sql_id_b = "SELECT id_barang, nama_barang FROM tbl_barang";
                if ($active_cabang_id !== 0) {
                  $sql_id_b .= " WHERE id_cabang = '$active_cabang_id'";
                }
                $sql_id_b .= " ORDER BY nama_barang ASC";
                $query_id_barang = mysqli_query($mysqli, $sql_id_b)
                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                // Loop untuk menampilkan setiap id_barang
                while ($data_id_barang = mysqli_fetch_assoc($query_id_barang)) {
                  echo "<option value='$data_id_barang[id_barang]'>$data_id_barang[id_barang] - " . htmlspecialchars($data_id_barang['nama_barang']) . "</option>";
                }
                ?>
              </select>
              <div class="invalid-feedback">ID Barang tidak boleh kosong.</div>
            </div>
            <div class="form-group">
              <label>Tanggal Adjustment <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_adjustment" class="form-control" autocomplete="off" required value="<?php echo date('Y-m-d'); ?>">
              <div class="invalid-feedback">Tanggal Adjustment tidak boleh kosong.</div>
            </div>
            <div class="form-group">
              <label>Jumlah Adjustment <span class="text-danger">*</span></label>
              <input type="number" name="jumlah_adjustment" class="form-control" autocomplete="off" required>
              <div class="invalid-feedback">Jumlah Adjustment tidak boleh kosong.</div>
            </div>
            <div class="form-group">
              <label>Keterangan</label>
              <textarea name="keterangan" class="form-control"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="card-action">
        <!-- Tombol simpan data -->
        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4">
        <!-- Tombol kembali ke halaman data adjustment -->
        <a href="?module=adjustment" class="btn btn-default btn-round pl-4 pr-4 ml-2">Batal</a>
      </div>
    </form>
  </div>
</div>
