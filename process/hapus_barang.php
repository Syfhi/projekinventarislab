<?php
include '../config/koneksi.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];

    mysqli_query($conn,
        "DELETE FROM barang WHERE id_barang='$id'"
    );

    header("Location: ../pages/inventaris.php");
}
?>