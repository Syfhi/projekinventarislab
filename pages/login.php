<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    if($user){
        if(password_verify($password, $user['password'])){
            $_SESSION['login'] = true;
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['id_user'] = $user['id_user'];

            header("Location: dashboard.php");
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Email tidak ditemukan!";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <button name="login">Login</button>
</form>