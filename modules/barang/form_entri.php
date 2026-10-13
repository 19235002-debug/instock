<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$id_cabang = $_SESSION['id_cabang'] ?? 1;
$hak_akses = $_SESSION['hak_akses'] ?? '';

if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
} else { 
  // Fetch Cabang Name
  $q_cb = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang = '$id_cabang'");
  $r_cb = mysqli_fetch_assoc($q_cb);
  $nama_cabang_text = $r_cb['nama_cabang'] ?? ('Cabang #' . $id_cabang);
?>
  <div id="pesan"></div>

  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-box-open mr-2"></i> Master Barang</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang" class="text-white">Barang</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Entri</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title font-weight-bold text-dark mb-0">
            <i class="fas fa-plus-circle text-primary mr-2"></i> Form Entri Data Barang Master
          </h5>
          <small class="text-muted">Isi rincian informasi barang inventaris dengan rapi dan akurat.</small>
        </div>
      </div>
      
      <form action="modules/barang/proses_entri.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="card-body py-4">
          <div class="row">
            
            <!-- Left Column: Informasi Utama Barang -->
            <div class="col-lg-7 border-right pr-lg-4">
              <h6 class="font-weight-bold text-primary mb-3 pb-2 border-bottom">
                <i class="fas fa-info-circle mr-2"></i> Informasi Identitas Barang
              </h6>

              <div class="row">
                <!-- ID Barang -->
                <div class="col-md-6">
                  <div class="form-group p-0 mb-3">
                    <?php
                    $query = mysqli_query($mysqli, "SELECT RIGHT(id_barang,4) as nomor FROM tbl_barang ORDER BY id_barang DESC LIMIT 1")
                      or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                    $rows = mysqli_num_rows($query);
                    $nomor_urut = ($rows <> 0) ? ((int)mysqli_fetch_assoc($query)['nomor'] + 1) : 1;
                    $id_barang = "B" . str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);
                    ?>
                    <label class="font-weight-bold text-dark">ID Barang <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg-light"><i class="fas fa-barcode"></i></span>
                      </div>
                      <input type="text" name="id_barang" class="form-control font-weight-bold bg-light" value="<?php echo $id_barang; ?>" readonly>
                    </div>
                  </div>
                </div>

                <!-- Cabang -->
                <div class="col-md-6">
                  <div class="form-group p-0 mb-3">
                    <label class="font-weight-bold text-dark">Cabang / Lokasi <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg-light"><i class="fas fa-building text-primary"></i></span>
                      </div>
                      <input type="hidden" name="id_cabang" value="<?php echo $id_cabang; ?>">
                      <input type="text" class="form-control font-weight-bold bg-light" value="<?php echo htmlspecialchars($nama_cabang_text); ?>" readonly>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Nama Barang -->
              <div class="form-group p-0 mb-3">
                <label class="font-weight-bold text-dark">Nama Barang <span class="text-danger">*</span></label>
                <div id="nama_barang_dropdown">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-box"></i></span>
                    </div>
                    <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Air Freshener Matic Spray 225ml" autocomplete="off" required>
                  </div>
                </div>
                <div class="invalid-feedback">Nama barang tidak boleh kosong.</div>
              </div>

              <div class="row">
                <!-- Jenis Barang -->
                <div class="col-md-6">
                  <div class="form-group p-0 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="font-weight-bold text-dark mb-0">Jenis / Kategori <span class="text-danger">*</span></label>
                      <button type="button" class="btn btn-link btn-sm p-0 font-weight-bold text-success" data-toggle="modal" data-target="#modalQuickJenis">
                        <i class="fas fa-plus-circle mr-1"></i> + Jenis
                      </button>
                    </div>
                    <select id="jenis_barang" name="jenis" class="form-control" autocomplete="off" required>
                      <option selected disabled value="">-- Pilih Jenis --</option>
                      <?php
                      $query_jenis = mysqli_query($mysqli, "SELECT * FROM tbl_jenis ORDER BY nama_jenis ASC")
                        or die('Ada kesalahan pada query tampil data: ' . mysqli_error($mysqli));
                      while ($data_jenis = mysqli_fetch_assoc($query_jenis)) {
                        echo "<option value='$data_jenis[id_jenis]'>$data_jenis[nama_jenis]</option>";
                      }
                      ?>
                    </select>
                    <div class="invalid-feedback">Jenis Barang tidak boleh kosong.</div>
                  </div>
                </div>

                <!-- Satuan Barang -->
                <div class="col-md-6">
                  <div class="form-group p-0 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="font-weight-bold text-dark mb-0">Satuan Barang <span class="text-danger">*</span></label>
                      <button type="button" class="btn btn-link btn-sm p-0 font-weight-bold text-success" data-toggle="modal" data-target="#modalQuickSatuan">
                        <i class="fas fa-plus-circle mr-1"></i> + Satuan
                      </button>
                    </div>
                    <select id="satuan_barang" name="satuan" class="form-control" autocomplete="off" required>
                      <option selected disabled value="">-- Pilih Satuan --</option>
                      <?php
                      $query_satuan = mysqli_query($mysqli, "SELECT * FROM tbl_satuan ORDER BY nama_satuan ASC")
                        or die('Ada kesalahan pada query tampil data: ' . mysqli_error($mysqli));
                      while ($data_satuan = mysqli_fetch_assoc($query_satuan)) {
                        echo "<option value='$data_satuan[id_satuan]'>$data_satuan[nama_satuan]</option>";
                      }
                      ?>
                    </select>
                    <div class="invalid-feedback">Satuan tidak boleh kosong.</div>
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- Stok Minimum -->
                <div class="col-md-6">
                  <div class="form-group p-0 mb-3">
                    <label class="font-weight-bold text-dark">Stok Minimum <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-exclamation-triangle text-warning"></i></span>
                      </div>
                      <input type="text" name="stok_minimum" class="form-control" placeholder="Batas minimum stok" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" required>
                    </div>
                    <div class="invalid-feedback">Stok minimum tidak boleh kosong.</div>
                  </div>
                </div>

                <!-- Nomor Rak -->
                <div class="col-md-6">
                  <div class="form-group p-0 mb-3">
                    <label class="font-weight-bold text-dark">Nomor Rak / Lokasi <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt text-danger"></i></span>
                      </div>
                      <select class="form-control" name="rak_id" required>
                        <option value="">-- Pilih Rak --</option>
                        <?php
                        $target_cabang = $_SESSION['id_cabang'] ?? getActiveCabangId();
                        $occupied_raks = [];
                        if ($target_cabang != 0) {
                          $q_occ = mysqli_query($mysqli, "SELECT rak_id FROM tbl_barang WHERE id_cabang = '$target_cabang' AND rak_id IS NOT NULL AND rak_id != ''");
                          while ($r_occ = mysqli_fetch_assoc($q_occ)) {
                            $occupied_raks[] = $r_occ['rak_id'];
                          }
                        }

                        $query_rak = mysqli_query($mysqli, "SELECT id, no_rak FROM location ORDER BY no_rak ASC");
                        while ($data_rak = mysqli_fetch_assoc($query_rak)) {
                          $is_used = in_array($data_rak['id'], $occupied_raks);
                          if ($is_used) {
                            echo "<option value=\"$data_rak[id]\" disabled>$data_rak[no_rak] (Terpakai di cabang ini)</option>";
                          } else {
                            echo "<option value=\"$data_rak[id]\">$data_rak[no_rak]</option>";
                          }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="invalid-feedback">Nomor Rak tidak boleh kosong.</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column: Media Foto Barang -->
            <div class="col-lg-5 pl-lg-4 mt-4 mt-lg-0">
              <h6 class="font-weight-bold text-primary mb-3 pb-2 border-bottom">
                <i class="fas fa-camera mr-2"></i> Foto & Visual Produk
              </h6>

              <div class="form-group p-0 mb-3">
                <label class="font-weight-bold text-dark mb-2">Unggah Foto Barang</label>
                <div class="custom-file mb-3">
                  <input type="file" id="foto" name="foto" class="custom-file-input" autocomplete="off">
                  <label class="custom-file-label" for="foto">Pilih berkas foto...</label>
                </div>

                <!-- Card Image Preview Box -->
                <div class="card border rounded p-3 text-center bg-light shadow-sm" style="min-height: 220px; display: flex; align-items: center; justify-content: center;">
                  <img style="max-height: 200px; object-fit: contain;" src="images/no_image.png" class="img-fluid foto-preview rounded" alt="Foto Barang">
                </div>

                <div class="alert alert-info py-2 px-3 mt-3 border-0 rounded shadow-sm" style="font-size: 0.85rem;">
                  <i class="fas fa-info-circle mr-1"></i> <strong>Format Foto:</strong> *.jpg / *.png (Maksimal 1 MB).
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="card-footer bg-light py-3 d-flex justify-content-between align-items-center">
          <a href="?module=barang" class="btn btn-secondary btn-round px-4 font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Batal
          </a>
          <button type="submit" name="simpan" class="btn btn-primary btn-round px-4 font-weight-bold shadow-sm">
            <i class="fas fa-save mr-1"></i> Simpan Data Barang
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Quick Add Jenis -->
  <div class="modal fade" id="modalQuickJenis" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white py-2">
          <h5 class="modal-title font-weight-bold text-white" style="font-size:0.95rem;"><i class="fas fa-tags mr-2"></i> Tambah Jenis Barang</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form id="formQuickJenis">
          <div class="modal-body py-3">
            <div class="form-group p-0 m-0">
              <label class="font-weight-bold text-dark">Nama Jenis / Kategori <span class="text-danger">*</span></label>
              <input type="text" id="input_nama_jenis" class="form-control" placeholder="Contoh: Alat Tulis / Elektronik" required autocomplete="off">
            </div>
          </div>
          <div class="modal-footer py-2 bg-light">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary btn-sm font-weight-bold">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Quick Add Satuan -->
  <div class="modal fade" id="modalQuickSatuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white py-2">
          <h5 class="modal-title font-weight-bold text-white" style="font-size:0.95rem;"><i class="fas fa-balance-scale mr-2"></i> Tambah Satuan Barang</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form id="formQuickSatuan">
          <div class="modal-body py-3">
            <div class="form-group p-0 m-0">
              <label class="font-weight-bold text-dark">Nama Satuan <span class="text-danger">*</span></label>
              <input type="text" id="input_nama_satuan" class="form-control" placeholder="Contoh: Pcs / Box / Roll" required autocomplete="off">
            </div>
          </div>
          <div class="modal-footer py-2 bg-light">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary btn-sm font-weight-bold">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      // Custom file input label update
      $('#foto').change(function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih berkas foto...');
        
        var filePath = $('#foto').val();
        var fileSize = $('#foto')[0].files[0] ? $('#foto')[0].files[0].size : 0;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

        if (!allowedExtensions.exec(filePath)) {
          $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Gagal!</span> <span data-notify="message">Tipe file tidak sesuai. Harap unggah file yang memiliki tipe *.jpg atau *.png.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          $('#foto').val('');
          $('.custom-file-label').html('Pilih berkas foto...');
          $('.foto-preview').attr('src', 'images/no_image.png');
          return false;
        } else if (fileSize > 1000000) {
          $('#pesan').html('<div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert"><span data-notify="icon" class="fas fa-times"></span><span data-notify="title" class="text-danger">Gagal!</span> <span data-notify="message">Ukuran file lebih dari 1 Mb. Harap unggah file yang memiliki ukuran maksimal 1 Mb.</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          $('#foto').val('');
          $('.custom-file-label').html('Pilih berkas foto...');
          $('.foto-preview').attr('src', 'images/no_image.png');
          return false;
        } else {
          var fileInput = document.getElementById('foto');
          if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('.foto-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(fileInput.files[0]);
          }
        }
      });

      // Fetch Nama Barang Options if Jenis Selected
      $('#jenis_barang').on('change', function() {
        var selectedJenisID = $(this).val();
        $.ajax({
          type: 'POST',
          url: 'get_nama_barang.php',
          data: { jenis_id: selectedJenisID },
          success: function(response) {
            if(response && response.trim() !== '') {
              $('#nama_barang_dropdown').html(response);
            }
          }
        });
      });

      // AJAX Quick Add Jenis
      $('#formQuickJenis').on('submit', function(e) {
        e.preventDefault();
        var namaJenis = $('#input_nama_jenis').val().trim();
        if(!namaJenis) return;

        $.ajax({
          url: 'modules/jenis/proses_quick_entri.php',
          type: 'POST',
          data: { nama_jenis: namaJenis },
          dataType: 'json',
          success: function(res) {
            if(res.status === 'success' || res.status === 'exists') {
              var newOption = new Option(res.nama_jenis, res.id_jenis, true, true);
              $('#jenis_barang').append(newOption).trigger('change');
              $('#modalQuickJenis').modal('hide');
              $('#input_nama_jenis').val('');
              if(typeof showToast === 'function') {
                showToast('success', 'Sukses!', 'Jenis "' + res.nama_jenis + '" berhasil ditambahkan.');
              }
            } else {
              alert(res.message || 'Gagal menyimpan jenis.');
            }
          },
          error: function() {
            alert('Terjadi kesalahan sistem.');
          }
        });
      });

      // AJAX Quick Add Satuan
      $('#formQuickSatuan').on('submit', function(e) {
        e.preventDefault();
        var namaSatuan = $('#input_nama_satuan').val().trim();
        if(!namaSatuan) return;

        $.ajax({
          url: 'modules/satuan/proses_quick_entri.php',
          type: 'POST',
          data: { nama_satuan: namaSatuan },
          dataType: 'json',
          success: function(res) {
            if(res.status === 'success' || res.status === 'exists') {
              var newOption = new Option(res.nama_satuan, res.id_satuan, true, true);
              $('#satuan_barang').append(newOption).trigger('change');
              $('#modalQuickSatuan').modal('hide');
              $('#input_nama_satuan').val('');
              if(typeof showToast === 'function') {
                showToast('success', 'Sukses!', 'Satuan "' + res.nama_satuan + '" berhasil ditambahkan.');
              }
            } else {
              alert(res.message || 'Gagal menyimpan satuan.');
            }
          },
          error: function() {
            alert('Terjadi kesalahan sistem.');
          }
        });
      });
    });
  </script>
<?php } ?>