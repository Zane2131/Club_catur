<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}
require 'koneksi.php';

$error = '';
$success = false;

// Menu logic
$menu = isset($_GET['menu']) ? $_GET['menu'] : 'daftar';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['daftar'])) {
    $nama     = trim($_POST['nama']);
    $nim      = trim($_POST['nim']);
    $fakultas = trim($_POST['fakultas']);
    $email    = trim($_POST['email']);
    $alasan   = trim($_POST['alasan']);

    if (!$nama || !$nim || !$fakultas || !$email || !$alasan) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        $stmt = $koneksi->prepare("INSERT INTO pendaftaran (nama, nim, fakultas, email, alasan) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Prepare gagal: " . $koneksi->error);
        }

        $stmt->bind_param("sssss", $nama, $nim, $fakultas, $email, $alasan);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit(); // Penting: agar kode di bawah tidak jalan lagi
        }
         else {
            $error = "Gagal menyimpan data: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<?php
$pendaftarList = [];
$result = $koneksi->query("SELECT * FROM pendaftaran ORDER BY id ASC");


if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendaftarList[] = $row;
    }
}
?>

<?php
$pendaftarList = [];
$result = $koneksi->query("SELECT * FROM pendaftaran ORDER BY id ASC");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendaftarList[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Organisasi Catur</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #f7f7f7 60%, #e2e2e2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 40px 32px 32px 32px;
        }
        h2 {
            font-family: 'Montserrat', Arial, sans-serif;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: 1px;
            color: #222;
            text-shadow: 1px 1px 0 #e2e2e2;
        }
        form {
            max-width: 600px;
            margin: 0 auto 40px auto;
            background: #f8f8f8;
            padding: 28px 32px 20px 32px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 2px solid #222;
            position: relative;
        }
        form:before {
            content: "";
            position: absolute;
            left: 0; top: 0; right: 0; bottom: 0;
            border-radius: 12px;
            pointer-events: none;
            background: repeating-linear-gradient(
                135deg,
                #222 0 8px,
                #fff 8px 16px
            );
            opacity: 0.04;
            z-index: 0;
        }
        label {
            display: block;
            margin-top: 18px;
            font-weight: 600;
            color: #222;
            letter-spacing: 0.5px;
        }
        input, textarea {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1.5px solid #bdbdbd;
            border-radius: 6px;
            font-size: 1rem;
            background: #fff;
            box-sizing: border-box;
            transition: border 0.2s;
            font-family: inherit;
        }
        input:focus, textarea:focus {
            border: 1.5px solid #222;
            outline: none;
            background: #f0f0f0;
        }
        button {
            margin-top: 24px;
            padding: 12px 32px;
            background: linear-gradient(90deg, #222 60%, #b58863 100%);
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(34,34,34,0.08);
            letter-spacing: 1px;
            transition: background 0.2s, transform 0.1s;
        }
        button:hover {
            background: linear-gradient(90deg, #b58863 0%, #222 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .message {
            margin: 0 auto 24px auto;
            max-width: 600px;
            padding: 12px 20px;
            border-radius: 7px;
            font-size: 1.05rem;
            font-weight: 500;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1.5px solid #f5c6cb;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1.5px solid #c3e6cb;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 30px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }
        th, td {
            padding: 14px 12px;
            text-align: left;
            font-size: 1rem;
        }
        th {
            background: #222;
            color: #fff;
            font-family: 'Montserrat', Arial, sans-serif;
            letter-spacing: 1px;
        }
        tr:nth-child(even) td {
            background: #f0d9b5;
        }
        tr:nth-child(odd) td {
            background: #b58863;
            color: #fff;
        }
        tr:hover td {
            background: #e6e6e6 !important;
            color: #222 !important;
            transition: background 0.2s, color 0.2s;
        }
        .chess-icon {
            font-size: 2.1rem;
            vertical-align: middle;
            margin-right: 10px;
            color: #b58863;
            text-shadow: 1px 1px 0 #222, 0 0 2px #fff;
        }
        /* Tambahan untuk menu */
        .navbar {
            display: flex;
            justify-content: center;
            gap: 32px;
            background: #222;
            padding: 18px 0 10px 0;
            border-radius: 12px 12px 0 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 6px 18px;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }
        .navbar a.active, .navbar a:hover {
            background: #b58863;
            color: #222;
        }
        @media (max-width: 700px) {
            .container { padding: 10px 2vw; }
            form { padding: 16px 6vw; }
            table, th, td { font-size: 0.95rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <nav class="navbar">
        <a href="?menu=daftar" class="<?= $menu === 'daftar' ? 'active' : '' ?>">&#9817; Daftar</a>
        <a href="?menu=tentang" class="<?= $menu === 'tentang' ? 'active' : '' ?>">&#9812; Tentang Kami</a>
        <a href="?menu=pelatih" class="<?= $menu === 'pelatih' ? 'active' : '' ?>">&#9818; Pelatih</a>
    </nav>

    <?php if ($menu === 'daftar'): ?>
        <h2><span class="chess-icon">&#9812;</span>Formulir Pendaftaran Organisasi Catur</h2>
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="message success">Pendaftaran berhasil!</div>
        <?php endif; ?>
        <form method="post" action="">
            <label>Nama Lengkap:
                <input type="text" name="nama" required>
            </label>
            <label>NIM:
                <input type="text" name="nim" required>
            </label>
            <label>Fakultas:
                <input type="text" name="fakultas" required>
            </label>
            <label>Email:
                <input type="email" name="email" required>
            </label>
            <label>Alasan Bergabung:
                <textarea name="alasan" required></textarea>
            </label>
            <button type="submit" name="daftar">&#9817; Daftar</button>
        </form>
        <h2><span class="chess-icon">&#9818;</span>Daftar Pendaftar Organisasi Catur</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Fakultas</th>
                <th>Email</th>
                <th>Alasan</th>
            </tr>
            <?php if (empty($pendaftarList)): ?>
                <tr><td colspan="6" align="center" style="background:#fff; color:#222;">Belum ada pendaftar.</td></tr>
            <?php else: ?>
                <?php foreach ($pendaftarList as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($p['nama']) ?></td>
                        <td><?= htmlspecialchars($p['nim']) ?></td>
                        <td><?= htmlspecialchars($p['fakultas']) ?></td>
                        <td><?= htmlspecialchars($p['email']) ?></td>
                        <td><?= htmlspecialchars($p['alasan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    <?php elseif ($menu === 'tentang'): ?>
        <h2><span class="chess-icon">&#9812;</span>Tentang Kami</h2>
        <div style="max-width:700px;margin:0 auto 30px auto;font-size:1.1rem;line-height:1.7;background:#f8f8f8;padding:28px 32px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);border:2px solid #222;">
            <p>Organisasi Catur Kampus adalah komunitas pecinta catur yang bertujuan untuk mengembangkan minat, bakat, dan prestasi mahasiswa dalam bidang catur. Kami rutin mengadakan pelatihan, sparring, dan turnamen internal maupun eksternal. Bergabunglah bersama kami untuk meningkatkan kemampuan berpikir strategis, memperluas relasi, dan meraih prestasi di dunia catur!</p>
        </div>
    <?php elseif ($menu === 'pelatih'): ?>
        <h2><span class="chess-icon">&#9818;</span>Pelatih Organisasi Catur</h2>
        <div style="max-width:700px;margin:0 auto 30px auto;font-size:1.1rem;line-height:1.7;background:#f8f8f8;padding:28px 32px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);border:2px solid #222;">
            <ul style="list-style:none;padding:0;">
                <li style="margin-bottom:22px;">
                    <b>GM Andi Pratama</b><br>
                    <span style="color:#b58863;">Grandmaster, Juara Nasional 2022</span><br>
                    Berpengalaman melatih tim catur kampus dan membawa tim meraih juara nasional. Spesialis strategi pembukaan dan middle game.
                </li>
                <li style="margin-bottom:22px;">
                    <b>IM Siti Rahmawati</b><br>
                    <span style="color:#b58863;">International Master, Alumni Fasilkom</span><br>
                    Fokus pada pengembangan bakat muda dan pelatihan teknik endgame. Pernah mewakili Indonesia di Olimpiade Catur.
                </li>
                <li>
                    <b>Coach Budi Santoso</b><br>
                    <span style="color:#b58863;">Pelatih Senior</span><br>
                    Ahli dalam pelatihan dasar-dasar catur dan membimbing anggota baru agar cepat berkembang.
                </li>
            </ul>
        </div>
    <?php endif; ?>
</div>
</body>
</html>