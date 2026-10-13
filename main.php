<?php
session_start(); // Mengaktifkan session

// Pengecekan session login user
// Jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // Alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: login.php?pesan=2');
    exit;
} else {
    require_once "config/database.php";
    require_once "helper/setting_helper.php";
    require_once "helper/cabang_helper.php";
    $sys_setting = getSystemSetting($mysqli);

    // Ambil foto profil user dari session atau database
    $user_id_sess = (int) ($_SESSION['id_user'] ?? 0);
    $user_foto_db = $_SESSION['foto'] ?? '';
    if (empty($user_foto_db) && $user_id_sess > 0) {
        $q_f = mysqli_query($mysqli, "SELECT foto FROM tbl_user WHERE id_user = $user_id_sess");
        if ($q_f && $r_f = mysqli_fetch_assoc($q_f)) {
            $user_foto_db = $r_f['foto'] ?? '';
            $_SESSION['foto'] = $user_foto_db;
        }
    }
    $user_avatar_path = (!empty($user_foto_db) && file_exists("assets/img/user/" . $user_foto_db))
        ? "assets/img/user/" . htmlspecialchars($user_foto_db)
        : "assets/img/avatar-2.png";
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="<?= htmlspecialchars($sys_setting['slogan_aplikasi']) ?>" />
        <meta name="author" content="Guntur" />

        <!-- Title -->
        <title><?= htmlspecialchars($sys_setting['nama_aplikasi']) ?> | Fulfillment Warehouse</title>
        <link rel="icon" href="<?= getLogoAppPath($sys_setting, 'favicon') ?>" type="image/x-icon" />

        <!-- Fonts and icons -->
        <script src="assets/js/plugin/webfont/webfont.min.js"></script>
        <script>
            WebFont.load({
                google: {
                    "families": ["Lato:300,400,700,900"]
                },
                custom: {
                    "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                    urls: ['assets/css/fonts.min.css']
                },
                active: function () {
                    sessionStorage.fonts = true;
                }
            });
        </script>

        <!-- CSS Files -->
        <link rel="stylesheet" href="assets/css/fonts.min.css">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/atlantis.min.css">

        <!-- Custom Style CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/global-custom-theme.css">

        <!-- Datepicker CSS -->
        <link rel="stylesheet" href="assets/js/plugin/datepicker/css/bootstrap-datepicker.css">
        <!-- Chosen CSS -->
        <link rel="stylesheet" href="assets/js/plugin/chosen/chosen.css">

        <!-- Core JS Files -->
        <script src="assets/js/core/jquery.3.2.1.min.js"></script>
        <script src="assets/js/core/popper.min.js"></script>
        <script src="assets/js/core/bootstrap.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="assets/js/plugin/sweetalert2/sweetalert2.all.min.js"></script>
        <!-- Toast Helper -->
        <script src="assets/js/toast-custom.js"></script>

        <!-- Chosen JS -->
        <script src="assets/js/plugin/chosen/chosen.jquery.min.js"></script>
    </head>

    <body>
        <div class="wrapper">
            <div class="main-header">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="purple">

                    <a href="?module=dashboard" class="logo text-white font-weight-bold d-flex align-items-center">
                        <img src="<?= getLogoAppPath($sys_setting) ?>" alt="navbar brand" class="navbar-brand mr-2"
                            style="max-height: 32px;">
                        <span class="navbar-brand-title font-weight-bold"
                            style="font-size: 1.1rem; letter-spacing: 0.5px; text-transform: uppercase;">
                            <?= htmlspecialchars($sys_setting['nama_aplikasi']) ?>
                        </span>
                    </a>
                    <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
                        data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon">
                            <i class="icon-menu"></i>
                        </span>
                    </button>
                    <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="icon-menu"></i>
                        </button>
                    </div>
                </div>
                <!-- End Logo Header -->

                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-expand-lg" data-background-color="purple2">
                    <div class="container-fluid">
                        <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                            <?php
                            $sess_hak_akses = $_SESSION['hak_akses'] ?? '';
                            $sess_id_cabang = $_SESSION['id_cabang'] ?? 0;
                            $where_low = ($sess_hak_akses == 'Super Admin' || $sess_hak_akses == 'Administrator')
                                ? "WHERE stok <= stok_minimum"
                                : "WHERE id_cabang = '$sess_id_cabang' AND stok <= stok_minimum";

                            // Query Stok Kritis
                            $q_low = mysqli_query($mysqli, "SELECT id_barang, nama_barang, stok, stok_minimum FROM tbl_barang $where_low LIMIT 5");
                            $q_low_count = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tbl_barang $where_low");
                            $row_low_count = mysqli_fetch_assoc($q_low_count);
                            $cnt_low = (int) ($row_low_count['total'] ?? 0);

                            // Query Pending Approval
                            $cnt_pending = 0;
                            if ($sess_hak_akses == 'Super Admin' || $sess_hak_akses == 'Administrator' || $sess_hak_akses == 'Kepala Gudang') {
                                $qp1 = mysqli_query($mysqli, "SELECT COUNT(*) as c FROM tbl_barang_masuk WHERE status = 'Pending'");
                                $qp2 = mysqli_query($mysqli, "SELECT COUNT(*) as c FROM tbl_barang_keluar WHERE status = 'Pending'");
                                $qp3 = mysqli_query($mysqli, "SELECT COUNT(*) as c FROM adjustment WHERE status = 'Pending'");
                                $r1 = mysqli_fetch_assoc($qp1);
                                $r2 = mysqli_fetch_assoc($qp2);
                                $r3 = mysqli_fetch_assoc($qp3);
                                $cnt_pending = ($r1['c'] ?? 0) + ($r2['c'] ?? 0) + ($r3['c'] ?? 0);
                            }
                            ?>

                            <!-- Active Branch Switcher -->
                            <?php if ($sess_hak_akses === 'Super Admin' || $sess_hak_akses === 'Administrator'): ?>
                                <li class="nav-item dropdown mr-3">
                                    <a class="nav-link dropdown-toggle text-white font-weight-bold d-flex align-items-center px-3 py-1 bg-white-10 rounded-pill"
                                        data-toggle="dropdown" href="#" role="button" aria-expanded="false"
                                        title="Pilih Cabang / Lokasi Work">
                                        <i class="fas fa-store text-warning mr-2"></i>
                                        <span><?= htmlspecialchars(getActiveCabangName($mysqli)) ?></span>
                                        <i class="fas fa-chevron-down ml-2 fa-xs text-white-50"></i>
                                    </a>
                                    <div class="dropdown-menu animated fadeIn shadow-sm mt-2">
                                        <div class="dropdown-header text-uppercase font-weight-bold text-muted"
                                            style="font-size: 0.75rem;">Switch Active Branch</div>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between <?= (getActiveCabangId() === 0) ? 'active bg-primary text-white font-weight-bold' : '' ?>"
                                            href="switch_cabang.php?id_cabang=0">
                                            <span><i class="fas fa-globe mr-2"></i> Semua Cabang</span>
                                            <?php if (getActiveCabangId() === 0): ?><i
                                                    class="fas fa-check fa-xs"></i><?php endif; ?>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <?php foreach (getAllBranches($mysqli) as $b): ?>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between <?= (getActiveCabangId() === (int) $b['id_cabang']) ? 'active bg-primary text-white font-weight-bold' : '' ?>"
                                                href="switch_cabang.php?id_cabang=<?= $b['id_cabang'] ?>">
                                                <span><i class="fas fa-building mr-2"></i>
                                                    <?= htmlspecialchars($b['nama_cabang']) ?></span>
                                                <?php if (getActiveCabangId() === (int) $b['id_cabang']): ?><i
                                                        class="fas fa-check fa-xs"></i><?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </li>
                            <?php else: ?>
                                <li class="nav-item mr-3">
                                    <span
                                        class="badge badge-light px-3 py-2 text-dark font-weight-bold shadow-sm d-flex align-items-center"
                                        style="border-radius: 20px;">
                                        <i class="fas fa-building text-primary mr-2"></i>
                                        <?= htmlspecialchars(getActiveCabangName($mysqli)) ?>
                                    </span>
                                </li>
                            <?php endif; ?>



                            <!-- Low Stock Alert Dropdown -->
                            <li class="nav-item dropdown hidden-caret mr-3">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                                    aria-expanded="false" title="Peringatan Stok Minimum">
                                    <i class="fas fa-bell text-white" style="font-size: 1.2rem;"></i>
                                    <?php if ($cnt_low > 0): ?>
                                        <span class="notification badge badge-danger"><?= $cnt_low ?></span>
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                                    <li>
                                        <div class="dropdown-title d-flex justify-content-between align-items-center">
                                            <span>Peringatan Stok Kritis</span>
                                            <span class="badge badge-warning"><?= $cnt_low ?> Item</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="notif-scroll scrollbar-outer">
                                            <div class="notif-center">
                                                <?php if ($cnt_low > 0): ?>
                                                    <?php while ($rlow = mysqli_fetch_assoc($q_low)): ?>
                                                        <a href="?module=barang">
                                                            <div class="notif-icon notif-danger"><i
                                                                    class="fas fa-exclamation-triangle"></i></div>
                                                            <div class="notif-content">
                                                                <span
                                                                    class="block"><?= htmlspecialchars($rlow['nama_barang']) ?></span>
                                                                <span class="time text-danger font-weight-bold">Sisa Stok:
                                                                    <?= $rlow['stok'] ?> (Min: <?= $rlow['stok_minimum'] ?>)</span>
                                                            </div>
                                                        </a>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <div class="p-3 text-center text-muted">Semua stok barang aman.</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="see-all" href="?module=laporan_stok">Lihat Semua Laporan Stok<i
                                                class="fa fa-angle-right ml-2"></i></a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Data user login -->
                            <li class="nav-item dropdown hidden-caret">
                                <!-- Link untuk membuka dropdown profil -->
                                <a class="dropdown-toggle profile-pic d-flex align-items-center" data-toggle="dropdown"
                                    href="javascript:void(0)" aria-expanded="false">
                                    <div class="avatar-sm">
                                        <!-- Gambar profil kecil -->
                                        <img src="<?= $user_avatar_path ?>" alt="image profile"
                                            class="avatar-img rounded-circle" style="object-fit: cover;">
                                    </div>
                                    <!-- Ikon panah bawah presisi & rapi -->
                                    <i class="fas fa-chevron-down ml-2 text-white"
                                        style="font-size: 11px; opacity: 0.85;"></i>
                                </a>

                                <!-- Isi dropdown profil -->
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <!-- Item pertama dalam dropdown, menampilkan gambar profil dan nama pengguna -->
                                    <li>
                                        <div class="user-box">
                                            <!-- Gambar profil besar -->
                                            <div class="avatar-lg"><img src="<?= $user_avatar_path ?>" alt="image profile"
                                                    class="avatar-img rounded-circle"
                                                    style="width: 60px; height: 60px; object-fit: cover;"></div>
                                            <div class="u-text pt-1">
                                                <!-- Nama pengguna diambil dari session -->
                                                <h4><?php echo $_SESSION['nama_user']; ?></h4>
                                                <!-- Hak akses pengguna diambil dari session -->
                                                <p class="text-muted"><?php echo $_SESSION['hak_akses']; ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- Menu user -->
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                        <a class="dropdown-item py-2" href="?module=profile">
                                            <i class="fas fa-user-alt mr-2"></i> Profil Saya
                                        </a>
                                        <a class="dropdown-item py-2" href="?module=form_ubah_password">
                                            <i class="fas fa-user-lock mr-2"></i> Ubah Password
                                        </a>
                                        <a class="dropdown-item py-2" href="clear_cache.php">
                                            <i class="fas fa-sync-alt mr-2 text-warning"></i> Clear Cache & Optimasi
                                        </a>
                                        <div class="dropdown-divider my-1"></div>
                                        <a class="dropdown-item py-2" href="javascript:void(0)"
                                            onclick="return confirmLogout();">
                                            <i class="fas fa-sign-out-alt mr-2 text-danger"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>

            <!-- Sidebar -->
            <div class="sidebar sidebar-style-2">
                <div class="sidebar-wrapper scrollbar scrollbar-inner">
                    <div class="sidebar-content">
                        <!-- Data user login -->
                        <div class="user">
                            <div class="avatar-sm float-left mr-2">
                                <img src="<?= $user_avatar_path ?>" alt="image profile" class="avatar-img rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            </div>
                            <div class="info">
                                <a>
                                    <span>
                                        <?php echo $_SESSION['nama_user']; ?>
                                        <span class="user-level"><?php echo $_SESSION['hak_akses']; ?></span>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <!-- Sidebar Menu -->
                        <ul class="nav nav-secondary">

                            <!-- Panggil file "sidebar_menu.php" untuk menampilkan menu -->
                            <?php include "sidebar_menu.php"; ?>

                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Sidebar -->

            <div class="main-panel">
                <!-- Main Content -->
                <div class="content">

                    <!-- Panggil file "content.php" untuk menampilkan halaman konten -->
                    <?php include "content.php"; ?>

                </div>
                <!-- End Main Content -->

                <!-- Footer -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="copyright ml-auto">
                            Copyright &copy; <?php echo date('Y'); ?> - <a href="javascript:void(0)"
                                class="text-secondary font-weight-bold"><?= htmlspecialchars($sys_setting['footer_text']) ?></a>.
                        </div>
                    </div>
                </footer>
                <!-- End Footer -->
            </div>
        </div>

        <!-- Modal Logout -->
        <div class="modal fade" id="modalLogout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-sign-out-alt mr-2"></i>Logout</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Apakah Anda yakin ingin logout?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-round" data-dismiss="modal">Batal</button>
                        <a href="logout.php" class="btn btn-danger btn-round">Ya, Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery UI -->
        <script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
        <script src="assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

        <!-- jQuery Scrollbar -->
        <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
        <!-- Datatables -->
        <script src="assets/js/plugin/datatables/datatables.min.js"></script>
        <!-- Datepicker JS -->
        <script src="assets/js/plugin/datepicker/js/bootstrap-datepicker.min.js"></script>

        <!-- Atlantis JS -->
        <script src="assets/js/atlantis.min.js"></script>

        <!-- Custom Scripts -->
        <script src="assets/js/plugin.js"></script>
        <script src="assets/js/form-validation.js"></script>
    </body>

    </html>
<?php } ?>