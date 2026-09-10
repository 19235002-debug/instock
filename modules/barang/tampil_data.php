<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$id_cabang = $_SESSION['id_cabang'];
$hak_akses = $_SESSION['hak_akses'];
// Mencegah direct access
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}


// Mengambil jenis dari tabel tbl_jenis
$jenisQuery = mysqli_query($mysqli, "SELECT DISTINCT id_jenis, nama_jenis FROM tbl_jenis");
$jenisList = [];
while ($jenisRow = mysqli_fetch_assoc($jenisQuery)) {
  $jenisList[] = $jenisRow;
}

// Menampilkan pesan berdasarkan query
if (isset($_GET['pesan'])) {
  switch ($_GET['pesan']) {
    case 1:
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data barang berhasil disimpan.'); });</script>";
      break;
    case 2:
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data barang berhasil diubah.'); });</script>";
      break;
    case 3:
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Data barang berhasil dihapus.'); });</script>";
      break;
    case 4:
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal!', 'Data barang tidak bisa dihapus karena sudah tercatat pada Data Transaksi.'); });</script>";
      break;
  }
}
?>



<!-- Tambahkan skrip jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Tambahkan fungsi "centang semua" -->
<script>
  $(document).ready(function () {
    // Ketika checkbox "Pilih Semua" diubah
    $('#checkAll').on('change', function () {
      if ($(this).prop('checked')) {
        $('.itemCheckbox').prop('checked', true); // Centang semua checkbox individual
      } else {
        $('.itemCheckbox').prop('checked', false); // Hapus centang dari semua checkbox individual
      }
    });

    // Ketika salah satu checkbox individual diubah
    $('.itemCheckbox').on('change', function () {
      if ($('.itemCheckbox:checked').length == $('.itemCheckbox').length) {
        $('#checkAll').prop('checked', true); // Centang checkbox "Pilih Semua"
      } else {
        $('#checkAll').prop('checked', false); // Hapus centang dari checkbox "Pilih Semua"
      }
    });

    // Fungsi untuk mencetak label barang yang dipilih
    $('#printSelected').click(function (e) {
      e.preventDefault();

      // Ambil semua ID barang yang dipilih
      var selectedItems = $('.itemCheckbox:checked').map(function () {
        return $(this).val();
      }).get();

      // Jika tidak ada barang yang dipilih, berikan peringatan
      if (selectedItems.length == 0) {
        alert("Pilih setidaknya satu barang untuk dicetak.");
        return;
      }

      // Buat form sementara untuk mengirim data ke halaman cetak
      var form = $('<form>', {
        'action': '?module=print_label',
        'method': 'post',
        'target': '_blank'
      });

      $.each(selectedItems, function (index, value) {
        form.append($('<input>', {
          'type': 'hidden',
          'name': 'selected_items[]',
          'value': value
        }));
      });

      // Tambahkan form ke dokumen dan kirim
      $('body').append(form);
      form.submit();
    });
  });
</script>


