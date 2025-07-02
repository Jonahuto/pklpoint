<?php
require '../config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Hapus data
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: kelolamahasiswa.php?hapus=1"); // arahkan ke halaman daftar
            exit();
        } else {
            header("Location: kelolamahasiswa.php?gagal=1"); // arahkan ke halaman daftar
            exit();
        }
    } else {
        echo "Query error: " . $conn->error;
    }
} else {
    echo "ID tidak ditemukan.";
}
?>
