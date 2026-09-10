/**
 * INSTOCK - Modern Form Validation with SweetAlert2 Toast Feedback
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    // Ambil seluruh form yang menggunakan kelas 'needs-validation'
    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach(function (form) {
      // Event Listener saat Form Disubmit
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();

          // Tambahkan kelas CSS bootstrap 'was-validated'
          form.classList.add('was-validated');

          // Cari elemen pertama yang tidak valid
          const firstInvalid = form.querySelector(':invalid');
          if (firstInvalid) {
            firstInvalid.focus();
            if (typeof firstInvalid.scrollIntoView === 'function') {
              firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          }

          // Tampilkan SweetAlert2 Toast Peringatan
          if (typeof showToast === 'function') {
            showToast('warning', 'Form Belum Lengkap!', 'Mohon periksa dan lengkapi kolom yang ditandai merah.');
          }
        } else {
          form.classList.add('was-validated');
        }
      }, false);

      // Live validation: Hapus status error saat pengguna mengetik/mengubah isi input
      const inputs = form.querySelectorAll('input, select, textarea');
      Array.from(inputs).forEach(function (input) {
        input.addEventListener('input', function () {
          if (input.checkValidity()) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
          } else if (form.classList.contains('was-validated')) {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
          }
        });

        input.addEventListener('change', function () {
          if (input.checkValidity()) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
          }
        });
      });
    });
  });
})();

/**
 * Helper pembatasan karakter input (Kombinasi tombol & char code)
 */
function getkey(e) {
  if (window.event) return window.event.keyCode;
  else if (e) return e.which;
  else return null;
}

function goodchars(e, goods, field) {
  var key, keychar;
  key = getkey(e);
  if (key == null) return true;

  keychar = String.fromCharCode(key).toLowerCase();
  goods = goods.toLowerCase();

  // Izinkan karakter yang terdaftar
  if (goods.indexOf(keychar) !== -1) return true;

  // Izinkan tombol kontrol (Backspace, Tab, Escape, Delete, Panah)
  if (key == null || key == 0 || key == 8 || key == 9 || key == 27) return true;

  // Pindah fokus saat menekan Enter
  if (key == 13) {
    var i;
    for (i = 0; i < field.form.elements.length; i++) {
      if (field == field.form.elements[i]) break;
    }
    i = (i + 1) % field.form.elements.length;
    field.form.elements[i].focus();
    return false;
  }
  return false;
}