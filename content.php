<?php
// Pastikan sesi dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mencegah direct access
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('location: 404.html');
    exit;
}

require_once "config/database.php";
require_once "helper/permission_helper.php";

$module = isset($_GET['module']) && !empty($_GET['module']) ? $_GET['module'] : 'dashboard';
$hak_akses = isset($_SESSION['hak_akses']) ? $_SESSION['hak_akses'] : '';

// Grup hak akses terstruktur
$admin_only = ['Super Admin', 'Administrator'];
$manager_groups = ['Super Admin', 'Administrator', 'Kepala Gudang'];
$gudang_groups = ['Super Admin', 'Administrator', 'Kepala Gudang', 'Staff Gudang', 'Admin Gudang'];
$all_roles = ['Super Admin', 'Administrator', 'Kepala Gudang', 'Staff Gudang', 'Admin Gudang', 'Supplier'];

$modules = [
    'dashboard' => ['path' => "modules/dashboard/tampil_data.php", 'access' => []],
    
    // Master Barang & Kategori
    'barang' => ['path' => "modules/barang/tampil_data.php", 'access' => $all_roles],
    'form_entri_barang' => ['path' => "modules/barang/form_entri.php", 'access' => $gudang_groups],
    'tampil_detail_barang' => ['path' => "modules/barang/tampil_detail.php", 'access' => $all_roles],
    'form_ubah_barang' => ['path' => "modules/barang/form_ubah.php", 'access' => $gudang_groups],
    
    'jenis' => ['path' => "modules/jenis/tampil_data.php", 'access' => $all_roles],
    'form_entri_jenis' => ['path' => "modules/jenis/form_entri.php", 'access' => $gudang_groups],
    'form_ubah_jenis' => ['path' => "modules/jenis/form_ubah.php", 'access' => $gudang_groups],
    
    'satuan' => ['path' => "modules/satuan/tampil_data.php", 'access' => $all_roles],
    'form_entri_satuan' => ['path' => "modules/satuan/form_entri.php", 'access' => $gudang_groups],
    'form_ubah_satuan' => ['path' => "modules/satuan/form_ubah.php", 'access' => $gudang_groups],
    
    // Master Lokasi & STO
    'location' => ['path' => "modules/location/tampil_data.php", 'access' => $all_roles],
    'form_entri_location' => ['path' => "modules/location/form_entri.php", 'access' => $gudang_groups],
    'tampil_detail_location' => ['path' => "modules/location/tampil_detail.php", 'access' => $all_roles],
    'form_ubah_location' => ['path' => "modules/location/form_ubah.php", 'access' => $gudang_groups],
    
    'lokasi_sto' => ['path' => "modules/lokasi_sto/tampil_data_lokasi_sto.php", 'access' => $gudang_groups],
    'form_entri_lokasi_sto' => ['path' => "modules/lokasi_sto/form_entri_lokasi_sto.php", 'access' => $gudang_groups],
    'form_ubah_lokasi_sto' => ['path' => "modules/lokasi_sto/form_ubah_lokasi_sto.php", 'access' => $gudang_groups],
    'proses_hapus_lokasi_sto' => ['path' => "modules/lokasi_sto/proses_hapus_lokasi_sto.php", 'access' => $gudang_groups],
    'proses_entri_lokasi_sto' => ['path' => "modules/lokasi_sto/proses_entri_lokasi_sto.php", 'access' => $gudang_groups],
    
    // Transaksi Inventaris
    'barang_masuk' => ['path' => "modules/barang-masuk/tampil_data.php", 'access' => $all_roles],
    'form_entri_barang_masuk' => ['path' => "modules/barang-masuk/form_entri.php", 'access' => $all_roles],
    
    'barang_keluar' => ['path' => "modules/barang-keluar/tampil_data.php", 'access' => $gudang_groups],
    'form_entri_barang_keluar' => ['path' => "modules/barang-keluar/form_entri.php", 'access' => $gudang_groups],
    
    'adjustment' => ['path' => "modules/adjustment/tampil_data.php", 'access' => $manager_groups],
    'form_entri_adjustment' => ['path' => "modules/adjustment/form_entri.php", 'access' => $manager_groups],
    'proses_hapus_adjustment' => ['path' => "modules/adjustment/proses_hapus.php", 'access' => $manager_groups],
    
    'transfer_stok' => ['path' => "modules/transfer_stok/tampil_data.php", 'access' => $admin_only],
    'form_entri_transfer' => ['path' => "modules/transfer_stok/form_entri.php", 'access' => $admin_only],
    'proses_entri_transfer' => ['path' => "modules/transfer_stok/proses_entri.php", 'access' => $admin_only],
    'proses_hapus_transfer' => ['path' => "modules/transfer_stok/proses_hapus.php", 'access' => $admin_only],
    'riwayat_transfer' => ['path' => "modules/transfer_stok/riwayat.php", 'access' => $admin_only],
    
    // Pengiriman & Ongkir
    'raja_ongkir' => ['path' => "modules/raja_ongkir/tampil_data.php", 'access' => $admin_only],
    'lacak' => ['path' => "lacak.php", 'access' => $admin_only],
    'biteship_tracking' => ['path' => "modules/biteship/track_shipment.php", 'access' => $admin_only],
    
    // Master Cabang & Area
    'cabang' => ['path' => "modules/cabang/tampil_cabang.php", 'access' => $admin_only],
    'form_entri_cabang' => ['path' => "modules/cabang/form_entri.php", 'access' => $admin_only],
    'edit_cabang' => ['path' => "modules/cabang/edit_cabang.php", 'access' => $admin_only],
    'proses_entri_cabang' => ['path' => "modules/cabang/proses_entri_cabang.php", 'access' => $admin_only],
    'proses_edit_cabang' => ['path' => "modules/cabang/proses_edit_cabang.php", 'access' => $admin_only],
    'proses_hapus_cabang' => ['path' => "modules/cabang/proses_hapus_cabang.php", 'access' => $admin_only],
    
    // Laporan
    'laporan_stok' => ['path' => "modules/laporan-stok/tampil_data.php", 'access' => $all_roles],
    'laporan_barang_masuk' => ['path' => "modules/laporan-barang-masuk/tampil_data.php", 'access' => $all_roles],
    'laporan_barang_keluar' => ['path' => "modules/laporan-barang-keluar/tampil_data.php", 'access' => $gudang_groups],
    
    // Pengaturan & User & Permission & Governance
    'user' => ['path' => "modules/user/tampil_data.php", 'access' => $manager_groups],
    'form_entri_user' => ['path' => "modules/user/form_entri.php", 'access' => $manager_groups],
    'form_ubah_user' => ['path' => "modules/user/form_ubah.php", 'access' => $manager_groups],
    'permission' => ['path' => "modules/permission/tampil_data.php", 'access' => $admin_only],
    'pengaturan' => ['path' => "modules/pengaturan/tampil_data.php", 'access' => $admin_only],
    'approval' => ['path' => "modules/approval/tampil_data.php", 'access' => $manager_groups],
    'audit_log' => ['path' => "modules/audit_log/tampil_data.php", 'access' => $admin_only],
    'profile' => ['path' => "modules/profile/form_profile.php", 'access' => []],
    'form_profile' => ['path' => "modules/profile/form_profile.php", 'access' => []],
    'form_ubah_password' => ['path' => "modules/password/form_ubah.php", 'access' => []],
    'print_label' => ['path' => "modules/print-label/print_label.php", 'access' => []],
];

if (isset($modules[$module])) {
    $has_role_access = empty($modules[$module]['access']) || in_array($hak_akses, $modules[$module]['access']);
    $has_dynamic_permission = isModuleAllowed($hak_akses, $module, $mysqli);

    if ($has_role_access && $has_dynamic_permission) {
        include $modules[$module]['path'];
    } else {
        include "403.php";
        exit;
    }
} else {
    include "404.php";
    exit;
}
