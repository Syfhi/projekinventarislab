<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['tambah'])){
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $lokasi = $_POST['lokasi'];
    $kondisi = $_POST['kondisi'];

    // Validasi input
    if(empty($nama) || empty($kategori) || empty($stok)){
        $_SESSION['error'] = 'Semua field harus diisi!';
        header("Location: ../pages/inventaris.php");
        exit;
    }

    // Insert ke database
    $query = "INSERT INTO barang (nama_barang, kategori, stok, lokasi, kondisi) 
              VALUES ('$nama','$kategori','$stok','$lokasi','$kondisi')";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = 'Barang berhasil ditambahkan!';
    } else {
        $_SESSION['error'] = 'Gagal menambahkan barang: ' . mysqli_error($conn);
    }

    header("Location: ../pages/inventaris.php");
    exit;
}
?>