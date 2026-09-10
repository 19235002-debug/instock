<?php
// Mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di-include oleh file lain
// Jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // Alihkan ke halaman error 404
  header('location: 404.html');
}
// Jika file di-include oleh file lain, tampilkan isi file
else {
  // menampilkan pesan sesuai dengan proses yang dijalankan
  // jika pesan tersedia
  if (isset($_GET['pesan'])) {
    // Pesan-pesan yang sesuai dengan kebutuhan Anda
    $pesan = [
      1 => [
        'class' => 'success',
        'icon' => 'check',
        'title' => 'Sukses!',
        'message' => 'Data adjustment berhasil disimpan.'
      ],
      2 => [
        'class' => 'success',
        'icon' => 'check',
        'title' => 'Sukses!',
        'message' => 'Data adjustment berhasil diubah.'
      ],
      3 => [
        'class' => 'success',
        'icon' => 'check',
        'title' => 'Sukses!',
        'message' => 'Data adjustment berhasil dihapus.'
      ],
      4 => [
        'class' => 'danger',
        'icon' => 'times',
        'title' => 'Gagal!',
        'message' => 'Data adjustment gagal! karena jumlah stock kurang.'
      ]
    ];

  
    $kode_pesan = (int)$_GET['pesan'];

    if (isset($pesan[$kode_pesan])) {
      $toast_type = ($pesan[$kode_pesan]['class'] === 'danger') ? 'error' : $pesan[$kode_pesan]['class'];
      $alert_title = $pesan[$kode_pesan]['title'];
      $alert_message = addslashes($pesan[$kode_pesan]['message']);

      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('$toast_type', '$alert_title', '$alert_message'); });</script>";
    }
  }
?>
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-sliders-h mr-2"></i> Adjustment Stok</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=adjustment" class="text-white">Adjustment</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <!-- tombol entri data -->
        <?php
        $modul = "form_entri_adjustment";
        $role = ["Super Admin", "Administrator", "Kepala Gudang"];

        if (in_array($hak_akses, $role)) {
          echo '<a href="?module=' . $modul . '" class="btn btn-secondary btn-round">
                  <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data Penyesuaian
                </a>';
        }
        ?>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Data Adjustment</div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center text-nowrap" style="white-space: nowrap;">No.</th>
              <th class="text-center text-nowrap" style="white-space: nowrap;">ID Adjustment</th>
              <th class="text-center text-nowrap" style="white-space: nowrap;">ID Barang</th>
              <th class="text-center text-nowrap" style="white-space: nowrap;">Tanggal Adjustment</th>
              <th class="text-center text-nowrap" style="white-space: nowrap;">Jumlah Adjustment</th>
              <th class="text-center text-nowrap" style="white-space: nowrap;">Keterangan</th>
              <th class="text-center text-nowrap" style="white-space: nowrap;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $active_cabang_id = getActiveCabangId();
            $sql_adj = "SELECT adjustment.id_adjustment, tbl_barang.id_barang AS id_barang_tbl, tbl_barang.nama_barang, adjustment.tanggal_adjustment, adjustment.jumlah_adjustment, adjustment.keterangan
                        FROM adjustment
                        INNER JOIN tbl_barang ON adjustment.id_barang = tbl_barang.id_barang";
            if ($active_cabang_id !== 0) {
              $sql_adj .= " WHERE tbl_barang.id_cabang = '$active_cabang_id'";
            }
            $sql_adj .= " ORDER BY adjustment.id_adjustment DESC";
            $query = mysqli_query($mysqli, $sql_adj)
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
            while ($data = mysqli_fetch_assoc($query)) { ?>
              <tr>
                <td width="40" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $no++; ?></td>
                <td width="120" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $data['id_adjustment']; ?></td>
                <td width="120" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $data['id_barang_tbl']; ?></td>
                <td width="130" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $data['tanggal_adjustment']; ?></td>
                <td width="120" class="text-center text-nowrap" style="white-space: nowrap;"><?php echo $data['jumlah_adjustment']; ?></td>
                <td class="text-nowrap" style="white-space: nowrap;"><?php echo $data['keterangan']; ?></td>
                <td width="80" class="text-center text-nowrap" style="white-space: nowrap;">
                  <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                    <a href="javascript:void(0)" onclick="return confirmDelete('modules/adjustment/proses_hapus.php?id=<?php echo $data['id_adjustment']; ?>', 'Anda yakin ingin menghapus data adjustment ID <?php echo htmlspecialchars($data['id_adjustment'], ENT_QUOTES); ?>?')" class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus">
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
