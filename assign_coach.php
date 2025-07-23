<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';

$error = '';
$success = '';

// Tangani form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pendaftar = $_POST['id_pendaftar'] ?? null;
    $coach_id = $_POST['coach_id'] ?? null;

    if (!$id_pendaftar || !$coach_id) {
        $error = "Silakan pilih pendaftar dan coach.";
    } else {
        $stmt = $koneksi->prepare("UPDATE pendaftaran SET coach_id = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $coach_id, $id_pendaftar);
            if ($stmt->execute()) {
                $success = "Coach berhasil di-assign ke pendaftar.";
            } else {
                $error = "Gagal assign coach: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Query error: " . $koneksi->error;
        }
    }
}

// Ambil data pendaftar dan coach untuk dropdown
$pendaftar = $koneksi->query("SELECT id, nama FROM pendaftaran");
$coach = $koneksi->query("SELECT id, nama FROM coach");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Assign Coach</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">🧩 Assign Coach ke Pendaftar</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="id_pendaftar" class="form-label">Pilih Pendaftar:</label>
            <select name="id_pendaftar" id="id_pendaftar" class="form-select" required>
                <option value="">-- Pilih Pendaftar --</option>
                <?php while ($row = $pendaftar->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="coach_id" class="form-label">Pilih Coach:</label>
            <select name="coach_id" id="coach_id" class="form-select" required>
                <option value="">-- Pilih Coach --</option>
                <?php while ($row = $coach->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Assign</button>
        <a href="admin.php" class="btn btn-secondary">⬅ Kembali</a>
    </form>
</div>
</body>
</html>
