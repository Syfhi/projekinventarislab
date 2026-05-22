<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['edit'])){
    $id = $_POST['id_barang'];
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $lokasi = $_POST['lokasi'];
    $kondisi = $_POST['kondisi'];

    // Validasi input
    if(empty($id) || empty($nama) || empty($kategori) || empty($stok)){
        $_SESSION['error'] = 'Semua field harus diisi!';
        header("Location: ../pages/inventaris.php");
        exit;
    }

    // Update ke database
    $query = "UPDATE barang SET nama_barang='$nama', kategori='$kategori', 
              stok='$stok', lokasi='$lokasi', kondisi='$kondisi' 
              WHERE id_barang='$id'";
    
    if(mysqli_query($conn, $query)){
        $tanggal = date('Y-m-d');
        $keterangan = "Barang diperbarui: '" . mysqli_real_escape_string($conn, $nama) . "'.";
        mysqli_query($conn, "INSERT INTO riwayat (keterangan, tanggal) VALUES ('" . mysqli_real_escape_string($conn, $keterangan) . "', '$tanggal')");

        $_SESSION['success'] = 'Barang berhasil diperbarui!';
    } else {
        $_SESSION['error'] = 'Gagal memperbarui barang: ' . mysqli_error($conn);
    }

    header("Location: ../pages/inventaris.php");
    exit;
}
?>
