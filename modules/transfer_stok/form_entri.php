<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$id_cabang = $_SESSION['id_cabang'];
$hak_akses = $_SESSION['hak_akses'];

if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
    exit;
}

function getCabangOptions($mysqli)
{
    $options = "";
    $query_cabang = mysqli_query($mysqli, "SELECT * FROM tbl_cabang ORDER BY nama_cabang ASC");
    while ($data_cabang = mysqli_fetch_assoc($query_cabang)) {
        $options .= "<option value='" . $data_cabang['id_cabang'] . "'>" . $data_cabang['nama_cabang'] . "</option>";
    }
    return $options;
}

$cabangOptions = getCabangOptions($mysqli);
?>

<div id="pesan"></div>
<div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
        <div class="page-header text-white">
            <h4 class="page-title text-white"><i class="fas fa-sign-in-alt mr-2"></i> Transfer Stok</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="?module=barang_masuk" class="text-white">Transfer Stok</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a>Entri</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="page-inner mt--5">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Entri Data Transfer Stok</div>
        </div>
        <form action="modules/transfer_stok/proses_entri.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="card-body">
                <div class="row">
                    <!-- Bagian Kiri -->
                    <div class="col-md-6">
                        <!-- Tanggal Transfer -->
                        <div class="form-group">
                            <label>Tanggal Transfer <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_transfer" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <!-- ID Cabang Asal -->
                        <div class="form-group">
                            <label>ID Cabang Asal <span class="text-danger">*</span></label>
                            <select name="id_cabang_asal" id="id_cabang_asal" class="form-control" required>
                                <option value="">-- Pilih Cabang Asal --</option>
                                <?php echo $cabangOptions; ?>
                            </select>
                        </div>
                        <!-- ID Cabang Tujuan -->
                        <div class="form-group">
                            <label>ID Cabang Tujuan <span class="text-danger">*</span></label>
                            <select name="id_cabang_tujuan" class="form-control" required>
                                <option value="">-- Pilih Cabang Tujuan --</option>
                                <?php echo $cabangOptions; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Bagian Kanan -->
                    <div class="col-md-6">
                        <!-- ID Barang -->
                        <div class="form-group">
                            <label>ID Barang <span class="text-danger">*</span></label>
                            <select name="nama_barang" id="id_barang" class="form-control" required>
                                <option value="">-- Pilih Barang --</option>
                                <!-- Opsi barang akan dimuat di sini -->
                            </select>
                        </div>
                        <!-- Stok Saat Ini -->
                        <div class="form-group">
                            <label>Stok Saat Ini</label>
                            <input type="text" id="stok_saat_ini" class="form-control" readonly>
                        </div>
                        <!-- Jumlah Transfer -->
                        <div class="form-group">
                            <label>Jumlah Transfer <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_transfer" class="form-control" required>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-action">
                <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
                <a href="?module=transfer_stok" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#id_cabang_asal').on('change', function() {
            var id_cabang = $(this).val();

            // Reset dropdown barang
            $('#id_barang').html('<option value="">-- Pilih Barang --</option>');

            // Reset stok
            $('#stok_saat_ini').val('');

            // Muat ulang barang berdasarkan cabang yang dipilih
            $.ajax({
                type: 'POST',
                url: 'get_barang.php',
                data: {
                    id_cabang: id_cabang
                },
                success: function(response) {
                    $('#id_barang').html(response);
                }
            });
        });

        // Ketika barang dipilih
        $('#id_barang').on('change', function() {
            var nama_barang = $(this).val();
            var id_cabang = $('#id_cabang_asal').val(); // Ambil ID cabang asal
            $.ajax({
                type: 'POST',
                url: 'get_stok.php', // File PHP untuk mengambil stok
                data: {
                    nama_barang: nama_barang,
                    id_cabang: id_cabang // Kirim ID cabang
                },
                success: function(response) {
                    $('#stok_saat_ini').val(response);
                }
            });
        });
    });
</script>