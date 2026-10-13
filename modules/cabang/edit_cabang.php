<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    // alihkan ke halaman error 404
    header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
    // mengecek data GET "id_cabang"
    if (isset($_GET['id'])) {
        // ambil data GET dari tombol ubah
        $id_cabang = $_GET['id'];

        // sql statement untuk menampilkan data dari tabel "tbl_cabang" berdasarkan "id_cabang"
        $query = mysqli_query($mysqli, "SELECT * FROM tbl_cabang WHERE id_cabang='$id_cabang'")
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
        // ambil data hasil query
        $data = mysqli_fetch_assoc($query);
    }
?>
    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-4">
            <div class="page-header text-white">
                <!-- judul halaman -->
                <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Cabang</h4>
                <!-- breadcrumbs -->
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=cabang" class="text-white">Cabang</a></li>
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
                <div class="card-title">Ubah Data Cabang</div>
            </div>
            <!-- form ubah data -->
            <form action="modules/cabang/proses_edit_cabang.php" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="id_cabang" value="<?php echo $data['id_cabang']; ?>">

                    <div class="form-group">
                        <label>Kode Lokasi Area <span class="text-danger">*</span></label>
                        <input type="text" name="kode_cabang" class="form-control col-lg-3 text-uppercase" autocomplete="off" value="<?php echo htmlspecialchars($data['kode_cabang'] ?? ''); ?>" required>
                        <small class="form-text text-muted">Kode lokasi ini nantinya menjadi awalan (prefix) penomoran rak.</small>
                        <div class="invalid-feedback">Kode Lokasi Area tidak boleh kosong.</div>
                    </div>

                    <div class="form-group">
                        <label>Nama Lokasi Area / Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_cabang" class="form-control col-lg-5" autocomplete="off" value="<?php echo htmlspecialchars($data['nama_cabang'] ?? ''); ?>" required>
                        <div class="invalid-feedback">Cabang tidak boleh kosong.</div>
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control col-lg-6" rows="3" autocomplete="off"><?php echo htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Nama PIC/Owner</label>
                        <input type="text" name="nama_fm" class="form-control col-lg-5" autocomplete="off" value="<?php echo htmlspecialchars($data['nama_fm'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" class="form-control col-lg-5" autocomplete="off" value="<?php echo htmlspecialchars($data['nomor_telepon'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Email Cabang</label>
                        <input type="email" name="email" class="form-control col-lg-5" autocomplete="off" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="card-action">
                    <!-- tombol simpan data -->
                    <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
                    <!-- tombol kembali ke halaman data cabang -->
                    <a href="?module=cabang" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
<?php } ?>