<!-- Bagian tampilan -->
<div class="panel-header bg-secondary-gradient">
  <div class="page-inner py-3">
    <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-box-open mr-2"></i> Data Barang</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang" class="text-white">Barang</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Data</a></li>
        </ul>
      </div>
      <div class="ml-md-auto py-2 py-md-0">
        <!-- tombol entri data -->
        <a href="?module=form_entri_barang" class="btn btn-secondary btn-round">
          <span class="btn-label"><i class="fa fa-plus mr-2"></i></span> Entri Data
        </a>
        <!-- tombol print label -->
        <a href="?module=print_label" id="printSelected" class="btn btn-primary btn-round ml-2">
          <span class="btn-label"><i class="fa fa-print mr-2"></i></span> Print Label
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-inner mt--5">
  <div class="card">
    <div class="card-header">
      <!-- judul tabel -->
      <div class="card-title">Data Barang</div>
      <!-- Filter Jenis -->
      <div class="float-right">
        <form method="GET" action="">
          <input type="hidden" name="module" value="barang">
          <select name="filter_jenis" class="form-control" onchange="this.form.submit()">
            <option value="all">Semua</option>
            <?php
            foreach ($jenisList as $jenis) {
              $selected = (isset($_GET['filter_jenis']) && $_GET['filter_jenis'] == $jenis['id_jenis']) ? 'selected' : '';
              echo "<option value='{$jenis['id_jenis']}' {$selected}>{$jenis['nama_jenis']}</option>";
            }
            ?>
          </select>
        </form>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <!-- tabel untuk menampilkan data dari database -->
        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th width="50" class="text-center">
                <input type="checkbox" id="checkAll" title="Pilih Semua">
              </th>
              <th class="text-center">Barcode</th>
              <th class="text-center">Nama Barang</th>
              <th class="text-center">Area</th>
              <th class="text-center">Stok</th>
              <th class="text-center">Jenis</th>
              <th class="text-center text-nowrap" width="130">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $active_cabang_id = getActiveCabangId();
            $filter_jenis = isset($_GET['filter_jenis']) ? $_GET['filter_jenis'] : 'all';
            
            $where_conditions = [];
            if ($active_cabang_id !== 0) {
              $where_conditions[] = "a.id_cabang = '$active_cabang_id'";
            }
            if ($filter_jenis !== 'all') {
              $filter_jenis_clean = mysqli_real_escape_string($mysqli, $filter_jenis);
              $where_conditions[] = "a.jenis = '$filter_jenis_clean'";
            }

            $sql_query = "SELECT a.*, b.nama_jenis, COALESCE(c.nama_cabang, CONCAT('Cabang #', a.id_cabang)) as nama_cabang 
                         FROM tbl_barang as a 
                         LEFT JOIN tbl_jenis as b ON a.jenis = b.id_jenis
                         LEFT JOIN tbl_cabang as c ON a.id_cabang = c.id_cabang";
            if (!empty($where_conditions)) {
              $sql_query .= " WHERE " . implode(" AND ", $where_conditions);
            }
            $sql_query .= " ORDER BY a.id_barang DESC";
            $query = mysqli_query($mysqli, $sql_query);


            // ambil data hasil query
            while ($data = mysqli_fetch_assoc($query)) { ?>
              <!-- tampilkan data -->
              <tr>
                <td width="30" class="text-center">
                  <input type="checkbox" class="itemCheckbox" name="selected_items[]"
                    value="<?php echo $data['id_barang']; ?>">
                </td>

                <td class="text-center" width="300"> <!-- Memperbesar area untuk barcode -->
                  <div>
                    <img src="generate_barcode.php?id=<?php echo $data['id_barang']; ?>"
                      alt="Barcode untuk <?php echo $data['id_barang']; ?>">
                    <br>
                    <?php echo $data['id_barang']; ?>
                  </div>
                </td>

                <td width="200"><?php echo $data['nama_barang']; ?></td> <!-- Mengurangi lebar kolom nama barang -->
                <td width="90" class="text-center"><?php echo $data['nama_cabang']; ?></td>
                <td width="50" class="text-center"><?php echo $data['stok']; ?></td> <!-- Mengurangi lebar kolom stok -->
                <td width="100" style="text-align: center;"><?php echo $data['nama_jenis']; ?></td>

                <td width="130" class="text-center text-nowrap" style="white-space: nowrap;">
                  <div class="d-inline-flex align-items-center justify-content-center text-nowrap">
                    <!-- tombol detail data -->
                    <a href="?module=tampil_detail_barang&id=<?php echo $data['id_barang']; ?>"
                      class="btn btn-icon btn-round btn-primary btn-sm mr-1" data-toggle="tooltip" data-placement="top"
                      title="Detail">
                      <i class="fas fa-clone fa-sm"></i>
                    </a>
                    <!-- tombol ubah data -->
                    <a href="?module=form_ubah_barang&id=<?php echo $data['id_barang']; ?>"
                      class="btn btn-icon btn-round btn-secondary btn-sm mr-1" data-toggle="tooltip" data-placement="top"
                      title="Ubah">
                      <i class="fas fa-pencil-alt fa-sm"></i>
                    </a>
                    <!-- tombol hapus data -->
                    <a href="javascript:void(0)"
                      onclick="return confirmDelete('modules/barang/proses_hapus.php?id=<?php echo $data['id_barang']; ?>', 'Anda yakin ingin menghapus data barang <?php echo htmlspecialchars($data['nama_barang'], ENT_QUOTES); ?>?')"
                      class="btn btn-icon btn-round btn-danger btn-sm" data-toggle="tooltip" data-placement="top"
                      title="Hapus">
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
