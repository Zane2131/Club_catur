<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['daftar'])) {
    $nama = htmlspecialchars(trim($_POST['nama']));
    $nim      = trim($_POST['nim']);
    $fakultas = trim($_POST['fakultas']);
    $email    = trim($_POST['email']);
    $alasan   = trim($_POST['alasan']);

    if (!$nama || !$nim || !$fakultas || !$email || !$alasan) {
        header("Location: form.php?error=" . urlencode("Semua field harus diisi!"));
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: form.php?error=" . urlencode("Format email tidak valid!"));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO pendaftar (nama, nim, fakultas, email, alasan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $nim, $fakultas, $email, $alasan);
    
    if ($stmt->execute()) {
        header("Location: form.php?success=1");
    } else {
        header("Location: form.php?error=" . urlencode("Gagal menyimpan data."));
    }
    $stmt->close();
}
?>
