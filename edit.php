<?php
require 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$result = $koneksi->query("SELECT * FROM pendaftaran WHERE id = $id");
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $fakultas = $_POST['fakultas'];
    $email = $_POST['email'];
    $alasan = $_POST['alasan'];

    $stmt = $koneksi->prepare("UPDATE pendaftaran SET nama=?, nim=?, fakultas=?, email=?, alasan=? WHERE id=?");
    $stmt->bind_param("sssssi", $nama, $nim, $fakultas, $email, $alasan, $id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-warning">Edit Data</div>
        <div class="card-body">
            <form method="post">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" value="<?= $data['nama'] ?>" class="form-control" required>

                <label class="form-label mt-2">NIM</label>
                <input type="text" name="nim" value="<?= $data['nim'] ?>" class="form-control" required>

                <label class="form-label mt-2">Fakultas</label>
                <input type="text" name="fakultas" value="<?= $data['fakultas'] ?>" class="form-control" required>

                <label class="form-label mt-2">Email</label>
                <input type="email" name="email" value="<?= $data['email'] ?>" class="form-control" required>

                <label class="form-label mt-2">Alasan</label>
                <textarea name="alasan" class="form-control" required><?= $data['alasan'] ?></textarea>

                <button type="submit" class="btn btn-success mt-3">Simpan</button>
                <a href="index.php" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
