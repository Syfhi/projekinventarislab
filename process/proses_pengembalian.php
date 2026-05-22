<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['kembalikan'])){
    $id_peminjaman = $_POST['id_peminjaman'];
    $id_barang = $_POST['id_barang'];
    $tanggal_kembali_actual = date('Y-m-d');
    $kondisi = $_POST['kondisi'];

    // Validasi input
    if(empty($id_peminjaman) || empty($id_barang)){
        $_SESSION['error'] = 'Data tidak valid!';
        header("Location: ../pages/peminjaman.php");
        exit;
    }

    // Update status peminjaman dan lepaskan relasi barang setelah dikembalikan
    $query = "UPDATE peminjaman SET status='Dikembalikan', tanggal_kembali_actual='$tanggal_kembali_actual', id_barang=NULL 
              WHERE id_peminjaman='$id_peminjaman'";
    
    if(mysqli_query($conn, $query)){
        // Update stok barang (tambah) dan kondisi
        mysqli_query($conn, "UPDATE barang SET stok = stok + 1, kondisi='$kondisi' WHERE id_barang='$id_barang'");
        
        // Simpan ke riwayat
        mysqli_query($conn, "INSERT INTO riwayat (id_peminjaman, keterangan, tanggal) 
                             VALUES ('$id_peminjaman','Barang dikembalikan dalam kondisi $kondisi','$tanggal_kembali_actual')");
        
        $_SESSION['success'] = 'Barang berhasil diterima kembali!';
    } else {
        $_SESSION['error'] = 'Gagal memproses pengembalian: ' . mysqli_error($conn);
    }

    header("Location: ../pages/peminjaman.php");
    exit;
}
?>
