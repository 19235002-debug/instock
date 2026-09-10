<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$id_cabang = $_SESSION['id_cabang'];
$hak_akses = $_SESSION['hak_akses'];

// Ambil nama_cabang berdasarkan id_cabang saat login
$query_nama_cabang = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang = '$id_cabang'");
$data_cabang = mysqli_fetch_assoc($query_nama_cabang);
$nama_cabang = $data_cabang['nama_cabang'];

// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
} else {
    // menampilkan pesan SweetAlert2 Toast sesuai dengan proses yang dijalankan
    if (isset($_GET['pesan'])) {
        $pesan = $_GET['pesan'];
        switch ($pesan) {
            case 1:
                echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data lokasi berhasil disimpan.'); });</script>";
                break;
            case 2:
                echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data lokasi berhasil diubah.'); });</script>";
                break;
            case 3:
                echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data lokasi berhasil dihapus.'); });</script>";
                break;
            case 4:
                $lokasi_sto = htmlspecialchars($_GET['lokasi_sto']);
                echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('warning', 'Gagal!', 'Lokasi STO $lokasi_sto sudah ada. Silahkan ganti nama lokasi.'); });</script>";
                break;
            case 5:
                echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Data lokasi STO tidak bisa dihapus karena sudah tercatat pada Data Barang.'); });</script>";
                break;
        }
    }

?>
    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-3">
            <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
                <div class="page-header text-white">
                    <!-- judul halaman -->
                    <h4 class="page-title text-white"><i class="fas fa-map-marker-alt mr-2"></i> Lokasi STO</h4>
                    <!-- breadcrumbs -->
                    <ul class="breadcrumbs">
                        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a href="?module=lokasi_sto" class="text-white">Lokasi STO</a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a>Data</a></li>
                    </ul>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <!-- tombol entri data -->
                    <a href="?module=form_entri_lokasi_sto" class="btn btn-secondary btn-round">
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
                <div class="card-title">Data Lokasi STO</div>
                <?php if ($hak_akses == "Super Admin" || $hak_akses == "Administrator") : ?>
                    <div class="float-right">
                        <form method="GET" action="">
                            <input type="hidden" name="module" value="lokasi_sto">
                            <select name="filter_cabang" class="form-control" onchange="this.form.submit()">
                                <option value="all">Semua Cabang</option>
                                <?php
                                $cabangQuery = mysqli_query($mysqli, "SELECT * FROM tbl_cabang");
                                while ($cabang = mysqli_fetch_assoc($cabangQuery)) {
                                    $selected = (isset($_GET['filter_cabang']) && $_GET['filter_cabang'] == $cabang['id_cabang']) ? 'selected' : '';
                                    echo "<option value='{$cabang['id_cabang']}' {$selected}>{$cabang['nama_cabang']}</option>";
                                }
                                ?>
                            </select>
                        </form>
                    </div>
                <?php endif; ?>

            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <!-- tabel untuk menampilkan data dari database -->
                    <table id="basic-datatables" class="display table table-bordered table-striped table-hover">

                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Lokasi STO</th>
                                <th class="text-center">Kode Wilayah</th>
                                <th class="text-center text-nowrap" style="white-space: nowrap;">Teritory Manager</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;
                            $active_cabang_id = getActiveCabangId();

                            $query_string = "SELECT tbl_lokasi_sto.*, tbl_cabang.nama_cabang 
                            FROM tbl_lokasi_sto 
                            LEFT JOIN tbl_cabang ON tbl_lokasi_sto.id_cabang = tbl_cabang.id_cabang";

                            if ($active_cabang_id !== 0) {
                                $query_string .= " WHERE tbl_lokasi_sto.id_cabang = '$active_cabang_id'";
                            }

                            $query = mysqli_query($mysqli, $query_string);

                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td width="30" class="text-center"><?php echo $no++; ?></td>
                                    <td width="250"><?php echo $data['nama_lokasi']; ?></td>
                                    <td width="200" class="text-center"><?php echo $data['kode_wilayah']; ?></td>
                                    <td width="250" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $data['nama_cabang']; ?></td> <!-- Tampilkan nama_cabang dari hasil join tabel -->
                                     <td width="100" class="text-center text-nowrap" style="white-space: nowrap;">
                                         <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                                             <!-- tombol ubah data -->
                                             <a href="?module=form_ubah_lokasi_sto&id=<?php echo $data['id_lokasi']; ?>" class="btn btn-icon btn-round btn-secondary btn-sm mr-1" data-toggle="tooltip" data-placement="top" title="Ubah">
                                                 <i class="fas fa-pencil-alt fa-sm"></i>
                                             </a>
                                             <!-- tombol hapus data -->
                                             <a href="javascript:void(0)" onclick="return confirmDelete('modules/lokasi_sto/proses_hapus_lokasi_sto.php?id=<?php echo $data['id_lokasi']; ?>', 'Anda yakin ingin menghapus lokasi STO <?php echo htmlspecialchars($data['nama_lokasi'], ENT_QUOTES); ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
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