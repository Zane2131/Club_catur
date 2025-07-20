<?php
session_start();
require 'koneksi.php';

$error = '';

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $query = mysqli_prepare($koneksi, "SELECT user, password FROM users WHERE username = ?");
    if (!$query) {
        die("Query error: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);
    mysqli_stmt_bind_result($query, $user, $hashed_password);

    if (mysqli_stmt_fetch($query)) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION["username"] = $username;
            $_SESSION["user"] = $user; // ← simpan nama lengkap
            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }

    mysqli_stmt_close($query);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Organisasi Catur</title>
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
        .login-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 40px 36px 32px 36px;
            max-width: 400px;
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
        .login-header {
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
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #222;
            border-radius: 8px;
            font-size: 1rem;
            background: #f8f8f8;
            transition: border 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
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
        .register-link {
            text-align: center;
            margin-top: 18px;
            font-size: 1rem;
            color: #222;
            position: relative;
            z-index: 1;
        }
        .register-link a {
            color: #bfa14a;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }
        .register-link a:hover {
            color: #222;
            text-decoration: underline;
        }
        @media (max-width: 500px) {
            .login-container {
                padding: 24px 8px 18px 8px;
            }
            .login-header {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="chess-bg"></div>
        <div class="login-header">
            <svg class="chess-icon" viewBox="0 0 32 32" fill="none">
                <ellipse cx="16" cy="28" rx="10" ry="3" fill="#bfa14a"/>
                <path d="M10 26h12l-1-8h-10l-1 8z" fill="#222"/>
                <path d="M12 18v-3a4 4 0 1 1 8 0v3" stroke="#bfa14a" stroke-width="2" fill="none"/>
                <circle cx="16" cy="10" r="3" fill="#bfa14a" stroke="#222" stroke-width="2"/>
            </svg>
            Login Organisasi Catur
        </div>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div>
                <label for="username">Username (email):</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            Belum punya akun? <a href="registrasi.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
