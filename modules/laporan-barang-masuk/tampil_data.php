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
        <h4 class="page-title text-white"><i class="fas fa-file-import mr-2"></i> Laporan Barang Masuk</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Laporan</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Barang Masuk</a></li>
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
          <div class="card-title font-weight-bold text-dark"><i class="fas fa-filter text-primary mr-2"></i> Filter Data Barang Masuk</div>
        </div>
        <!-- form filter data -->
        <div class="card-body">
          <form action="?module=laporan_barang_masuk" method="post" class="needs-validation" novalidate>
            <div class="row align-items-end">
              <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                <div class="form-group p-0 m-0">
                  <label class="font-weight-bold text-dark mb-2">Tanggal Awal <span class="text-danger">*</span></label>
                  <input type="text" name="tanggal_awal" class="form-control date-picker" autocomplete="off" placeholder="dd-mm-yyyy" required>
                  <div class="invalid-feedback">Tanggal awal tidak boleh kosong.</div>
                </div>
              </div>

              <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                <div class="form-group p-0 m-0">
                  <label class="font-weight-bold text-dark mb-2">Tanggal Akhir <span class="text-danger">*</span></label>
                  <input type="text" name="tanggal_akhir" class="form-control date-picker" autocomplete="off" placeholder="dd-mm-yyyy" required>
                  <div class="invalid-feedback">Tanggal akhir tidak boleh kosong.</div>
                </div>
              </div>

              <div class="col-md-4 col-lg-2">
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
    $tanggal_awal  = $_POST['tanggal_awal'];
    $tanggal_akhir = $_POST['tanggal_akhir'];
  ?>
    <div class="page-inner mt--5">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
          <!-- judul form -->
          <div class="card-title font-weight-bold text-dark"><i class="fas fa-filter text-primary mr-2"></i> Filter Data Barang Masuk</div>
        </div>
        <!-- form filter data -->
        <div class="card-body">
          <form action="?module=laporan_barang_masuk" method="post" class="needs-validation" novalidate>
            <div class="row align-items-end">
              <div class="col-md-3 col-lg-3 mb-3 mb-md-0">
                <div class="form-group p-0 m-0">
                  <label class="font-weight-bold text-dark mb-2">Tanggal Awal <span class="text-danger">*</span></label>
                  <input type="text" name="tanggal_awal" class="form-control date-picker" autocomplete="off" value="<?php echo $tanggal_awal; ?>" required>
                  <div class="invalid-feedback">Tanggal awal tidak boleh kosong.</div>
                </div>
              </div>

              <div class="col-md-3 col-lg-3 mb-3 mb-md-0">
                <div class="form-group p-0 m-0">
                  <label class="font-weight-bold text-dark mb-2">Tanggal Akhir <span class="text-danger">*</span></label>
                  <input type="text" name="tanggal_akhir" class="form-control date-picker" autocomplete="off" value="<?php echo $tanggal_akhir; ?>" required>
                  <div class="invalid-feedback">Tanggal akhir tidak boleh kosong.</div>
                </div>
              </div>

              <div class="col-md-2 col-lg-2 mb-3 mb-md-0">
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
                  <a href="modules/laporan-barang-masuk/cetak.php?tanggal_awal=<?php echo urlencode($tanggal_awal); ?>&tanggal_akhir=<?php echo urlencode($tanggal_akhir); ?>" target="_blank" class="btn btn-warning btn-round btn-block shadow-sm text-dark font-weight-bold">
                    <i class="fa fa-print mr-2"></i> Cetak
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <!-- judul tabel -->
          <div class="card-title">
            <i class="fas fa-file-alt mr-2"></i> Laporan Data Barang Masuk Tanggal <strong><?php echo $tanggal_awal; ?></strong> s.d. <strong><?php echo $tanggal_akhir; ?></strong>
          </div>
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
                  <th class="text-center text-nowrap" style="white-space: nowrap;">Jumlah Masuk</th>
                  <th class="text-center text-nowrap" style="white-space: nowrap;">Satuan</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // ubah format tanggal menjadi Tahun-Bulan-Hari (Y-m-d)
                $tanggal_awal  = date('Y-m-d', strtotime($tanggal_awal));
                $tanggal_akhir = date('Y-m-d', strtotime($tanggal_akhir));

                // variabel untuk nomor urut tabel
                $no = 1;

                $active_cabang_id = getActiveCabangId();
                $where_c = ($active_cabang_id === 0) ? "" : " AND (a.id_cabang = '$active_cabang_id' OR b.id_cabang = '$active_cabang_id')";
                $query = mysqli_query($mysqli, "SELECT a.id_transaksi, a.tanggal, a.barang, a.jumlah, b.nama_barang, c.nama_satuan
                                                FROM tbl_barang_masuk as a INNER JOIN tbl_barang as b INNER JOIN tbl_satuan as c 
                                                ON a.barang=b.id_barang AND b.satuan=c.id_satuan 
                                                WHERE a.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $where_c ORDER BY a.id_transaksi ASC")
                                                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                // ambil data hasil query
                while ($data = mysqli_fetch_assoc($query)) { ?>
                  <!-- tampilkan data -->
                  <tr>
                    <td width="40" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $no++; ?></td>
                    <td width="120" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $data['id_transaksi']; ?></td>
                    <td width="110" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                    <td class="text-nowrap" style="white-space: nowrap;"><?php echo $data['barang']; ?> - <?php echo $data['nama_barang']; ?></td>
                    <td width="120" class="text-right text-nowrap" style="white-space: nowrap;"><?php echo number_format($data['jumlah'], 0, '', '.'); ?></td>
                    <td width="80" class="text-nowrap" style="white-space: nowrap;"><?php echo $data['nama_satuan']; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
  <?php
  }
}
?>