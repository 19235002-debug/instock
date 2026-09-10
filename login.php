<?php
require_once "config/database.php";
require_once "helper/setting_helper.php";
$sys_setting = getSystemSetting($mysqli);
?>
  <!-- Title -->
<title><?= htmlspecialchars($sys_setting['nama_aplikasi']) ?> | Login</title>

<!-- Favicon icon -->
<?php $fav_v = md5($sys_setting['favicon'] . ($sys_setting['updated_at'] ?? '')); ?>
<link rel="icon" href="<?= htmlspecialchars($sys_setting['favicon']) ?>?v=<?= $fav_v ?>" type="image/x-icon" />
<link rel="shortcut icon" href="<?= htmlspecialchars($sys_setting['favicon']) ?>?v=<?= $fav_v ?>" type="image/x-icon" />

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
    active: function() {
      sessionStorage.fonts = true;
    }
  });
</script>

<!-- CSS Files -->
<link rel="stylesheet" href="assets/css/fonts.min.css">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/atlantis.min.css">
<link rel="stylesheet" href="assets/css/global-custom-theme.css">
<link rel="stylesheet" href="assets/css/login.css?v=<?= time() ?>">
  <!-- SweetAlert2 -->
  <script src="assets/js/plugin/sweetalert2/sweetalert2.all.min.js"></script>
  <script src="assets/js/toast-custom.js"></script>
</head>

<body class="login">
  <?php
  // menampilkan pesan SweetAlert2 Toast sesuai dengan proses yang dijalankan
  if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 1) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('error', 'Gagal Login!', 'Username atau Password salah. Cek kembali Username dan Password Anda.'); });</script>";
    } elseif ($_GET['pesan'] == 2) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('warning', 'Peringatan!', 'Anda harus login terlebih dahulu.'); });</script>";
    } elseif ($_GET['pesan'] == 3) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { showToast('success', 'Sukses!', 'Anda telah berhasil logout.'); });</script>";
    }
  }
  ?>

  <div class="wrapper wrapper-login">
    <div class="container container-login animated fadeIn">
      <!-- logo -->
      <div class="text-center mb-4"><img src="<?= htmlspecialchars($sys_setting['logo']) ?>" alt="Logo" style="max-width: 245px; max-height: 110px; object-fit: contain;"></div>
      <!-- judul -->
      <h3 class="text-center"><b>Silahkan Login</b></h3>
      <!-- form login -->
      <form action="proses_login.php" method="post" class="needs-validation" novalidate>
        <div class="form-group form-floating-label">
          <div class="user-icon"><i class="fas fa-user"></i></div>
          <input type="text" id="username" name="username" class="form-control input-border-bottom" placeholder=" " autocomplete="off" required>
          <label for="username" class="placeholder">Username</label>
          <div class="invalid-feedback">Username tidak boleh kosong.</div>
        </div>

        <div class="form-group form-floating-label">
          <div class="user-icon"><i class="fas fa-lock"></i></div>
          <input type="password" id="password" name="password" class="form-control input-border-bottom" placeholder=" " autocomplete="off" required>
          <label for="password" class="placeholder">Password</label>
          <div class="show-password" id="togglePassBtn"><i class="fas fa-eye" id="toggleIcon"></i></div>
          <div class="invalid-feedback">Password tidak boleh kosong.</div>
        </div>

        <div class="form-action mt-2">
          <!-- tombol login -->
          <input type="submit" name="login" value="LOGIN" class="btn btn-secondary btn-rounded btn-login btn-block">
        </div>

        <!-- footer -->
        <div class="login-footer mt-4">
          <span class="msg">&copy; <a href="http://birawa.my.id">INSTOCK</a> - <?php echo date('Y'); ?></span>
        </div>
      </form>
    </div>
  </div>

  <!--   Core JS Files   -->
  <script src="assets/js/core/jquery.3.2.1.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>

  <!-- jQuery UI -->
  <script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>

  <!-- Template JS -->
  <script src="assets/js/ready.js"></script>

  <!-- Custom Scripts -->
  <script src="assets/js/form-validation.js"></script>
  <script>
    $(document).ready(function() {
      // Live check to toggle 'filled' class on inputs
      $('.form-floating-label .form-control').on('keyup change input blur focus', function() {
        if ($(this).val().trim() !== '') {
          $(this).addClass('filled');
        } else {
          $(this).removeClass('filled');
        }
      }).trigger('change');

      // Password visibility toggle handler
      $('#togglePassBtn').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $pass = $('#password');
        var $icon = $('#toggleIcon');
        
        if ($pass.attr('type') === 'password') {
          $pass.attr('type', 'text');
          $icon.attr('class', 'fas fa-eye-slash');
        } else {
          $pass.attr('type', 'password');
          $icon.attr('class', 'fas fa-eye');
        }
        $pass.trigger('input');
      });
    });
  </script>
</body>

</html>