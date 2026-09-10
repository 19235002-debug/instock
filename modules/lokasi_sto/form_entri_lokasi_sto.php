<?php

// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    // alihkan ke halaman error 404
    header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
    // Pastikan sesi diaktifkan
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Ambil id_cabang dan hak_akses dari sesi
    $id_cabang = $_SESSION['id_cabang'];
    $hak_akses = $_SESSION['hak_akses'];
    $query_wilayah = "SELECT * FROM tbl_wilayah WHERE id_cabang = '$id_cabang'";
    $result_wilayah = mysqli_query($mysqli, $query_wilayah);
?>

    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-4">
            <div class="page-header text-white">
                <!-- judul halaman -->
                <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Lokasi STO</h4>
                <!-- breadcrumbs -->
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=lokasi_sto" class="text-white">Lokasi STO</a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a>Entri</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <!-- judul form -->
                <div class="card-title">Entri Data Lokasi STO</div>
            </div>
            <!-- form entri data -->
            <form action="modules/lokasi_sto/proses_entri_lokasi_sto.php" method="post" class="needs-validation" novalidate>

                <div class="card-body">
                    <div class="form-group">
                        <label>Lokasi STO <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lokasi" class="form-control col-lg-5" autocomplete="off" required>
                        <div class="invalid-feedback">Lokasi STO tidak boleh kosong.</div>
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
                            // Mengambil id_cabang dari sesi
                            $id_cabang_login = $_SESSION['id_cabang'];

                            // Mengambil nama cabang berdasarkan id_cabang
                            $query_cabang = "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang = '$id_cabang_login'";
                            $result_cabang = mysqli_query($mysqli, $query_cabang);

                            if ($result_cabang && mysqli_num_rows($result_cabang) > 0) {
                                $data_cabang = mysqli_fetch_assoc($result_cabang);
                                $nama_cabang = $data_cabang['nama_cabang'];

                                // Menampilkan nama cabang sebagai opsi teritory manager yang dipilih dan readonly
                                echo "<option value='$id_cabang_login' selected readonly>$nama_cabang</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <input type="hidden" name="id_cabang" value="<?php echo $id_cabang; ?>">



                    <div class="card-action">
                        <!-- tombol simpan data -->
                        <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
                        <!-- tombol kembali ke halaman data lokasi_sto -->
                        <a href="?module=lokasi_sto" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
                    </div>
            </form>
        </div>
    </div>
<?php } ?>