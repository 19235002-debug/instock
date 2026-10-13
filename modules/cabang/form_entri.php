<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
    exit;
} else { ?>
    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-4">
            <div class="page-header text-white">
                <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Cabang</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=cabang" class="text-white">Cabang</a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a>Entri</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Entri Data Cabang</div>
            </div>
            <form action="modules/cabang/proses_entri_cabang.php" method="post" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label>Kode Lokasi Area <span class="text-danger">*</span></label>
                        <input type="text" name="kode_cabang" class="form-control col-lg-3 text-uppercase" placeholder="Contoh: JKT, BDG, SBY" autocomplete="off" required>
                        <small class="form-text text-muted">Kode lokasi ini nantinya menjadi awalan (prefix) penomoran rak.</small>
                        <div class="invalid-feedback">Kode Lokasi Area tidak boleh kosong.</div>
                    </div>

                    <div class="form-group">
                        <label>Nama Lokasi Area / Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_cabang" class="form-control col-lg-5" autocomplete="off" required>
                        <div class="invalid-feedback">Cabang tidak boleh kosong.</div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control col-lg-6" rows="3" autocomplete="off" placeholder="Jl. Contoh No. 123, Kota"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Nama PIC/Owner</label>
                        <input type="text" name="nama_fm" class="form-control col-lg-5" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" class="form-control col-lg-5" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Email Cabang</label>
                        <input type="email" name="email" class="form-control col-lg-5" autocomplete="off" placeholder="cabang@domain.com">
                    </div>
                </div>
                <div class="card-action">
                    <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
                    <a href="?module=cabang" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
<?php } ?>