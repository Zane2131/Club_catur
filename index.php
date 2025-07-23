<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}
require 'koneksi.php';

$error = '';
$success = false;

$menu = $_GET['menu'] ?? 'daftar';
$username = $_SESSION['username'] ?? 'User';

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
            exit();
        } else {
            $error = "Gagal menyimpan data: " . $stmt->error;
        }

        $stmt->close();
    }
}

$pendaftarList = [];
$result = $koneksi->query("SELECT * FROM pendaftaran ORDER BY id ASC");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendaftarList[] = $row;
    }
}
$jumlahPendaftar = count($pendaftarList);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Organisasi Catur</title>
    <link rel="icon" href="../img/module_table_top.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.13);
            padding: 40px 32px 32px 32px;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .brand {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #222;
            letter-spacing: 2px;
        }
        .user {
            font-size: 1rem;
            color: #555;
            background: #f0d9b5;
            padding: 7px 18px;
            border-radius: 20px;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        nav.navbar {
            display: flex;
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
        .navbar a.logout {
            margin-left: auto;
            color: #f44336;
            background: #fff;
            border: 1.5px solid #f44336;
            transition: background 0.2s, color 0.2s;
        }
        .navbar a.logout:hover {
            background: #f44336;
            color: #fff;
        }
        .message { margin: 10px auto; max-width: 600px; padding: 10px; border-radius: 7px; font-size: 1.05rem; }
        .error { background: #f8d7da; color: #721c24; border: 1.5px solid #f5c6cb; }
        .success { background: #d4edda; color: #155724; border: 1.5px solid #c3e6cb; }
        form {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 30px;
        }
        form label {
            font-weight: 500;
            margin-bottom: 2px;
        }
        form input, form textarea {
            padding: 10px 12px;
            border: 1.5px solid #b58863;
            border-radius: 7px;
            font-size: 1rem;
            font-family: 'Roboto', Arial, sans-serif;
            margin-top: 4px;
            background: #f7f7f7;
            transition: border 0.2s;
        }
        form input:focus, form textarea:focus {
            border: 1.5px solid #222;
            outline: none;
        }
        form textarea {
            min-height: 60px;
            resize: vertical;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }
        button[type="submit"], button[type="reset"] {
            background: #b58863;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 7px;
            font-size: 1rem;
            font-family: 'Montserrat', Arial, sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(181,136,99,0.08);
        }
        button[type="submit"]:hover, button[type="reset"]:hover {
            background: #222;
            color: #fff;
        }
        .info-pendaftar {
            margin: 18px 0 10px 0;
            font-size: 1.08rem;
            color: #555;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 18px;
        }
        th, td {
            padding: 14px 12px;
            text-align: left;
        }
        th {
            background: #222;
            color: #fff;
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
        }
        @media (max-width: 700px) {
            .container { padding: 18px 4vw; }
            nav.navbar { flex-direction: column; gap: 10px; padding: 10px 0; }
            header { flex-direction: column; gap: 8px; align-items: flex-start; }
            table, thead, tbody, th, td, tr { display: block; }
            th, td { padding: 10px 6px; }
            tr { margin-bottom: 10px; }
            th { background: #222; color: #fff; }
        }
    </style>
</head>

<script>
document.querySelector("form").addEventListener("submit", function(e) {
    const email = document.querySelector('input[name="email"]').value;
    const nim = document.querySelector('input[name="nim"]').value;

    if (!/^[0-9]{8,}$/.test(nim)) {
        alert("NIM harus berupa angka minimal 8 digit.");
        e.preventDefault();
    } else if (!email.includes("@")) {
        alert("Format email tidak valid.");
        e.preventDefault();
    }
});
</script>


<body>
<div class="container">
    <header>
        <div class="brand">&#9812; Organisasi Catur</div>
        <div class="user">👤 <?= htmlspecialchars($username) ?></div>
    </header>
    </header>
     <nav class="navbar">
        <a href="?menu=daftar" class="<?= $menu === 'daftar' ? 'active' : '' ?>">&#9817; Daftar</a>
        <a href="?menu=tentang" class="<?= $menu === 'tentang' ? 'active' : '' ?>">&#9812; Tentang Kami</a>
        <a href="?menu=pelatih" class="<?= $menu === 'pelatih' ? 'active' : '' ?>">&#9818; Pelatih</a>
        <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin logout?')">🚪 Logout</a>
     </nav>


    <?php if ($menu === 'daftar'): ?>
        <h2 style="margin-top:0">&#9812; Formulir Pendaftaran Organisasi Catur</h2>
        <?php if ($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?><div class="message success">Pendaftaran berhasil!</div><?php endif; ?>

        <form method="post" autocomplete="off">
            <label>Nama Lengkap:
                <input type="text" name="nama" required placeholder="Masukkan nama lengkap">
            </label>
            <label>NIM:
                <input type="text" name="nim" required placeholder="Masukkan NIM">
            </label>
            <label>Fakultas:
                <input type="text" name="fakultas" required placeholder="Masukkan fakultas">
            </label>
            <label>Email:
                <input type="email" name="email" required placeholder="Masukkan email aktif">
            </label>
            <label>Alasan Bergabung:
                <textarea name="alasan" required placeholder="Mengapa ingin bergabung?"></textarea>
            </label>
            <div class="form-actions">
                <button type="submit" name="daftar">&#9817; Daftar</button>
                <button type="reset">Reset</button>
            </div>
        </form>

        <div class="info-pendaftar">Jumlah pendaftar: <b><?= $jumlahPendaftar ?></b></div>
        <h2>&#9818; Daftar Pendaftar Organisasi Catur</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Fakultas</th>
                <th>Email</th>
                <th>Alasan</th>
                <th>Aksi</th>
            </tr>
            <?php if (empty($pendaftarList)): ?>
                <tr><td colspan="7" align="center" style="background:#fff; color:#222;">Belum ada pendaftar.</td></tr>
            <?php else: ?>
                <?php foreach ($pendaftarList as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($p['nama']) ?></td>
                        <td><?= htmlspecialchars($p['nim']) ?></td>
                        <td><?= htmlspecialchars($p['fakultas']) ?></td>
                        <td><?= htmlspecialchars($p['email']) ?></td>
                        <td><?= htmlspecialchars($p['alasan']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $p['id'] ?>" style="color:#1976d2; text-decoration:underline;">Edit</a>
                            |
                            <a href="delete.php?id=<?= $p['id'] ?>" style="color:#d32f2f; text-decoration:underline;" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>

    <?php elseif ($menu === 'tentang'): ?>
        <h2>&#9812; Tentang Kami</h2>
        <section>
        <p>Organisasi Catur Kampus adalah komunitas pecinta catur yang bertujuan untuk mengembangkan minat, bakat, dan prestasi mahasiswa dalam bidang catur. Kami rutin mengadakan pelatihan, sparring, dan turnamen internal maupun eksternal.</p>
        </section>
    <?php elseif ($menu === 'pelatih'): ?>
        <h2>&#9818; Pelatih Organisasi Catur</h2>
        <section>
        <ul>
            <li><b>GM Andi Pratama</b> – Grandmaster, Juara Nasional 2022</li>
            <li><b>IM Siti Rahmawati</b> – International Master, Alumni Fasilkom</li>
            <li><b>Coach Budi Santoso</b> – Pelatih Senior dan pembimbing anggota baru</li>
        </ul>
        </section>
    <?php endif; ?>
    <footer style="margin-top:40px; text-align:center; color:#888; font-size:0.98rem;">
        &copy; <?= date('Y') ?> Organisasi Catur Kampus
    </footer>
</div>
</body>
</html>
