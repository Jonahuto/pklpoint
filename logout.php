<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $waktu_keluar = date('Y-m-d H:i:s');

    // Update log_aktivitas: set logout_time dan status Tidak Aktif
    $sql = "UPDATE log_aktivitas SET waktu_keluar = ?, status = 'Tidak Aktif' WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("si", $waktu_keluar, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}





session_destroy();
header("Location: login.php");
exit();
?>