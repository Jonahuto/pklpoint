<?php
session_start();
require '../config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek role
if ($_SESSION['role'] !== 'mahasiswa') {
    echo "Akses ditolak.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID tidak valid.";
    exit();
}

$id_kegiatan = intval($_GET['id']);

// Ambil file terkait
$stmt = $conn->prepare("SELECT file FROM kegiatan WHERE id_kegiatan = ? AND id_user = ?");
$stmt->bind_param("ii", $id_kegiatan, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Data tidak ditemukan atau bukan milik Anda.";
    exit();
}

$data = $result->fetch_assoc();
$file = $data['file'];

// Hapus dari database
$delete = $conn->prepare("DELETE FROM kegiatan WHERE id_kegiatan = ? AND id_user = ?");
$delete->bind_param("ii", $id_kegiatan, $user_id);
$delete->execute();
$delete->close();

// Hapus file jika ada
if (!empty($file) && file_exists("uploads/$file")) {
    unlink("uploads/$file");
}

header("Location: kegiatan.php");
exit();
?>
