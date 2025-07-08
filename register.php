<?php
session_start();
require 'config.php'; // Include the database connection

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $nim = trim($_POST['nim']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);

    // Validate required fields
    if (empty($nama_lengkap) || empty($nim) || empty($email) || empty($password) || empty($konfirmasi_password)) {
        $error = "Nama Lengkap, NIM, E-Mail, Password, dan Konfirmasi Password tidak boleh kosong!";
    } elseif ($password !== $konfirmasi_password) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Cek apakah NIM sudah terdaftar
        $sql_cek_nim = "SELECT user_id FROM users WHERE nim = ?";
        $stmt_cek = $conn->prepare($sql_cek_nim);
        $stmt_cek->bind_param("s", $nim);
        $stmt_cek->execute();
        $stmt_cek->store_result();

        if ($stmt_cek->num_rows > 0) {
            $error = "NIM sudah terdaftar. Silakan gunakan NIM lain.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare insert statement
            $sql = "INSERT INTO users (nama_lengkap, nim, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssss", $nama_lengkap, $nim, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $user_id = $stmt->insert_id;

                    // Insert default activity status
                    $sql_log = "INSERT INTO log_aktivitas (user_id, status) VALUES (?, 'Tidak Aktif')";
                    $stmt_log = $conn->prepare($sql_log);
                    $stmt_log->bind_param("i", $user_id);
                    $stmt_log->execute();
                    $stmt_log->close();

                    header("Location: login.php?=success=1");
                    exit();
                } else {
                    $error = "Gagal mendaftarkan akun: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Gagal menyiapkan statement: " . $conn->error;
            }
        }

        $stmt_cek->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Point - Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="css/register.css">
</head>

<body>
    <header>
        <div class="head">
            <span>Registrasi Akun</span>
        </div>
    </header>
    <main>
        <section class="register-card" aria-label="Form Register PKLPoint">
            <h2>Daftar</h2>
            <?php if (!empty($error)): ?>
                <div style="color: red; margin-bottom: 10px;"><?php echo $error; ?></div>
            <?php endif; ?>
            <form class="register-form" action="register.php" method="POST" autocomplete="off">
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" aria-label="Nama Lengkap" required />
                <input type="text" name="nim" placeholder="NIM" pattern="[0-9]+" aria-label="NIM" title="NIM hanya boleh angka" required />
                <input type="email" name="email" placeholder="E-Mail" aria-label="E-Mail" required />
                <input type="password" name="password" placeholder="Password" aria-label="Password" required>
                <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Password" aria-label="Konfirmasi Password" required>
                <button type="submit">Buat Akun</button>
            </form>
            <p class="signup-text">
                Sudah punya akun? <a href="login.php" aria-label="Daftar akun baru">Login</a>
            </p>
        </section>
    </main>

</body>

</html>