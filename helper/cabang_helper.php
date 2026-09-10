<?php
/**
 * Helper terpusat untuk Manajemen Multi-Cabang (Multi-Tenant Branch)
 */

if (!function_exists('getActiveCabangId')) {
    function getActiveCabangId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $hak_akses = $_SESSION['hak_akses'] ?? '';
        if ($hak_akses === 'Super Admin' || $hak_akses === 'Administrator') {
            return isset($_SESSION['active_cabang']) ? (int)$_SESSION['active_cabang'] : 0;
        }
        return (int)($_SESSION['id_cabang'] ?? 0);
    }
}

if (!function_exists('setActiveCabangId')) {
    function setActiveCabangId($id_cabang) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $hak_akses = $_SESSION['hak_akses'] ?? '';
        if ($hak_akses === 'Super Admin' || $hak_akses === 'Administrator') {
            $_SESSION['active_cabang'] = (int)$id_cabang;
        }
    }
}

if (!function_exists('getActiveCabangName')) {
    function getActiveCabangName($mysqli) {
        $id = getActiveCabangId();
        if ($id === 0) {
            return "Semua Cabang";
        }
        $query = mysqli_query($mysqli, "SELECT nama_cabang FROM tbl_cabang WHERE id_cabang = '$id'");
        if ($query && $row = mysqli_fetch_assoc($query)) {
            return $row['nama_cabang'];
        }
        return "Cabang #" . $id;
    }
}

if (!function_exists('getBranchWhereClause')) {
    /**
     * Mengembalikan klausa SQL WHERE/AND berdasarkan cabang aktif
     * @param string $column_name Nama kolom (misal 'id_cabang' atau 'b.id_cabang')
     * @param string $clause_type 'WHERE' atau 'AND'
     * @return string
     */
    function getBranchWhereClause($column_name = 'id_cabang', $clause_type = 'WHERE') {
        $id = getActiveCabangId();
        if ($id === 0) {
            return ($clause_type === 'WHERE') ? "WHERE 1=1" : "";
        }
        return "{$clause_type} {$column_name} = '{$id}'";
    }
}

if (!function_exists('getAllBranches')) {
    function getAllBranches($mysqli) {
        $branches = [];
        $query = mysqli_query($mysqli, "SELECT id_cabang, nama_cabang, nama_fm FROM tbl_cabang ORDER BY nama_cabang ASC");
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $branches[] = $row;
            }
        }
        return $branches;
    }
}
