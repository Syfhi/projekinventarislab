<?php
$conn = mysqli_connect("localhost", "root", "", "inventaris");

if(!$conn){
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>