// Fungsi untuk menginisialisasi plugin INSTOCK WMS
$(document).ready(function() {
  // 1. DataTables Initializer
  if ($.fn.DataTable && $('#basic-datatables').length) {
    $('#basic-datatables').DataTable({
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
        infoFiltered: "(disaring dari _MAX_ total data)",
        paginate: {
          first: "Pertama",
          last: "Terakhir",
          next: "<i class='fas fa-chevron-right'></i>",
          previous: "<i class='fas fa-chevron-left'></i>"
        }
      }
    });
  }

  // 2. Datepicker Initializer (Bootstrap Datepicker)
  if ($.fn.datepicker) {
    $('.date-picker').datepicker({
      format: "dd-mm-yyyy",
      autoclose: true,
      todayHighlight: true,
      orientation: "bottom auto",
      templates: {
        leftArrow: '<i class="fas fa-chevron-left"></i>',
        rightArrow: '<i class="fas fa-chevron-right"></i>'
      }
    });
  }
});