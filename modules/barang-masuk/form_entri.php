<?php
$id_cabang = $_SESSION['id_cabang'];
$hak_akses = $_SESSION['hak_akses'];

if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
} else {
  // Generate ID Transaksi
  $query = mysqli_query($mysqli, "SELECT RIGHT(id_transaksi,7) as nomor FROM tbl_barang_masuk ORDER BY id_transaksi DESC LIMIT 1")
    or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
  $rows = mysqli_num_rows($query);
  if ($rows <> 0) {
    $data = mysqli_fetch_assoc($query);
    $nomor_urut = (int)$data['nomor'] + 1;
  } else {
    $nomor_urut = 1;
  }
  $id_transaksi = "TM-" . str_pad($nomor_urut, 7, "0", STR_PAD_LEFT);
  $auto_no_sj   = "SJ-" . date("Ym") . "-" . str_pad($nomor_urut, 4, "0", STR_PAD_LEFT);

  // Ambil daftar barang sesuai cabang aktif
  $active_cabang_id = getActiveCabangId();
  $barang_options = [];
  if ($active_cabang_id === 0) {
    $q_b = mysqli_query($mysqli, "SELECT b.id_barang, b.nama_barang, b.stok, b.satuan, s.nama_satuan, b.rak_id, r.no_rak 
                                   FROM tbl_barang b 
                                   LEFT JOIN tbl_satuan s ON b.satuan = s.id_satuan 
                                   LEFT JOIN location r ON b.rak_id = r.id 
                                   ORDER BY b.nama_barang ASC");
  } else {
    $q_b = mysqli_query($mysqli, "SELECT b.id_barang, b.nama_barang, b.stok, b.satuan, s.nama_satuan, b.rak_id, r.no_rak 
                                   FROM tbl_barang b 
                                   LEFT JOIN tbl_satuan s ON b.satuan = s.id_satuan 
                                   LEFT JOIN location r ON b.rak_id = r.id 
                                   WHERE b.id_cabang = '$active_cabang_id' 
                                   ORDER BY b.nama_barang ASC");
  }
  while ($rb = mysqli_fetch_assoc($q_b)) {
    $barang_options[] = $rb;
  }

  // Ambil daftar satuan
  $satuan_options = [];
  $q_s = mysqli_query($mysqli, "SELECT * FROM tbl_satuan ORDER BY nama_satuan ASC");
  while ($rs = mysqli_fetch_assoc($q_s)) {
    $satuan_options[] = $rs;
  }

  // Ambil daftar rak
  $rak_options = [];
  $q_r = mysqli_query($mysqli, "SELECT * FROM location WHERE status = 'active' ORDER BY no_rak ASC");
  while ($rr = mysqli_fetch_assoc($q_r)) {
    $rak_options[] = $rr;
  }

  // Ambil daftar cabang
  $cabang_options = [];
  $q_c = mysqli_query($mysqli, "SELECT * FROM tbl_cabang ORDER BY nama_cabang ASC");
  while ($rc = mysqli_fetch_assoc($q_c)) {
    $cabang_options[] = $rc;
  }

  // Ambil daftar supplier dari tbl_user (hak_akses = Supplier)
  $supplier_options = [];
  $q_sp = mysqli_query($mysqli, "SELECT id_user, nama_user, username FROM tbl_user WHERE hak_akses = 'Supplier' ORDER BY nama_user ASC");
  while ($rsp = mysqli_fetch_assoc($q_sp)) {
    $supplier_options[] = $rsp;
  }
?>
  <div id="pesan"></div>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
      <div class="page-header text-white">
        <h4 class="page-title text-white"><i class="fas fa-sign-in-alt mr-2"></i> Barang Masuk</h4>
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang_masuk" class="text-white">Barang Masuk</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Entri</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <form action="modules/barang-masuk/proses_entri.php" method="post" id="formBarangMasuk" class="needs-validation" novalidate>
      
      <!-- Step Progress Indicator Banner -->
      <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-3 bg-white">
          <div class="row align-items-center text-center text-md-left">
            <div class="col-md-4 mb-3 mb-md-0 border-right-md">
              <div class="d-flex align-items-center justify-content-center justify-content-md-start px-2 py-1">
                <div class="avatar avatar-sm mr-3">
                  <span class="avatar-title rounded-circle bg-primary-gradient text-white font-weight-bold shadow-sm" style="width: 38px; height: 38px; line-height: 38px; font-size: 1rem;">1</span>
                </div>
                <div>
                  <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 0.92rem;">Informasi Pengiriman</h6>
                  <small class="text-muted d-block" style="font-size: 0.78rem;">No. Surat Jalan & Supplier</small>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0 border-right-md">
              <div class="d-flex align-items-center justify-content-center justify-content-md-start px-2 py-1">
                <div class="avatar avatar-sm mr-3">
                  <span class="avatar-title rounded-circle bg-info-gradient text-white font-weight-bold shadow-sm" style="width: 38px; height: 38px; line-height: 38px; font-size: 1rem;">2</span>
                </div>
                <div>
                  <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 0.92rem;">Item Barang & Batch</h6>
                  <small class="text-muted d-block" style="font-size: 0.78rem;">Pilih Item, Qty, & Expired</small>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="d-flex align-items-center justify-content-center justify-content-md-start px-2 py-1">
                <div class="avatar avatar-sm mr-3">
                  <span class="avatar-title rounded-circle bg-success-gradient text-white font-weight-bold shadow-sm" style="width: 38px; height: 38px; line-height: 38px; font-size: 1rem;">3</span>
                </div>
                <div>
                  <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 0.92rem;">Simpan & Verifikasi</h6>
                  <small class="text-muted d-block" style="font-size: 0.78rem;">Auto Status <?= ($hak_akses == 'Super Admin' || $hak_akses == 'Administrator' || $hak_akses == 'Kepala Gudang') ? 'Approved' : 'Pending' ?></small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Information Header Surat Jalan -->
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <div class="card-title text-white font-weight-bold">
            <i class="fas fa-file-invoice mr-2"></i> Dokumen Surat Jalan & Informasi Pengiriman
          </div>
          <span class="badge badge-light text-primary font-weight-bold px-3 py-2" style="font-size: 0.95rem;">
            <?= htmlspecialchars($id_transaksi) ?>
          </span>
        </div>
        <div class="card-body">
          <input type="hidden" name="id_transaksi" value="<?= htmlspecialchars($id_transaksi) ?>">

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label class="font-weight-bold">No. Surat Jalan <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                  </div>
                  <input type="text" name="no_surat_jalan" class="form-control font-weight-bold" value="<?= htmlspecialchars($auto_no_sj) ?>" required autocomplete="off">
                </div>
                <div class="invalid-feedback">No Surat Jalan wajib diisi.</div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label class="font-weight-bold">Supplier / Vendor <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-truck text-primary"></i></span>
                  </div>
                  <select name="supplier" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Supplier / Vendor --</option>
                    <?php if (!empty($supplier_options)): ?>
                      <?php foreach ($supplier_options as $sp): ?>
                        <option value="<?= htmlspecialchars($sp['nama_user']) ?>">
                          <?= htmlspecialchars($sp['nama_user']) ?> (<?= htmlspecialchars($sp['username']) ?>)
                        </option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="PT. Supplier Utama">PT. Supplier Utama</option>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="invalid-feedback">Supplier wajib dipilih.</div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label class="font-weight-bold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                  </div>
                  <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label class="font-weight-bold">Cabang Tujuan <span class="text-danger">*</span></label>
                <?php 
                $user_cabang_id = (getActiveCabangId() > 0) ? getActiveCabangId() : ($_SESSION['id_cabang'] ?? 1);
                if ($hak_akses == 'Super Admin' || $hak_akses == 'Administrator'): 
                ?>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-building text-primary"></i></span>
                    </div>
                    <select name="id_cabang" class="form-control font-weight-bold" required>
                      <option value="" disabled>-- Pilih Cabang --</option>
                      <?php foreach ($cabang_options as $cb): ?>
                        <option value="<?= $cb['id_cabang'] ?>" <?= ($cb['id_cabang'] == $user_cabang_id) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($cb['nama_cabang']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                <?php else: ?>
                  <input type="hidden" name="id_cabang" value="<?= $user_cabang_id ?>">
                  <?php 
                  $q_cb = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang = '$user_cabang_id'");
                  $r_cb = mysqli_fetch_assoc($q_cb);
                  $nama_cabang_locked = $r_cb['nama_cabang'] ?? ('Cabang #' . $user_cabang_id);
                  ?>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-light"><i class="fas fa-building text-muted"></i></span>
                    </div>
                    <input type="text" class="form-control bg-light font-weight-bold" value="<?= htmlspecialchars($nama_cabang_locked) ?>" readonly required>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group pt-0">
                <label class="font-weight-bold">Catatan / Keterangan Penerimaan</label>
                <input type="text" name="catatan" class="form-control" placeholder="Catatan tambahan (opsional), misal: No. Kontainer / Kondisi Segel Good" autocomplete="off">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Table Dynamic Items -->
      <div class="card shadow-sm mt-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
          <div>
            <h5 class="card-title text-dark font-weight-bold mb-0">
              <i class="fas fa-boxes text-warning mr-2"></i> Daftar Barang Dalam Pengiriman (Batching)
            </h5>
            <small class="text-muted">Masukkan satu atau lebih item barang yang dikirim dalam Surat Jalan ini.</small>
          </div>
          <button type="button" class="btn btn-success btn-round btn-sm px-3" id="btnAddRow">
            <i class="fas fa-plus mr-1"></i> Tambah Item Barang
          </button>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-0" id="tableItems">
              <thead class="bg-light">
                <tr class="text-center">
                  <th style="width: 4%;">#</th>
                  <th style="width: 25%;">Barang <span class="text-danger">*</span></th>
                  <th style="width: 12%;">Satuan</th>
                  <th style="width: 10%;">Stok Saat Ini</th>
                  <th style="width: 12%;">Jumlah Masuk <span class="text-danger">*</span></th>
                  <th style="width: 14%;">No. Batch / Lot</th>
                  <th style="width: 13%;">Exp Date</th>
                  <th style="width: 10%;">Aksi</th>
                </tr>
              </thead>
              <tbody id="itemRowsContainer">
                <!-- Dynamic Rows will be inserted here -->
              </tbody>
            </table>
          </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-between align-items-center py-3">
          <div>
            <span class="font-weight-bold text-dark">Total Item: <span id="lblTotalItem" class="badge badge-primary">0</span></span>
            <span class="font-weight-bold text-dark ml-3">Total Qty: <span id="lblTotalQty" class="badge badge-success">0</span></span>
          </div>
          <div>
            <a href="?module=barang_masuk" class="btn btn-secondary px-4 mr-2">
              <i class="fas fa-arrow-left mr-1"></i> Batal
            </a>
            <button type="submit" name="simpan" class="btn btn-primary px-4 font-weight-bold">
              <i class="fas fa-save mr-1"></i> Simpan Transaksi Masuk
            </button>
          </div>
        </div>
      </div>

    </form>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const container = document.getElementById("itemRowsContainer");
      const btnAdd = document.getElementById("btnAddRow");
      const lblTotalItem = document.getElementById("lblTotalItem");
      const lblTotalQty = document.getElementById("lblTotalQty");

      // Master data in JSON for JavaScript row creation
      const barangList = <?= json_encode($barang_options) ?>;
      const satuanList = <?= json_encode($satuan_options) ?>;

      let rowCount = 0;

      function createRow() {
        rowCount++;
        const tr = document.createElement("tr");
        tr.id = "row_" + rowCount;
        tr.className = "item-row";

        let optionsBarangHtml = '<option value="" disabled selected>-- Pilih Barang --</option>';
        barangList.forEach(b => {
          optionsBarangHtml += `<option value="${b.id_barang}" data-stok="${b.stok}" data-satuan="${b.satuan}" data-satuan-name="${b.nama_satuan || ''}">${b.id_barang} - ${b.nama_barang}</option>`;
        });

        let optionsSatuanHtml = '<option value="" disabled selected>-- Satuan --</option>';
        satuanList.forEach(s => {
          optionsSatuanHtml += `<option value="${s.id_satuan}">${s.nama_satuan}</option>`;
        });

        tr.innerHTML = `
          <td class="text-center align-middle row-number font-weight-bold">${container.children.length + 1}</td>
          <td>
            <select name="barang[]" class="form-control select-barang" required>
              ${optionsBarangHtml}
            </select>
            <div class="invalid-feedback">Silakan pilih barang.</div>
          </td>
          <td class="align-middle">
            <select name="satuan[]" class="form-control select-satuan" required>
              ${optionsSatuanHtml}
            </select>
          </td>
          <td class="align-middle text-center">
            <span class="badge badge-info txt-stok" style="font-size: 0.9rem;">0</span>
          </td>
          <td>
            <input type="number" name="jumlah[]" class="form-control input-jumlah" min="1" placeholder="Qty" required autocomplete="off">
            <div class="invalid-feedback">Masukkan Qty valid.</div>
          </td>
          <td>
            <input type="text" name="no_batch[]" class="form-control input-batch" placeholder="e.g. BATCH-A01" autocomplete="off">
          </td>
          <td>
            <input type="date" name="expired_date[]" class="form-control input-exp">
          </td>
          <td class="text-center align-middle">
            <button type="button" class="btn btn-danger btn-sm btn-del-row" title="Hapus Baris">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        `;

        container.appendChild(tr);
        bindRowEvents(tr);
        updateTotals();
      }

      function bindRowEvents(row) {
        const selectBarang = row.querySelector(".select-barang");
        const selectSatuan = row.querySelector(".select-satuan");
        const txtStok = row.querySelector(".txt-stok");
        const inputJumlah = row.querySelector(".input-jumlah");
        const btnDel = row.querySelector(".btn-del-row");

        selectBarang.addEventListener("change", function () {
          const selectedOption = this.options[this.selectedIndex];
          const stok = selectedOption.getAttribute("data-stok") || 0;
          const satuanId = selectedOption.getAttribute("data-satuan") || "";

          txtStok.textContent = stok;
          if (satuanId) {
            selectSatuan.value = satuanId;
          }
        });

        inputJumlah.addEventListener("input", function () {
          updateTotals();
        });

        btnDel.addEventListener("click", function () {
          if (container.children.length > 1) {
            row.remove();
            reindexRows();
            updateTotals();
          } else {
            Swal.fire({
              icon: 'warning',
              title: 'Minimal 1 Item',
              text: 'Setidaknya harus ada 1 item barang dalam Surat Jalan penerimaan.',
              confirmButtonColor: '#3085d6'
            });
          }
        });
      }

      function reindexRows() {
        const rows = container.querySelectorAll(".item-row");
        rows.forEach((r, idx) => {
          r.querySelector(".row-number").textContent = idx + 1;
        });
      }

      function updateTotals() {
        const rows = container.querySelectorAll(".item-row");
        let totalQty = 0;
        rows.forEach(r => {
          const qtyVal = parseInt(r.querySelector(".input-jumlah").value) || 0;
          totalQty += qtyVal;
        });
        lblTotalItem.textContent = rows.length;
        lblTotalQty.textContent = totalQty;
      }

      // Add initial row
      createRow();

      btnAdd.addEventListener("click", function () {
        createRow();
      });

      // Bootstrap validation trigger
      const form = document.getElementById("formBarangMasuk");
      form.addEventListener("submit", function (e) {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add("was-validated");
      });
    });
  </script>
<?php
}
?>