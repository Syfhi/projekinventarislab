<?php
include '../config/koneksi.php';

if(isset($_POST['register'])){
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn, "INSERT INTO users (nama,email,password,role)
    VALUES ('$nama','$email','$password','user')");

    echo "Registrasi berhasil!";
}
?>

<form method="POST">
    <input type="text" name="nama" placeholder="Nama" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <button name="register">Daftar</button>
</form>