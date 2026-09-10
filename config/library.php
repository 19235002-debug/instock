<?php

class Library {

    private $db; // Database connection

    public function __construct($connection) {
        $this->db = $connection;
    }

    public function getIdTransaksi() {
        // Query to fetch the last id_transaksi from tbl_barang_masuk
        $query = "SELECT RIGHT(id_transaksi,7) as nomor FROM tbl_barang_masuk ORDER BY id_transaksi DESC LIMIT 1";
        $result = $this->db->query($query);
        // More code to process the result and return the id_transaksi
    }

    public function getBarangById($id_barang) {
        // Query to fetch data from tbl_barang based on id_barang
        $query = "SELECT * FROM tbl_barang WHERE id_barang = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_barang]);
        return $stmt->fetch(); // Assuming PDO is used
    }

    public function importBarangMasuk($data) {
        // Code to import data into tbl_barang_masuk
        // $data would be an associative array with keys corresponding to columns
        $query = "INSERT INTO tbl_barang_masuk (id_transaksi, tanggal, barang, jumlah, satuan) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$data['id_transaksi'], $data['tanggal'], $data['barang'], $data['jumlah'], $data['satuan']]);
    }

    // Add other methods as needed

}
