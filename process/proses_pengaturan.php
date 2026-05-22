<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['update_profil'])){
    $id_user = $_SESSION['id_user'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];

    // Validasi input
    if(empty($nama) || empty($email)){
        $_SESSION['error'] = 'Semua field harus diisi!';
        header("Location: ../pages/pengaturan.php");
        exit;
    }

    // Cek email sudah digunakan user lain
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND id_user != '$id_user'");
    if(mysqli_num_rows($cek) > 0){
        $_SESSION['error'] = 'Email sudah digunakan!';
        header("Location: ../pages/pengaturan.php");
        exit;
    }

    // Update profil
    $query = "UPDATE users SET nama='$nama', email='$email' WHERE id_user='$id_user'";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['nama'] = $nama;
        $_SESSION['success'] = 'Profil berhasil diperbarui!';
    } else {
        $_SESSION['error'] = 'Gagal memperbarui profil: ' . mysqli_error($conn);
    }

    header("Location: ../pages/pengaturan.php");
    exit;
}

if(isset($_POST['update_password'])){
    $id_user = $_SESSION['id_user'];
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Validasi input
    if(empty($password_lama) || empty($password_baru) || empty($konfirmasi_password)){
        $_SESSION['error'] = 'Semua field harus diisi!';
        header("Location: ../pages/pengaturan.php");
        exit;
    }

    // Cek password lama
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id_user='$id_user'"));
    
    if(!password_verify($password_lama, $user['password'])){
        $_SESSION['error'] = 'Password lama tidak sesuai!';
        header("Location: ../pages/pengaturan.php");
        exit;
    }

    // Validasi password baru
    if($password_baru !== $konfirmasi_password){
        $_SESSION['error'] = 'Konfirmasi password tidak sesuai!';
        header("Location: ../pages/pengaturan.php");
        exit;
    }

    if(strlen($password_baru) < 6){
        $_SESSION['error'] = 'Password minimal 6 karakter!';
        header("Location: ../pages/pengaturan.php");
        exit;
    }

    // Update password
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password='$password_hash' WHERE id_user='$id_user'";
    
    if(mysqli_query($conn, $query)){
        $_SESSION['success'] = 'Password berhasil diubah!';
    } else {
        $_SESSION['error'] = 'Gagal mengubah password: ' . mysqli_error($conn);
    }

    header("Location: ../pages/pengaturan.php");
    exit;
}
?>
