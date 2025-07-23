<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit;
}
require 'koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $keahlian = trim($_POST['keahlian']);

    if (empty($nama) || empty($keahlian)) {
        $error = "Semua field harus diisi.";
    } else {
        $stmt = $koneksi->prepare("INSERT INTO coach (nama, keahlian) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $nama, $keahlian);
            if ($stmt->execute()) {
                $success = "Coach berhasil ditambahkan.";
            } else {
                $error = "Gagal menyimpan coach: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Query error: " . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Coach</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">➕ Tambah Coach Baru</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Coach:</label>
            <input type="text" name="nama" id="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="keahlian" class="form-label">Keahlian:</label>
            <textarea name="keahlian" id="keahlian" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </form>
</div>
</body>
</html>
