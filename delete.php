<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}

require 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php?menu=daftar&error=invalid_id");
    exit;
}

$stmt = $koneksi->prepare("DELETE FROM pendaftaran WHERE id = ?");
if (!$stmt) {
    die("Prepare statement gagal: " . $koneksi->error);
}
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?menu=daftar&deleted=1");
    exit;
} else {
    echo "Gagal menghapus data: " . $stmt->error;
}
