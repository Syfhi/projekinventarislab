<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['pinjam'])){
    $id_user = $_SESSION['id_user'];
    $id_barang = $_POST['id_barang'];
    $tanggal_pinjam = date('Y-m-d');
    $durasi = $_POST['durasi'];
    $tanggal_kembali = date('Y-m-d', strtotime("+$durasi days"));
    $keterangan = $_POST['keterangan'];

    // Validasi input
    if(empty($id_barang) || empty($durasi)){
        $_SESSION['error'] = 'Semua field harus diisi!';
        header("Location: ../pages/peminjaman.php");
        exit;
    }

    // Cek stok barang
    $cek = mysqli_query($conn, "SELECT stok FROM barang WHERE id_barang='$id_barang'");
    $barang = mysqli_fetch_assoc($cek);
    
    if($barang['stok'] <= 0){
        $_SESSION['error'] = 'Stok barang tidak tersedia!';
        header("Location: ../pages/peminjaman.php");
        exit;
    }

    // Insert ke tabel peminjaman
    $query = "INSERT INTO peminjaman (id_user, id_barang, tanggal_pinjam, tanggal_kembali, durasi, keterangan, status) 
              VALUES ('$id_user','$id_barang','$tanggal_pinjam','$tanggal_kembali','$durasi','$keterangan','Sedang Dipinjam')";
    
    if(mysqli_query($conn, $query)){
        $id_peminjaman = mysqli_insert_id($conn);
        // Update stok barang (kurangi)
        mysqli_query($conn, "UPDATE barang SET stok = stok - 1 WHERE id_barang='$id_barang'");
        $logText = "Barang dipinjam: " . mysqli_real_escape_string($conn, $keterangan);
        mysqli_query($conn, "INSERT INTO riwayat (id_peminjaman, keterangan, tanggal) 
                             VALUES ('$id_peminjaman', '" . $logText . "', '$tanggal_pinjam')");
        $_SESSION['success'] = 'Peminjaman berhasil! Mohon kembalikan pada tanggal ' . $tanggal_kembali;
    } else {
        $_SESSION['error'] = 'Gagal memproses peminjaman: ' . mysqli_error($conn);
    }

    header("Location: ../pages/peminjaman.php");
    exit;
}
?>
