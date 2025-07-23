<?php
session_start();
require 'koneksi.php';

// Cek apakah sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Organisasi Catur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🎓 Dashboard Admin</h2>
            <a href="logout.php" class="btn btn-danger" onclick="return confirm('Yakin ingin logout?')">Logout</a>
        </div>

        <div class="mb-3">
            <a href="tambah_coach.php" class="btn btn-success me-2">➕ Tambah Coach</a>
            <a href="assign_coach.php" class="btn btn-warning">🧩 Assign Coach ke Client</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <strong>📋 Daftar Pendaftar</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Fakultas</th>
                                <th>Email</th>
                                <th>Alasan</th>
                                <th>Coach</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.*, c.nama AS coach 
                                    FROM pendaftaran p 
                                    LEFT JOIN coach c ON p.coach_id = c.id";
                            $result = $koneksi->query($sql);
                            if ($result && $result->num_rows > 0):
                                $i = 1;
                                while ($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['nim']) ?></td>
                                <td><?= htmlspecialchars($row['fakultas']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['alasan']) ?></td>
                                <td><?= htmlspecialchars($row['coach'] ?? '-') ?></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data pendaftar.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
