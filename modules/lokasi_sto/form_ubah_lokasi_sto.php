<?php
// Mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html'); // Alihkan ke halaman error 404 jika file diakses langsung
} else {
    // Jika file di-include oleh file lain, tampilkan isi file
    if (isset($_GET['id'])) {
        $id_lokasi = $_GET['id'];
        $id_cabang = $_SESSION['id_cabang'];
        $hak_akses = $_SESSION['hak_akses'];
        $query_wilayah = "SELECT * FROM tbl_wilayah WHERE id_cabang = '$id_cabang'";
        $result_wilayah = mysqli_query($mysqli, $query_wilayah);
        // SQL statement untuk menampilkan data dari tabel "tbl_lokasi_sto"
        $query = mysqli_query($mysqli, "SELECT * FROM tbl_lokasi_sto WHERE id_lokasi='$id_lokasi'")
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

        $data = mysqli_fetch_assoc($query); // Ambil data hasil query
    }
?>
    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-4">
            <div class="page-header text-white">
                <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Lokasi Sto</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=lokasi_sto" class="text-white">Lokasi Sto</a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a>Ubah</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Ubah Data Lokasi Sto</div>
            </div>
            <form action="modules/lokasi_sto/proses_ubah_lokasi_sto.php" method="post" class="needs-validation" novalidate>
                <div class="card-body">
                    <input type="hidden" name="id_lokasi" value="<?php echo $data['id_lokasi']; ?>">
                    <div class="form-group">
                        <label>Lokasi Sto <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lokasi" class="form-control col-lg-5" autocomplete="off" value="<?php echo $data['nama_lokasi']; ?>" required>
                        <div class="invalid-feedback">Lokasi Sto tidak boleh kosong.</div>
                    </div>
                    <div class="form-group">
                        <label>Kode Wilayah</label>
                        <select name="kode_wilayah" class="form-control col-lg-5">
                            <?php
                            while ($data_wilayah = mysqli_fetch_assoc($result_wilayah)) {
                                echo "<option value='" . $data_wilayah['kode_wilayah'] . "'>" . $data_wilayah['kode_wilayah'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Teritory Manager</label>
                        <select name="id_teritory" class="form-control col-lg-5">
                            <?php
                            $query_teritory = mysqli_query($mysqli, "SELECT * FROM teritory_manager");
                            while ($teritory = mysqli_fetch_assoc($query_teritory)) {
                                echo '<option value="' . $teritory['id_teritory'] . '" ' . ($teritory['id_teritory'] == $data['id_teritory'] ? 'selected' : '') . '>' . $teritory['teritory_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
        </div>
        <div class="card-action">
            <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
            <a href="?module=lokasi_sto" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
        </form>
    </div>
    </div>
<?php
}
?>