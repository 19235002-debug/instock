<?php
$id_cabang = $_SESSION['id_cabang']; // Sesuaikan dengan cara Anda mendapatkan id_cabang yang login

// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di-include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    // alihkan ke halaman error 404
    header('location: 404.html');
}
// jika file di-include oleh file lain, tampilkan isi file
else {
    // menampilkan pesan selamat datang
    // jika pesan tersedia
    if (isset($_GET['pesan'])) {
        // jika pesan = 1
        if ($_GET['pesan'] == 1) {
            // tampilkan pesan selamat datang
            echo '<div class="alert alert-notify alert-secondary alert-dismissible fade show" role="alert">
              <span data-notify="icon" class="fas fa-user-alt"></span> 
              <span data-notify="title" class="text-secondary">Hi! ' . $_SESSION['nama_user'] . '</span> 
              <span data-notify="message">Selamat Datang di Aplikasi Instock.</span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
        }
    }
?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="panel-header bg-secondary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-top flex-column flex-md-row">
                <div class="page-header text-white">
                    <!-- judul halaman -->
                    <h4 class="page-title text-white"><i class="fas fa-home mr-2"></i> Dashboard</h4>
                    <ul class="breadcrumbs">
                        <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
                        <li class="separator"><i class="flaticon-right-arrow"></i></li>
                        <li class="nav-item active"><span>Dashboard</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="page-inner mt--5">
        <div class="row row-card-no-pd mt--2">
            <!-- menampilkan informasi jumlah data barang -->
            <div class="col-sm-12 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big2 text-center">
                                    <i class="flaticon-box-2 text-secondary"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Data Barang</p>
                                    <?php
                                    // Ganti query SQL dengan filter berdasarkan id_cabang yang login
                                    // Cek apakah pengguna adalah Administrator
                                    if ($_SESSION['hak_akses'] == 'Super Admin' || $_SESSION['hak_akses'] == 'Administrator') {
                                        $query_barang = "SELECT * FROM tbl_barang";
                                    } else {
                                        $query_barang = "SELECT * FROM tbl_barang WHERE id_cabang = $id_cabang";
                                    }
                                    $result_barang = mysqli_query($mysqli, $query_barang) or die(mysqli_error($mysqli));
                                    $jumlah_barang = mysqli_num_rows($result_barang);

                                    ?>
                                    <!-- tampilkan data -->
                                    <h4 class="card-title"><?php echo number_format($jumlah_barang, 0, '', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- menampilkan informasi jumlah data barang masuk -->
            <div class="col-sm-12 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big2 text-center">
                                    <i class="flaticon-inbox text-success"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Data Barang Masuk</p>
                                    <?php
                                    // Cek apakah pengguna adalah Administrator
                                    if ($_SESSION['hak_akses'] == 'Super Admin' || $_SESSION['hak_akses'] == 'Administrator') {
                                        $query_barang_masuk = "SELECT * FROM tbl_barang_masuk";
                                    } else {
                                        $query_barang_masuk = "SELECT * FROM tbl_barang_masuk WHERE id_cabang = $id_cabang";
                                    }
                                    $result_barang_masuk = mysqli_query($mysqli, $query_barang_masuk) or die('Ada kesalahan pada query jumlah data barang masuk : ' . mysqli_error($mysqli));
                                    $jumlah_barang_masuk = mysqli_num_rows($result_barang_masuk);
                                    ?>

                                    <!-- tampilkan data -->
                                    <h4 class="card-title"><?php echo number_format($jumlah_barang_masuk, 0, '', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- menampilkan informasi jumlah data barang keluar -->
            <div class="col-sm-12 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big2 text-center">
                                    <i class="flaticon-archive text-warning"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Data Barang Keluar</p>
                                    <?php
                                    // sql statement untuk menampilkan jumlah data pada tabel "tbl_barang_keluar"
                                    // Ganti query SQL dengan filter berdasarkan id_cabang yang login
                                    if ($_SESSION['hak_akses'] == 'Super Admin' || $_SESSION['hak_akses'] == 'Administrator') {
                                        $query_barang_keluar = "SELECT * FROM tbl_barang_keluar";
                                    } else {
                                        $query_barang_keluar = "SELECT * FROM tbl_barang_keluar WHERE id_cabang = $id_cabang";
                                    }
                                    $result_barang_keluar = mysqli_query($mysqli, $query_barang_keluar) or die(mysqli_error($mysqli));
                                    $jumlah_barang_keluar = mysqli_num_rows($result_barang_keluar);

                                    ?>
                                    <!-- tampilkan data -->
                                    <h4 class="card-title"><?php echo number_format($jumlah_barang_keluar, 0, '', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // mengecek hak akses
        // jika hak akses bukan "Admin Gudang" 
        if ($_SESSION['hak_akses'] != 'Admin Gudang') { ?>
            <!-- tampilkan informasi jumlah data jenis barang, satuan, dan user -->
            <div class="row">
                <!-- menampilkan informasi jumlah data jenis barang -->
                <div class="col-sm-12 col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body ">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fas fa-clone"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Data Jenis Barang</p>
                                        <?php
                                        // sql statement untuk menampilkan jumlah data pada tabel "tbl_jenis"
                                        $query = mysqli_query($mysqli, "SELECT * FROM tbl_jenis")
                                            or die('Ada kesalahan pada query jumlah data jenis barang : ' . mysqli_error($mysqli));
                                        // ambil jumlah data dari hasil query
                                        $jumlah_jenis_barang = mysqli_num_rows($query);
                                        ?>
                                        <!-- tampilkan data -->
                                        <h4 class="card-title"><?php echo number_format($jumlah_jenis_barang, 0, '', '.'); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- menampilkan informasi jumlah data satuan -->
                <div class="col-sm-12 col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Data Satuan</p>
                                        <?php
                                        // sql statement untuk menampilkan jumlah data pada tabel "tbl_satuan"
                                        $query = mysqli_query($mysqli, "SELECT * FROM tbl_satuan")
                                            or die('Ada kesalahan pada query jumlah data satuan : ' . mysqli_error($mysqli));
                                        // ambil jumlah data dari hasil query
                                        $jumlah_satuan = mysqli_num_rows($query);
                                        ?>
                                        <!-- tampilkan data -->
                                        <h4 class="card-title"><?php echo number_format($jumlah_satuan, 0, '', '.'); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- menampilkan informasi jumlah data user -->
                <div class="col-sm-12 col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ml-3 ml-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Data User</p>
                                        <?php
                                        // sql statement untuk menampilkan jumlah data pada tabel "tbl_user"
                                        $query = mysqli_query($mysqli, "SELECT * FROM tbl_user")
                                            or die('Ada kesalahan pada query jumlah data user : ' . mysqli_error($mysqli));
                                        // ambil jumlah data dari hasil query
                                        $jumlah_user = mysqli_num_rows($query);
                                        ?>
                                        <!-- tampilkan data -->
                                        <h4 class="card-title"><?php echo number_format($jumlah_user, 0, '', '.'); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mt-1 pb-2">
            <?php } ?>

            <!-- chart -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">Perbandingan Stok Barang per Cabang</h4>
                </div>
                <div class="card-body">
                    <canvas id="stokPerBarangChart" width="400" height="200"></canvas>
                </div>
            </div>

            <script>
                var ctx = document.getElementById('stokPerBarangChart').getContext('2d');
                var datasets = [];

                <?php
                $query_barang = "SELECT DISTINCT nama_barang FROM tbl_barang";
                $result_barang = mysqli_query($mysqli, $query_barang);
                $barangs = mysqli_fetch_all($result_barang, MYSQLI_ASSOC);

                $query_cabang = "SELECT id_cabang, nama_cabang FROM tbl_cabang";
                $result_cabang = mysqli_query($mysqli, $query_cabang);
                $cabangs = mysqli_fetch_all($result_cabang, MYSQLI_ASSOC);

                function generateRandomColorPHP()
                {
                    $letters = '0123456789ABCDEF';
                    $color = '#';
                    for ($i = 0; $i < 6; $i++) {
                        $color .= $letters[rand(0, 15)];
                    }
                    return $color;
                }

                foreach ($cabangs as $cabang) {
                    $id_cabang = $cabang['id_cabang'];
                    $nama_cabang = $cabang['nama_cabang'];

                    $dataForCabang = [];
                    foreach ($barangs as $barang) {
                        $nama_barang = $barang['nama_barang'];
                        $query_stok = "SELECT SUM(stok) AS total_stok FROM tbl_barang WHERE nama_barang = '$nama_barang' AND id_cabang = $id_cabang";
                        $result_stok = mysqli_query($mysqli, $query_stok);
                        $row_stok = mysqli_fetch_assoc($result_stok);
                        $dataForCabang[] = $row_stok['total_stok'] ?? 0;
                    }

                    $color = generateRandomColorPHP();
                    echo "datasets.push({
            label: '$nama_cabang',
            backgroundColor: '$color',
            borderColor: '$color',
            data: " . json_encode($dataForCabang) . ",
            fill: false
        });\n";
                }
                ?>

                var stokPerBarangChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_column($barangs, 'nama_barang')); ?>,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'top',
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            </script>

            <!-- menampilkan informasi stok barang yang telah mencapai batas minimum -->
            <!-- chart -->
            <div class="card mt-4">
                <div class="card-header">
                    <!-- judul tabel -->
                    <div class="card-title"><i class="fas fa-info-circle mr-2"></i> Stok barang telah mencapai batas minimum</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- tabel untuk menampilkan data dari database -->
                        <table id="basic-datatables" class="display table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">ID Barang</th>
                                    <th class="text-center">Nama Barang</th>
                                    <th class="text-center">Jenis Barang</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // variabel untuk nomor urut tabel
                                $no = 1;
                                // sql statement untuk menampilkan data dari tabel "tbl_barang", tabel "tbl_jenis", dan tabel "tbl_satuan" berdasarkan "stok"
                                $query = "";
                                if ($_SESSION['hak_akses'] == 'Super Admin' || $_SESSION['hak_akses'] == 'Administrator') {
                                    // Jika Administrator, tampilkan semua data
                                    $query = "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, b.nama_jenis, c.nama_satuan, d.nama_cabang
                          FROM tbl_barang as a 
                          INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                          INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan
                          INNER JOIN tbl_cabang as d ON a.id_cabang=d.id_cabang
                          WHERE a.stok<=a.stok_minimum 
                          ORDER BY a.id_barang ASC";
                                } else {
                                    // Jika bukan Administrator, tampilkan data berdasarkan id_cabang yang login
                                    $id_cabang_logged_in = $_SESSION['id_cabang']; // Anda harus menyesuaikan ini sesuai dengan nama session yang Anda gunakan
                                    $query = "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, b.nama_jenis, c.nama_satuan, d.nama_cabang
                          FROM tbl_barang as a 
                          INNER JOIN tbl_jenis as b ON a.jenis=b.id_jenis 
                          INNER JOIN tbl_satuan as c ON a.satuan=c.id_satuan
                          INNER JOIN tbl_cabang as d ON a.id_cabang=d.id_cabang
                          WHERE a.stok<=a.stok_minimum AND a.id_cabang='$id_cabang_logged_in'
                          ORDER BY a.id_barang ASC";
                                }

                                $result = mysqli_query($mysqli, $query) or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                                // ambil data hasil query
                                while ($data = mysqli_fetch_assoc($result)) {
                                ?>
                                    <!-- tampilkan data -->
                                    <tr>
                                        <td width="50" class="text-center"><?php echo $no++; ?></td>
                                        <td width="80" class="text-center"><?php echo $data['id_barang']; ?></td>
                                        <td width="200"><?php echo $data['nama_barang']; ?></td>
                                        <td width="150"><?php echo $data['nama_jenis']; ?></td>
                                        <td class="text-center"><?php echo $data['nama_cabang']; ?></td>
                                        <td width="70" class="text-right"><span class="badge badge-warning"><?php echo $data['stok']; ?></span></td>
                                        <td width="70"><?php echo $data['nama_satuan']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        <?php } ?>