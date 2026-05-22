<?php
include '../config/koneksi.php';

$success = '';
$error = '';

if(isset($_POST['register'])){
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi password
    if($password !== $confirm_password){
        $error = 'Password dan konfirmasi password tidak sesuai.';
    } else if(strlen($password) < 6){
        $error = 'Password minimal 6 karakter.';
    } else {
        // Cek email sudah terdaftar
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($query) > 0){
            $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "INSERT INTO users (nama,email,password,role)
            VALUES ('$nama','$email','$password_hash','user')");
            
            if($insert){
                $success = 'Registrasi berhasil! Silakan login dengan akun Anda.';
            } else {
                $error = 'Terjadi kesalahan saat registrasi. Coba lagi.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Inventaris Lab</title>
    <style>
        :root {
            color-scheme: dark;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #eef2ff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at top, rgba(99, 102, 241, .24), transparent 28%),
                        linear-gradient(180deg, #eef2ff 0%, #f9fafb 100%);
            color: #111827;
        }

        .register-shell {
            width: min(420px, calc(100% - 32px));
            padding: 32px;
        }

        .register-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.16);
            overflow: hidden;
        }

        .register-card__header {
            padding: 32px 32px 24px;
            background: linear-gradient(135deg, #4338ca 0%, #2563eb 100%);
            color: #ffffff;
            text-align: center;
        }

        .register-card__header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            letter-spacing: -0.03em;
        }

        .register-card__header p {
            font-size: 0.95rem;
            opacity: .9;
        }

        .register-card__body {
            padding: 32px;
        }

        .register-card__field {
            width: 100%;
            margin-bottom: 18px;
        }

        .register-card__field label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            color: #475569;
        }

        .register-card__field input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            outline: none;
            background: #f8fafc;
            font-size: 1rem;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .register-card__field input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, .12);
            background: #ffffff;
        }

        .register-card__button {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: none;
            background: #4338ca;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform .2s ease, background .2s ease;
        }

        .register-card__button:hover {
            background: #3730a3;
            transform: translateY(-1px);
        }

        .register-card__error {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 0.95rem;
        }

        .register-card__success {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bfdbfe;
            font-size: 0.95rem;
        }

        .register-card__footer {
            padding: 0 32px 32px;
            text-align: center;
            font-size: 0.95rem;
            color: #64748b;
        }

        .register-card__footer a {
            color: #4338ca;
            text-decoration: none;
            font-weight: 600;
            transition: color .2s ease;
        }

        .register-card__footer a:hover {
            color: #3730a3;
        }
    </style>
</head>
<body>
    <div class="register-shell">
        <div class="register-card">
            <div class="register-card__header">
                <h1>Daftar Akun</h1>
                <p>Buat akun baru untuk mengakses sistem inventaris</p>
            </div>

            <div class="register-card__body">
                <?php if($error): ?>
                    <div class="register-card__error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="register-card__success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="register-card__field">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="register-card__field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan email" required>
                    </div>

                    <div class="register-card__field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="register-card__field">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                    </div>

                    <button type="submit" name="register" class="register-card__button">Daftar</button>
                </form>
            </div>

            <div class="register-card__footer">
                Sudah punya akun? <a href="login.php">Masuk di sini</a>
            </div>
        </div>
    </div>
</body>
</html>