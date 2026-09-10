<style>
.table th, .table td {
    border-left: 1px solid #e3e6f0;
    padding: 8px 12px;
}

.table th:first-child, .table td:first-child {
    border-left: none;
}
</style>

<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else { ?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-file-signature mr-2"></i> Laporan Stok</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Laporan</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Stok</a></li>
        </ul>
      </div>
    </div>
  </div>

  <?php
  // mengecek data hasil submit dari form filter
  // jika tidak ada data yang dikirim (tombol tampilkan belum diklik) 
  if (!isset($_POST['tampil'])) { ?>
    <div class="page-inner mt--5">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
          <!-- judul form -->
          <div class="card-title font-weight-bold text-dark"><i class="fas fa-filter text-primary mr-2"></i> Filter Data Stok</div>
        </div>
        <!-- form filter data -->
        <div class="card-body">
          <form action="?module=laporan_stok" method="post" class="needs-validation" novalidate>
            <div class="row align-items-end">
              <div class="col-md-5 col-lg-4 mb-3 mb-md-0">
                <div class="form-group p-0 m-0">
                  <label class="font-weight-bold text-dark mb-2">Kategori Stok <span class="text-danger">*</span></label>
                  <select name="stok" class="form-control" autocomplete="off" required>
                    <option selected disabled value="">-- Pilih Kategori Stok --</option>
                    <option value="Seluruh">Seluruh Barang</option>
                    <option value="Minimum">Batas Minimum (Stok Kritis)</option>
                  </select>
                  <div class="invalid-feedback">Silakan pilih kategori stok.</div>
                </div>
              </div>

              <div class="col-md-3 col-lg-2">
                <div class="form-group p-0 m-0">
                  <!-- tombol tampil data -->
                  <button type="submit" name="tampil" class="btn btn-secondary btn-round btn-block shadow-sm">
                    <i class="fas fa-search mr-2"></i> Tampilkan
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php
  }
  // jika ada data yang dikirim (tombol tampilkan diklik)
  else {
    // ambil data hasil submit dari form filter
    $stok = $_POST['stok'];
  ?>
    <div class="page-inner mt--5">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
          <!-- judul form -->
          <div class="card-title font-weight-bold text-dark"><i class="fas fa-filter text-primary mr-2"></i> Filter Data Stok</div>
        </div>
        <!-- form filter data -->
        <div class="card-body">
          <form action="?module=laporan_stok" method="post" class="needs-validation" novalidate>
            <div class="row align-items-end">
              <div class="col-md-4 col-lg-4 mb-3 mb-md-0">
                <div class="form-group p-0 m-0">
                  <label class="font-weight-bold text-dark mb-2">Kategori Stok <span class="text-danger">*</span></label>
                  <select name="stok" class="form-control" autocomplete="off" required>
                    <option value="Seluruh" <?php if ($_POST['stok'] == 'Seluruh') echo 'selected'; ?>>Seluruh Barang</option>
                    <option value="Minimum" <?php if ($_POST['stok'] == 'Minimum') echo 'selected'; ?>>Batas Minimum (Stok Kritis)</option>
                  </select>
                  <div class="invalid-feedback">Silakan pilih kategori stok.</div>
                </div>
              </div>

              <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
                <div class="form-group p-0 m-0">
                  <!-- tombol tampil data -->
                  <button type="submit" name="tampil" class="btn btn-secondary btn-round btn-block shadow-sm">
                    <i class="fas fa-search mr-2"></i> Tampilkan
                  </button>
                </div>
              </div>

              <div class="col-md-2 col-lg-2 mb-3 mb-md-0">
                <div class="form-group p-0 m-0">
                  <!-- tombol cetak laporan -->
                  <a href="modules/laporan-stok/cetak.php?stok=<?php echo urlencode($stok); ?>" target="_blank" class="btn btn-warning btn-round btn-block shadow-sm text-dark font-weight-bold">
                    <i class="fa fa-print mr-2"></i> Cetak
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <?php
        // mengecek filter data stok
        // jika filter data stok "Seluruh" dipilih
        if ($stok == 'Seluruh') { ?>
          <!-- tampilkan laporan stok seluruh barang -->
          <div class="card-header">
            <!-- judul tabel -->
            <div class="card-title"><i class="fas fa-file-alt mr-2"></i> Laporan Stok Seluruh Barang</div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <!-- tabel untuk menampilkan data dari database -->
              <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
                <thead>
                  <tr>
                    <th class="text-center">No.</th>
                    <th class="text-center">ID Barang</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Jenis Barang</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Satuan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $active_cabang_id = getActiveCabangId();
                  $where_cabang = ($active_cabang_id === 0) ? "" : " AND a.id_cabang = '$active_cabang_id'";
                  $no = 1;
                  $query = mysqli_query($mysqli, "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, b.nama_jenis, c.nama_satuan, d.nama_cabang
                                                  FROM tbl_barang as a 
                                                  INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                                                  INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan
                                                  LEFT JOIN tbl_cabang as d ON a.id_cabang=d.id_cabang
                                                  WHERE 1=1 $where_cabang
                                                  ORDER BY a.id_barang ASC")
                                                  or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data = mysqli_fetch_assoc($query)) { ?>
                    <!-- tampilkan data -->
                    <tr>
                      <td width="50" class="text-center"><?php echo $no++; ?></td>
                      <td width="80" class="text-center"><?php echo $data['id_barang']; ?></td>
                      <td width="200"><?php echo $data['nama_barang']; ?></td>
                      <td width="150"><?php echo $data['nama_jenis']; ?></td>
                      <?php
                      // mengecek data "stok"
                      // jika data stok minim
                      if ($data['stok'] <= $data['stok_minimum']) { ?>
                        <!-- tampilkan data dengan warna background -->
                        <td width="70" class="text-right"><span class="badge badge-warning"><?php echo $data['stok']; ?></span></td>
                      <?php }
                      // jika data stok tidak minim
                      else { ?>
                        <!-- tampilkan data tanpa warna background -->
                        <td width="70" class="text-right"><?php echo $data['stok']; ?></td>
                      <?php } ?>
                      <td width="70"><?php echo $data['nama_satuan']; ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php
        }
        // jika filter data stok "Minimum" dipilih
        else { ?>
          <!-- tampilkan laporan stok barang yang mencapai batas minimum -->
          <div class="card-header">
            <!-- judul tabel -->
            <div class="card-title"><i class="fas fa-file-alt mr-2"></i> Laporan Stok Barang yang Mencapai Batas Minimum</div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <!-- tabel untuk menampilkan data dari database -->
              <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
                <thead>
                  <tr>
                    <th class="text-center">No.</th>
                    <th class="text-center">ID Barang</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Jenis Barang</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Satuan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $query = mysqli_query($mysqli, "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, b.nama_jenis, c.nama_satuan, d.nama_cabang
                                                  FROM tbl_barang as a 
                                                  INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                                                  INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan
                                                  LEFT JOIN tbl_cabang as d ON a.id_cabang=d.id_cabang
                                                  WHERE a.stok<=a.stok_minimum $where_cabang 
                                                  ORDER BY a.id_barang ASC")
                                                  or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  // ambil data hasil query
                  while ($data = mysqli_fetch_assoc($query)) { ?>
                    <!-- tampilkan data -->
                    <tr>
                      <td width="50" class="text-center"><?php echo $no++; ?></td>
                      <td width="80" class="text-center"><?php echo $data['id_barang']; ?></td>
                      <td width="200"><?php echo $data['nama_barang']; ?></td>
                      <td width="150"><?php echo $data['nama_jenis']; ?></td>
                      <td width="70" class="text-right"><?php echo $data['stok']; ?></td>
                      <td width="70"><?php echo $data['nama_satuan']; ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
<?php
  }
}
?>