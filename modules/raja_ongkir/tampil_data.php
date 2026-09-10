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

// Fungsi untuk mengambil pilihan cabang
function getCabangOptions($mysqli)
{
    $options = "";
    $query_cabang = mysqli_query($mysqli, "SELECT * FROM tbl_cabang ORDER BY nama_cabang ASC");
    while ($data_cabang = mysqli_fetch_assoc($query_cabang)) {
        $options .= "<option value='" . $data_cabang['id_cabang'] . "'>" . htmlspecialchars($data_cabang['nama_cabang']) . "</option>";
    }
    return $options;
}

$cabangOptions = getCabangOptions($mysqli);
?>

<style>
    .card-modern {
        border-radius: 12px !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
        transition: all 0.3s ease;
    }
    .section-title-box {
        background: rgba(104, 97, 206, 0.06);
        border-left: 4px solid #6861ce;
        padding: 10px 15px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 20px;
    }
    .input-group-text {
        border-radius: 8px 0 0 8px !important;
        background-color: #f8f9fa !important;
        border-color: #cbd5e1 !important;
        color: #475569 !important;
    }
    .input-group select.form-control {
        border-radius: 0 8px 8px 0 !important;
    }
</style>

<!-- Header Halaman -->
<div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-3">
        <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
            <div class="page-header text-white">
                <h4 class="page-title text-white"><i class="fas fa-calculator mr-2"></i> Cek Ongkir</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                    <li class="separator"><i class="flaticon-right-arrow"></i></li>
                    <li class="nav-item"><a href="?module=raja_ongkir" class="text-white">Hitung Ongkir</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="row">
        <!-- Form Hitung Ongkir -->
        <div class="col-md-12">
            <div class="card card-modern">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title text-secondary font-weight-bold mb-1">
                                <i class="fas fa-shipping-fast mr-2 text-secondary"></i> Form Cek Tarif Expedisi
                            </h3>
                            <p class="text-muted small mb-0">Cek dan bandingkan tarif pengiriman antar cabang ke seluruh Indonesia</p>
                        </div>
                        <span class="badge badge-secondary p-2"><i class="fas fa-database mr-1"></i> Data RajaOngkir</span>
                    </div>
                    <hr class="mb-0 mt-3">
                </div>

                <form id="formOngkir" action="rajaongkir_request.php" method="post" class="needs-validation" novalidate>
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Kolom Kiri: Asal -->
                            <div class="col-md-6 mb-3">
                                <div class="section-title-box">
                                    <h5 class="text-secondary font-weight-bold mb-0">
                                        <i class="fas fa-paper-plane mr-2 text-secondary"></i> LOKASI ASAL
                                    </h5>
                                </div>

                                <div class="form-group px-0">
                                    <label for="id_cabang_asal" class="font-weight-bold">Cabang Asal <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text text-secondary"><i class="fas fa-store"></i></span>
                                        </div>
                                        <select name="id_cabang_asal" id="id_cabang_asal" class="form-control" required>
                                            <option value="">-- Pilih Cabang Asal --</option>
                                            <?php echo $cabangOptions; ?>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">Silakan pilih cabang asal.</div>
                                </div>

                                <div class="form-group px-0">
                                    <label for="alamatAsal" class="font-weight-bold">Kota / Kabupaten Asal <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text text-secondary"><i class="fas fa-map-marker-alt"></i></span>
                                        </div>
                                        <select id="alamatAsal" name="alamatAsal" class="form-control" required>
                                            <option value="">-- Cari / Pilih Kota Asal --</option>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">Pilih kota asal pengiriman.</div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Tujuan -->
                            <div class="col-md-6 mb-3">
                                <div class="section-title-box">
                                    <h5 class="text-secondary font-weight-bold mb-0">
                                        <i class="fas fa-map-marked-alt mr-2 text-secondary"></i> LOKASI TUJUAN
                                    </h5>
                                </div>

                                <div class="form-group px-0">
                                    <label for="id_cabang_tujuan" class="font-weight-bold">Cabang Tujuan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text text-secondary"><i class="fas fa-building"></i></span>
                                        </div>
                                        <select name="id_cabang_tujuan" id="id_cabang_tujuan" class="form-control" required>
                                            <option value="">-- Pilih Cabang Tujuan --</option>
                                            <?php echo $cabangOptions; ?>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">Silakan pilih cabang tujuan.</div>
                                </div>

                                <div class="form-group px-0">
                                    <label for="alamatTujuan" class="font-weight-bold">Kota / Kabupaten Tujuan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text text-secondary"><i class="fas fa-map-marked-alt"></i></span>
                                        </div>
                                        <select id="alamatTujuan" name="alamatTujuan" class="form-control" required>
                                            <option value="">-- Cari / Pilih Kota Tujuan --</option>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">Pilih kota tujuan pengiriman.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris Kurir & Berat -->
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="section-title-box">
                                    <h5 class="text-secondary font-weight-bold mb-0">
                                        <i class="fas fa-box-open mr-2 text-secondary"></i> Rincian Paket & Ekspedisi
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group px-0">
                                    <label for="pilihanKurir" class="font-weight-bold">Pilihan Ekspedisi Kurir <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text text-secondary"><i class="fas fa-truck"></i></span>
                                        </div>
                                        <select id="pilihanKurir" name="pilihanKurir" class="form-control" required>
                                            <option value="">-- Pilih Kurir --</option>
                                            <option value="jne">JNE Express</option>
                                            <option value="tiki">TIKI (Titipan Kilat)</option>
                                            <option value="pos">POS Indonesia</option>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">Pilih ekspedisi pengiriman.</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group px-0">
                                    <label for="berat" class="font-weight-bold">Berat Paket (Kilogram) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text text-secondary"><i class="fas fa-weight-hanging"></i></span>
                                        </div>
                                        <input type="number" step="0.1" min="0.1" id="berat" name="berat" class="form-control" placeholder="Contoh: 1.5" style="height: 42px;" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">Kg</span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Masukkan berat paket dalam Kg.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-action bg-light border-top p-4 d-flex justify-content-end">
                        <button type="submit" id="btnHitung" class="btn btn-secondary btn-round px-4 py-2 font-weight-bold">
                            <i class="fas fa-calculator mr-2"></i> Hitung Ongkos Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Wadah Hasil Perhitungan Ongkir -->
        <div class="col-md-12" id="resultWrapper" style="display: none;">
            <div class="card card-modern">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4">
                    <div class="card-title font-weight-bold text-success">
                        <i class="fas fa-receipt mr-2 text-success"></i> Hasil Perhitungan Tarif Ongkir
                    </div>
                    <button type="button" class="btn btn-sm btn-light text-muted btn-round" onclick="$('#resultWrapper').slideUp();">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
                </div>
                <div class="card-body p-0" id="resultContent">
                    <!-- Konten hasil AJAX dimuat di sini -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var cachedCities = null;

    function fetchCities(callback) {
        if (cachedCities) {
            callback(cachedCities);
            return;
        }
        fetch('get_cities.php')
            .then(response => response.json())
            .then(cities => {
                cachedCities = cities || [];
                callback(cachedCities);
            })
            .catch(error => {
                console.error('Error fetching cities:', error);
                callback([]);
            });
    }

    function populateCityDropdown(selectId, defaultCabangText) {
        var selectEl = document.getElementById(selectId);
        if (!selectEl) return;

        fetchCities(function(cities) {
            var currentValue = selectEl.value;
            selectEl.innerHTML = '<option value="">-- Cari atau Pilih Kota / Kabupaten --</option>';

            var matchedId = null;
            var cleanCabang = defaultCabangText ? defaultCabangText.split('-')[0].trim().toLowerCase() : '';

            cities.forEach(function(city) {
                var option = document.createElement('option');
                option.value = city.city_id;
                var displayName = (city.type ? city.type + ' ' : '') + city.city_name + ' (' + (city.province || '') + ')';
                option.text = displayName;
                selectEl.appendChild(option);

                if (cleanCabang && !matchedId) {
                    var cityNameLower = city.city_name.toLowerCase();
                    if (cleanCabang.includes(cityNameLower) || cityNameLower.includes(cleanCabang)) {
                        matchedId = city.city_id;
                    }
                }
            });

            if (currentValue && selectEl.querySelector('option[value="' + currentValue + '"]')) {
                selectEl.value = currentValue;
            } else if (matchedId) {
                selectEl.value = matchedId;
            }
        });
    }

    // Memperbarui dropdown Alamat Asal berdasarkan pilihan Cabang Asal
    function updateAlamatAsal() {
        var selectCabang = document.getElementById('id_cabang_asal');
        var namaCabang = (selectCabang && selectCabang.selectedIndex > 0) ? selectCabang.options[selectCabang.selectedIndex].text : '';
        populateCityDropdown('alamatAsal', namaCabang);
    }

    // Memperbarui dropdown Alamat Tujuan berdasarkan pilihan Cabang Tujuan
    function updateAlamatTujuan() {
        var selectCabang = document.getElementById('id_cabang_tujuan');
        var namaCabang = (selectCabang && selectCabang.selectedIndex > 0) ? selectCabang.options[selectCabang.selectedIndex].text : '';
        populateCityDropdown('alamatTujuan', namaCabang);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Trigger update kota saat cabang berubah
        $('#id_cabang_asal').on('change', function() {
            updateAlamatAsal();
        });
        $('#id_cabang_tujuan').on('change', function() {
            updateAlamatTujuan();
        });

        // Muat data kota awal
        updateAlamatAsal();
        updateAlamatTujuan();

        // Submisi form via AJAX
        $('#formOngkir').on('submit', function(event) {
            event.preventDefault();

            var alamatAsal = $('#alamatAsal').val();
            var alamatTujuan = $('#alamatTujuan').val();
            var beratKg = parseFloat($('#berat').val());
            var pilihanKurir = $('#pilihanKurir').val();

            if (!alamatAsal || !alamatTujuan || isNaN(beratKg) || beratKg <= 0 || !pilihanKurir) {
                if (typeof showToast === 'function') {
                    showToast('warning', 'Form Belum Lengkap', 'Silakan lengkapi seluruh field lokasi, kurir, dan berat paket.');
                }
                return;
            }

            var beratGram = Math.round(beratKg * 1000);
            var $btn = $('#btnHitung');
            var originalBtnHtml = $btn.html();

            // Ubah state tombol menjadi loading
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menghitung Tarif...');

            $.ajax({
                type: 'POST',
                url: 'rajaongkir_request.php',
                data: {
                    alamatAsal: alamatAsal,
                    alamatTujuan: alamatTujuan,
                    berat: beratGram,
                    pilihanKurir: pilihanKurir
                },
                success: function(response) {
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    $('#resultContent').html(response);
                    $('#resultWrapper').slideDown(400, function() {
                        document.getElementById('resultWrapper').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    });

                    if (typeof showToast === 'function') {
                        showToast('success', 'Perhitungan Selesai', 'Opsi tarif pengiriman berhasil dimuat.');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html(originalBtnHtml);
                    if (typeof showToast === 'function') {
                        showToast('error', 'Gagal!', 'Gagal menghubungkan ke layanan RajaOngkir.');
                    }
                }
            });
        });
    });
</script>