/**
 * INSTOCK - SweetAlert2 Helper Config (Toast & Confirm Dialogs)
 */

// Menambahkan CSS khusus untuk memberikan jarak antar tombol pada SweetAlert2 Modal
(function () {
  const style = document.createElement('style');
  style.innerHTML = `
    .swal2-actions .btn {
      margin: 0 6px !important;
    }
  `;
  document.head.appendChild(style);
})();

// SweetAlert2 Toast Configuration
const SwalToast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3500,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer);
    toast.addEventListener('mouseleave', Swal.resumeTimer);
  }
});

/**
 * Tampilkan Notifikasi Toast Melayang
 * @param {string} icon - 'success' | 'error' | 'warning' | 'info' | 'question'
 * @param {string} title - Judul Notifikasi
 * @param {string} text - Pesan tambahan (opsional)
 */
function showToast(icon, title, text = '') {
  SwalToast.fire({
    icon: icon,
    title: title,
    text: text
  });
}

/**
 * Tampilkan Modal Konfirmasi Hapus Data Modern (SweetAlert2)
 * @param {string} url - URL aksi penghapusan
 * @param {string} message - Pesan detail konfirmasi
 */
function confirmDelete(url, message = 'Data yang dihapus tidak dapat dikembalikan!') {
  Swal.fire({
    title: 'Apakah Anda Yakin?',
    text: message,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus!',
    cancelButtonText: 'Batal',
    reverseButtons: true,
    customClass: {
      confirmButton: 'btn btn-danger px-4 mx-2',
      cancelButton: 'btn btn-secondary px-4 mx-2'
    },
    buttonsStyling: false
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
  return false;
}

/**
 * Tampilkan Modal Konfirmasi Logout Modern (SweetAlert2)
 */
function confirmLogout() {
  Swal.fire({
    title: 'Konfirmasi Logout',
    text: 'Apakah Anda yakin ingin keluar dari sistem INSTOCK?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-sign-out-alt mr-1"></i> Ya, Logout',
    cancelButtonText: 'Batal',
    reverseButtons: true,
    customClass: {
      confirmButton: 'btn btn-danger px-4 mx-2',
      cancelButton: 'btn btn-secondary px-4 mx-2'
    },
    buttonsStyling: false
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = 'logout.php';
    }
  });
  return false;
}
