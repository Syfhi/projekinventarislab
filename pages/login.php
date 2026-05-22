<?php
session_start();
include '../config/koneksi.php';

$error = '';

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
            exit;
        } else {
            $error = 'Password salah. Periksa kembali password Anda.';
        }
    } else {
        $error = 'Email tidak ditemukan. Silakan cek kembali atau daftar terlebih dahulu.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventaris Lab</title>
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

        .login-shell {
            width: min(420px, calc(100% - 32px));
            padding: 32px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.16);
            overflow: hidden;
        }

        .login-card__header {
            padding: 32px 32px 24px;
            background: linear-gradient(135deg, #4338ca 0%, #2563eb 100%);
            color: #ffffff;
            text-align: center;
        }

        .login-card__header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            letter-spacing: -0.03em;
        }

        .login-card__header p {
            font-size: 0.95rem;
            opacity: .9;
        }

        .login-card__body {
            padding: 32px;
        }

        .login-card__field {
            width: 100%;
            margin-bottom: 18px;
        }

        .login-card__field label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            color: #475569;
        }

        .login-card__field input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            outline: none;
            background: #f8fafc;
            font-size: 1rem;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .login-card__field input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, .12);
            background: #ffffff;
        }

        .login-card__button {
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

        .login-card__button:hover {
            background: #3730a3;
            transform: translateY(-1px);
        }

        .login-card__error {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 0.95rem;
        }

        .login-card__footer {
            margin-top: 22px;
            text-align: center;
            font-size: 0.95rem;
            color: #64748b;
        }

        .login-card__footer a {
            color: #4338ca;
            text-decoration: none;
            font-weight: 600;
        }

        .login-card__footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-card {
                border-radius: 22px;
            }

            .login-card__header,
            .login-card__body {
                padding-left: 24px;
                padding-right: 24px;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="login-card">
            <div class="login-card__header">
                <h1>Login Inventaris Lab</h1>
                <p>Masuk untuk mengelola data inventaris ruang laboratorium.</p>
            </div>
            <div class="login-card__body">
                <?php if($error): ?>
                    <div class="login-card__error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" autocomplete="on">
                    <div class="login-card__field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="login-card__field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" placeholder="Password" required>
                    </div>

                    <button type="submit" name="login" class="login-card__button">Masuk</button>
                </form>

                <div class="login-card__footer">
                    Belum punya akun? <a href="register.php">Daftar sekarang</a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>