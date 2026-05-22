<?php
session_start();
include '../config/koneksi.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];

    // Validasi ID
    if(empty($id)){
        $_SESSION['error'] = 'ID barang tidak valid!';
        header("Location: ../pages/inventaris.php");
        exit;
    }

    $barangResult = mysqli_query($conn, "SELECT nama_barang FROM barang WHERE id_barang='$id'");
    $barangData = mysqli_fetch_assoc($barangResult);
    $namaBarang = $barangData ? $barangData['nama_barang'] : '';

    // Periksa apakah barang masih terkait dengan peminjaman aktif
    $cekPinjam = mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE id_barang='$id' AND status != 'Dikembalikan'");
    $pinjamData = mysqli_fetch_assoc($cekPinjam);
    if($pinjamData['total'] > 0){
        $_SESSION['error'] = 'Barang tidak dapat dihapus karena masih dipinjam.';
        header("Location: ../pages/inventaris.php");
        exit;
    }

    // Delete dari database
    $query = "DELETE FROM barang WHERE id_barang='$id'";
    
    if(mysqli_query($conn, $query)){
        $tanggal = date('Y-m-d');
        $keterangan = $namaBarang ? "Barang dihapus: '" . mysqli_real_escape_string($conn, $namaBarang) . "'." : "Barang dengan ID $id dihapus dari inventaris.";
        mysqli_query($conn, "INSERT INTO riwayat (keterangan, tanggal) VALUES ('" . mysqli_real_escape_string($conn, $keterangan) . "', '$tanggal')");

        $_SESSION['success'] = 'Barang berhasil dihapus!';
    } else {
        $_SESSION['error'] = 'Gagal menghapus barang: ' . mysqli_error($conn);
    }

    header("Location: ../pages/inventaris.php");
    exit;
}
?>