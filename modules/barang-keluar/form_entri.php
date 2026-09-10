<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$id_cabang = $_SESSION['id_cabang'];
$hak_akses = $_SESSION['hak_akses'];

if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
} else {
?>

  <div id="pesan"></div>

  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-sign-out-alt mr-2"></i> Barang Keluar</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang_keluar" class="text-white">Barang Keluar</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Entri</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Entri Data Barang Keluar</div>
      </div>
      <form action="modules/barang-keluar/proses_entri.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <div class="row">

            <!-- Kolom Kiri -->
            <div class="col-md-6">

              <?php
              // Membuat "id_transaksi"
              $query = mysqli_query($mysqli, "SELECT RIGHT(id_transaksi,7) as nomor FROM tbl_barang_keluar ORDER BY id_transaksi DESC LIMIT 1")
                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
              $rows = mysqli_num_rows($query);

              if ($rows <> 0) {
                $data = mysqli_fetch_assoc($query);
                $nomor_urut = $data['nomor'] + 1;
              } else {
                $nomor_urut = 1;
              }

              $id_transaksi = "TK-" . str_pad($nomor_urut, 7, "0", STR_PAD_LEFT);
              ?>

              <div class="form-group">
                <label>ID Transaksi <span class="text-danger">*</span></label>
                <input type="text" name="id_transaksi" class="form-control" value="<?php echo $id_transaksi; ?>" readonly>
              </div>

              <div class="form-group">
                <label>ID Cabang <span class="text-danger">*</span></label>
                <select class="form-control" name="id_cabang" required readonly>
                  <option value="<?php echo $id_cabang; ?>"><?php echo $id_cabang; ?></option>
                </select>
              </div>
              <div class="form-group">
                <label>Barang <span class="text-danger">*</span></label>
                <select id="data_barang" name="barang" class="form-control" autocomplete="off" required>
                  <option selected disabled value="">-- Pilih --</option>
                  <?php
                  $active_cabang_id = getActiveCabangId();
                  $cabang_filter = ($active_cabang_id === 0) ? "" : " WHERE id_cabang='$active_cabang_id'";

                  $query_barang = mysqli_query($mysqli, "SELECT id_barang, nama_barang FROM tbl_barang $cabang_filter ORDER BY id_barang ASC")
                    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  while ($data_barang = mysqli_fetch_assoc($query_barang)) {
                    echo "<option value='$data_barang[id_barang]'>$data_barang[id_barang] - $data_barang[nama_barang]</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label>Rak ID <span class="text-danger">*</span></label>
                <input type="text" name="rak_id" id="rak_id_input" class="form-control" readonly>
              </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal <span class="text-danger">*</span></label>
                <input type="text" name="tanggal" class="form-control date-picker" autocomplete="off" value="<?php echo date("d-m-Y"); ?>" required>
              </div>
              <div class="form-group">
                <label>Lokasi STO <span class="text-danger">*</span></label>
                <select name="lokasi_sto" class="form-control" required>
                  <option selected disabled value="">-- Pilih Lokasi STO --</option>
                  <?php
                  $query_lokasi = mysqli_query($mysqli, "SELECT nama_lokasi FROM tbl_lokasi_sto $cabang_filter ORDER BY nama_lokasi ASC");
                  while ($data_lokasi = mysqli_fetch_assoc($query_lokasi)) {
                    echo "<option value='$data_lokasi[nama_lokasi]'>$data_lokasi[nama_lokasi]</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label>Stok <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="text" id="data_stok" name="stok" class="form-control" readonly>
                  <div id="data_satuan" class="input-group-append"></div>
                </div>
              </div>
              <div class="form-group">
                <label>Jumlah Keluar <span class="text-danger">*</span></label>
                <input type="text" id="jumlah" name="jumlah" class="form-control" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" required>
              </div>
              <div class="form-group">
                <label>Sisa Stok <span class="text-danger">*</span></label>
                <input type="text" id="sisa" name="sisa" class="form-control" readonly>
              </div>
            </div>
          </div>
        </div>

        <div class="card-action">
          <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <a href="?module=barang_keluar" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      // Menampilkan data barang dari select box ke textfield
      $('#data_barang').change(function() {
        // mengambil value dari "id_barang"
        var id_barang = $('#data_barang').val();

        $.ajax({
          type: "GET",
          url: "modules/barang-keluar/get_barang.php",
          data: {
            id_cabang: '<?php echo ($_SESSION["hak_akses"] != "administrator" && isset($_SESSION["id_cabang"])) ? $_SESSION["id_cabang"] : ""; ?>',
            id_barang: id_barang // Pastikan Anda tetap memiliki parameter ini
          },
          dataType: "JSON",

          success: function(result) {
            // tampilkan data
            $('#data_stok').val(result.stok);
            $('#data_satuan').html('<span class="input-group-text">' + result.nama_satuan + '</span>');

            // Menampilkan rak_id ke input
            $('#rak_id_input').val(result.rak_id);

            $('#jumlah').focus();
          }
        });
      });

      // menghitung sisa stok
      $('#jumlah').keyup(function() {
        // mengambil data dari form entri
        var stok = $('#data_stok').val();
        var jumlah = $('#jumlah').val();

        // mengecek input data
        // jika data barang belum diisi
        if (stok == "") {
          // tampilkan pesan info
          $('#pesan').html('<div class="alert alert-notify alert-info alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-info"></span><span data-notify="title" class="text-info">Info!</span> <span data-notify="message">Silahkan isi data barang terlebih dahulu.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input "jumlah"
          $('#jumlah').val('');
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika "jumlah" belum diisi
        else if (jumlah == "") {
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika "jumlah" diisi 0
        else if (jumlah == 0) {
          // tampilkan pesan peringatan
          $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-exclamation"></span><span data-notify="title" class="text-warning">Peringatan!</span> <span data-notify="message">Jumlah keluar tidak boleh 0 (nol).</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input "jumlah"
          $('#jumlah').val('');
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika "jumlah" lebih dari "stok"
        else if (eval(jumlah) > eval(stok)) {
          // tampilkan pesan peringatan
          $('#pesan').html('<div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-exclamation"></span><span data-notify="title" class="text-warning">Peringatan!</span> <span data-notify="message">Stok tidak memenuhi, kurangi jumlah keluar.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          // reset input "jumlah"
          $('#jumlah').val('');
          // sisa stok kosong
          var sisa_stok = "";
        }
        // jika "jumlah" sudah diisi
        else {
          // hitung sisa stok
          var sisa_stok = eval(stok) - eval(jumlah);
        }

        // tampilkan sisa stok
        $('#sisa').val(sisa_stok);
      });
    });


    // Fungsi untuk mendeteksi perubahan pada input barcode
    document.getElementById('barcodeInput').addEventListener('change', function() {
      // Ambil nilai dari input barcode
      var barcodeValue = this.value;

      // Pilih dropdown dengan id "data_barang"
      var selectElement = document.getElementById('data_barang');

      // Loop melalui semua pilihan di dropdown
      for (var i = 0; i < selectElement.options.length; i++) {
        if (selectElement.options[i].value.startsWith(barcodeValue)) {
          // Atur pilihan yang sesuai dengan barcode sebagai pilihan yang dipilih
          selectElement.selectedIndex = i;
          break;
        }
      }

      // Reset input barcode untuk pemindaian selanjutnya
      this.value = '';
    });
  </script>
<?php } ?>