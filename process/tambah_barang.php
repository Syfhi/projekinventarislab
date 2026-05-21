<?php
include '../config/koneksi.php';

if(isset($_POST['tambah'])){
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];

    mysqli_query($conn,
        "INSERT INTO barang (nama_barang, kategori, stok)
        VALUES ('$nama','$kategori','$stok')"
    );

    header("Location: ../pages/inventaris.php");
}
?>