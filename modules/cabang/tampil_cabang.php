<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
    exit;
}

// Menampilkan pesan sesuai dengan proses yang dijalankan
if (isset($_GET['pesan'])) {
    switch ($_GET['pesan']) {
        case '1':
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data cabang berhasil disimpan.'); });</script>";
            break;

        case '2':
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data cabang berhasil diubah.'); });</script>";
            break;

        case '3':
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data cabang berhasil dihapus.'); });</script>";
            break;

        case '4':
            $cabang = htmlspecialchars($_GET['cabang'] ?? '');
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('warning', 'Gagal!', 'Cabang $cabang sudah ada. Silahkan ganti nama cabang yang Anda masukkan.'); });</script>";
            break;

        case '5':
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Data cabang tidak bisa dihapus karena sudah tercatat pada Data Barang.'); });</script>";
            break;
    }
}
?>

<div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
        <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
            <div class="page-header text-white">
                <h4 class="page-title text-white"><i class="fas fa-building mr-2"></i> Lokasi Area / Cabang</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=cabang" class="text-white">Area</a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a>Data</a></li>
                </ul>
            </div>
            <div class="ml-md-auto py-2 py-md-0">
                <a href="?module=form_entri_cabang" class="btn btn-secondary btn-round">
                    <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Data Lokasi Area</div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Lokasi Area</th>
                            <th class="text-center">Nama PIC/Owner</th>
                            <th class="text-center">Alamat Lengkap</th>
                            <th class="text-center">Kontak & Email</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($mysqli, "SELECT * FROM tbl_cabang ORDER BY id_cabang ASC")
                            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td class="font-weight-bold text-nowrap"><?php echo htmlspecialchars($data['nama_cabang']); ?></td>
                                <td><?php echo htmlspecialchars($data['nama_fm'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars($data['alamat'] ?: '-'); ?></td>
                                <td>
                                    <div><i class="fas fa-phone-alt fa-xs text-muted mr-1"></i> <?php echo htmlspecialchars($data['nomor_telepon'] ?: '-'); ?></div>
                                    <small class="text-muted"><i class="fas fa-envelope fa-xs mr-1"></i> <?php echo htmlspecialchars($data['email'] ?: '-'); ?></small>
                                </td>
                                <td width="100" class="text-center text-nowrap" style="white-space: nowrap;">
                                    <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                                        <a href="?module=edit_cabang&id=<?php echo $data['id_cabang']; ?>" class="btn btn-icon btn-round btn-secondary btn-sm mr-1" data-toggle="tooltip" data-placement="top" title="Ubah">
                                            <i class="fas fa-pencil-alt fa-sm"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="return confirmDelete('modules/cabang/proses_hapus_cabang.php?id=<?php echo $data['id_cabang']; ?>', 'Anda yakin ingin menghapus data cabang <?php echo htmlspecialchars($data['nama_cabang'], ENT_QUOTES); ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
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