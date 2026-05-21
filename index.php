<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location:pages/login.php");
}
?>

<h2>Selamat datang, <?= $_SESSION['nama']; ?></h2>

<a href="logout.php">Logout</a>