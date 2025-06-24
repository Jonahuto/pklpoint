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

    // Validate NIM and Password
    if (empty($nama_lengkap) || empty($nim) || empty($email) ||  empty($password) || empty($konfirmasi_password)) {
        echo "Nama Lengkap, NIM, E-Mail, Password, dan Konfirmasi Password tidak boleh kosong!";
    } elseif ($password !== $konfirmasi_password) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement
        $sql = "INSERT INTO users (nama_lengkap, nim, email,  password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("ssss", $nama_lengkap, $nim, $email, $hashedPassword);

            // Execute the statement
            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
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
            <form class="register-form" action="register.php" method="POST" autocomplete="off" novalidate>
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" aria-label="Nama Lengkap" required />
                <input type="text" name="nim" placeholder="NIM" aria-label="NIM" required />
                <input type="text" name="email" placeholder="E-Mail" aria-label="E-Mail" required />
                <input type="password" name="password" placeholder="Password" aria-label="Password">
                <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Password" aria-label="Konfirmasi Password">
                <button type="submit">Buat Akun</button>
            </form>
            <p class="signup-text">
                Sudah punya akun? <a href="login.php" aria-label="Daftar akun baru">Login</a>
            </p>
        </section>
    </main>

</body>

</html>