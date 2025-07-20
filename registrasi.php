<?php
require 'koneksi.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['user'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validasi sederhana
    if (empty($user) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Semua kolom wajib diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Cek apakah username sudah digunakan
        $check = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Username sudah terdaftar.";
        } else {
            // Simpan data baru
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, user, password) VALUES (?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $username, $user, $hashed_password);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Registrasi berhasil. <a href='Login.php'>Login sekarang</a>.";
                } else {
                    $error = "Registrasi gagal: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Query gagal: " . mysqli_error($koneksi);
            }
        }

        mysqli_stmt_close($check);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - Organisasi Catur</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f7f7f7 60%, #e2e2e2 100%);
            font-family: 'Roboto', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 40px 36px 32px 36px;
            max-width: 420px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        .chess-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            opacity: 0.08;
            z-index: 0;
            background-image:
                linear-gradient(45deg, #222 25%, transparent 25%, transparent 75%, #222 75%, #222),
                linear-gradient(45deg, #222 25%, transparent 25%, transparent 75%, #222 75%, #222);
            background-size: 48px 48px;
            background-position: 0 0, 24px 24px;
        }
        .register-header {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 2.1rem;
            text-align: center;
            margin-bottom: 18px;
            color: #222;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .chess-icon {
            width: 36px;
            height: 36px;
            vertical-align: middle;
        }
        form {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        label {
            font-weight: 600;
            color: #222;
            margin-bottom: 6px;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #222;
            border-radius: 8px;
            font-size: 1rem;
            background: #f8f8f8;
            transition: border 0.2s;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #bfa14a;
            outline: none;
            background: #fffbe6;
        }
        button[type="submit"] {
            background: linear-gradient(90deg, #222 70%, #bfa14a 100%);
            color: #fff;
            font-family: 'Montserrat', Arial, sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(34,34,34,0.08);
            transition: background 0.2s, transform 0.1s;
        }
        button[type="submit"]:hover {
            background: linear-gradient(90deg, #bfa14a 0%, #222 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .error-message {
            color: #c0392b;
            background: #fff0f0;
            border: 1px solid #c0392b;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 600;
            font-family: 'Montserrat', Arial, sans-serif;
            position: relative;
            z-index: 1;
        }
        .success-message {
            color: #27ae60;
            background: #f0fff0;
            border: 1px solid #27ae60;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 600;
            font-family: 'Montserrat', Arial, sans-serif;
            position: relative;
            z-index: 1;
        }
        .login-link {
            text-align: center;
            margin-top: 18px;
            font-size: 1rem;
            color: #222;
            position: relative;
            z-index: 1;
        }
        .login-link a {
            color: #bfa14a;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }
        .login-link a:hover {
            color: #222;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="chess-bg"></div>
        <div class="register-header">
            <svg class="chess-icon" viewBox="0 0 32 32" fill="none">
                <rect x="6" y="24" width="20" height="4" rx="2" fill="#bfa14a"/>
                <rect x="10" y="8" width="12" height="16" rx="3" fill="#222"/>
                <rect x="13" y="4" width="6" height="4" rx="2" fill="#bfa14a"/>
                <circle cx="16" cy="3" r="2" fill="#222"/>
            </svg>
            Form Registrasi
        </div>
        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?= $success ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <label for="user">Nama Lengkap:</label>
            <input type="text" name="user" id="user" required>

            <label for="username">Username (email):</label>
            <input type="email" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Daftar</button>
        </form>
        <div class="login-link">
            Sudah punya akun? <a href="Login.php">Login di sini</a>
        </div>
    </div>
</body>
</html>
