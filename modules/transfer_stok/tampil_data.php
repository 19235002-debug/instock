<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    // alihkan ke halaman error 404
    header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
    // menampilkan pesan SweetAlert2 Toast sesuai dengan proses yang dijalankan
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == 1) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data Transfer Barang berhasil disimpan.'); });</script>";
        } elseif ($_GET['pesan'] == 2) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data Transfer Barang berhasil dihapus.'); });</script>";
        } elseif ($_GET['pesan'] == 3) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Anda tidak boleh menghapus data transfer barang ini.'); });</script>";
        }
    }
?>
    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-3">
            <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
                <div class="page-header text-white">
                    <h4 class="page-title text-white"><i class="fas fa-exchange-alt mr-2"></i> Transfer Stok</h4>
                    <ul class="breadcrumbs">
                        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a href="?module=transfer_stok" class="text-white">Transfer Barang</a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item"><a>Data</a></li>
                    </ul>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <a href="?module=form_entri_transfer" class="btn btn-secondary btn-round mr-2">
                        <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Data Transfer Barang</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Tanggal Transfer</th>
                                <th class="text-center">Dari Area</th>
                                <th class="text-center">Tujuan Area</th>
                                <th class="text-center">Nama Barang</th> <!-- Ubah ini -->
                                <th class="text-center">Jumlah Transfer</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $active_cabang_id = getActiveCabangId();
                            $where_transfer = ($active_cabang_id === 0) ? "" : "WHERE id_cabang_asal = '$active_cabang_id' OR id_cabang_tujuan = '$active_cabang_id'";
                            $query = mysqli_query($mysqli, "SELECT * FROM tbl_transfer_stok $where_transfer ORDER BY tanggal_transfer DESC") or die(mysqli_error($mysqli));
                            while ($data = mysqli_fetch_assoc($query)) {
                                $id_cabang_asal = $data['id_cabang_asal'];
                                $id_cabang_tujuan = $data['id_cabang_tujuan'];
                                $nama_barang = $data['nama_barang']; // Menggunakan nama_barang

                                // Query untuk mengambil nama cabang dari tabel cabang
                                $query_cabang_asal = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang = '$id_cabang_asal'");
                                $query_cabang_tujuan = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang = '$id_cabang_tujuan'");

                                // Mendapatkan nama cabang dari hasil query
                                $cabang_asal = mysqli_fetch_assoc($query_cabang_asal)['nama_cabang'];
                                $cabang_tujuan = mysqli_fetch_assoc($query_cabang_tujuan)['nama_cabang'];

                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td class="text-center"><?php echo date('d-m-Y', strtotime($data['tanggal_transfer'])); ?></td>
                                    <td class="text-center"><?php echo $cabang_asal; ?></td>
                                    <td class="text-center"><?php echo $cabang_tujuan; ?></td>
                                    <td class="text-center"><?php echo $nama_barang; ?></td> <!-- Menggunakan nama_barang -->
                                    <td class="text-center"><?php echo $data['jumlah_transfer']; ?></td>
                                    <td width="100" class="text-center text-nowrap" style="white-space: nowrap;">
                                        <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                                            <a href="?module=form_entri_transfer&id=<?php echo $data['id_transfer']; ?>" class="btn btn-icon btn-round btn-secondary btn-sm mr-1" data-toggle="tooltip" data-placement="top" title="Ubah">
                                                <i class="fas fa-pencil-alt fa-sm"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="return confirmDelete('modules/transfer_stok/proses_hapus.php?id=<?php echo $data['id_transfer']; ?>', 'Anda yakin ingin menghapus data transfer stok ini?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
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
<?php }
