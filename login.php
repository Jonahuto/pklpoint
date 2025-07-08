<?php
session_start();
require 'config.php'; // Include your DB connection here

$error = ''; // Initialize error message variable

// Redirect if already logged in
if (isset($_SESSION['nim']) && isset($_SESSION['role'])) {
    // Redirect based on role
    switch ($_SESSION['role']) {
        case 'mahasiswa':
            header("Location: mhs/dashboard.php");
            exit();
        case 'dosen':
            header("Location: dosen/dashboard.php");
            exit();
        case 'admin':
            header("Location: admin/dashboard.php");
            exit();
        default:
            // Invalid role - destroy session and show error
            session_destroy();
            $error = "Role tidak valid.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $nim = trim($_POST['nim']);
    $password = trim($_POST['password']);

    if (empty($nim) || empty($password)) {
        $error = "NIM dan Password tidak boleh kosong!";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $sql = "SELECT user_id, password, role, nama_lengkap FROM users WHERE nim = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            $error = "Terjadi kesalahan server. Silakan coba lagi.";
        } else {
            $stmt->bind_param("s", $nim);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Verify hashed password
                if (password_verify($password, $row['password'])) {
                    // Set session variables
                    $_SESSION['nim'] = $nim;
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                    $_SESSION['user_id'] = $row['user_id'];

                    // Update log_aktivitas: set login_time dan status Aktif
                    $user_id = $row['user_id'];
                    $now = date('Y-m-d H:i:s');

                    // Cek apakah sudah ada log_aktivitas untuk user ini
                    $cek_log = "SELECT id FROM log_aktivitas WHERE user_id = ?";
                    $stmt_cek_log = $conn->prepare($cek_log);
                    $stmt_cek_log->bind_param("i", $user_id);
                    $stmt_cek_log->execute();
                    $stmt_cek_log->store_result();

                    if ($stmt_cek_log->num_rows > 0) {
                        // Update log lama
                        $update_log = "UPDATE log_aktivitas SET waktu_masuk = ?, status = 'Aktif', waktu_keluar = NULL WHERE user_id = ?";
                        $stmt_update = $conn->prepare($update_log);
                        $stmt_update->bind_param("si", $now, $user_id);
                        $stmt_update->execute();
                        $stmt_update->close();
                    } else {
                        // Insert log baru (jika belum ada)
                        $insert_log = "INSERT INTO log_aktivitas (user_id, waktu_masuk, status) VALUES (?, ?, 'Aktif')";
                        $stmt_insert = $conn->prepare($insert_log);
                        $stmt_insert->bind_param("is", $user_id, $now);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                    }
                    $stmt_cek_log->close();

                    // Redirect based on role
                    switch ($row['role']) {
                        case 'mahasiswa':
                            header("Location: mhs/dashboard.php");
                            exit();
                        case 'dosen':
                            header("Location: dosen/dashboard.php");
                            exit();
                        case 'admin':
                            header("Location: admin/dashboard.php");
                            exit();
                        default:
                            session_destroy();
                            $error = "Role tidak valid.";
                    }
                } else {
                    $error = "Password salah!";
                }
            } else {
                $error = "NIM tidak ditemukan!";
            }
            $stmt->close();
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Point - Login</title>
    <link rel="stylesheet" href="css/login-mhs.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
</head>

<body>
    <?php if (isset($_GET['success'])): ?>
        <div id="success-register-message" class="popup success">
            Berhasil mendaftar.
        </div>
    <?php endif; ?>
    <header>
        <div class="logo">
            <span class="material-icons" aria-hidden="true"><svg height="75" width="75" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                    <path d="M220 112c0 23.6-8.29 45.23-23.35 60.88C182.52 187.57 163.33 196 144 196c-15.45 0-26.78-4.18-34.89-9.31l-9.43 40.06a12 12 0 1 1-23.36-5.5l32-136a12 12 0 1 1 23.36 5.5l-16.45 69.93C118.72 164.86 127.16 172 144 172c25.56 0 52-22.45 52-60a68 68 0 1 0-126.91 34a12 12 0 0 1-20.77 12A92 92 0 1 1 220 112Z" fill="currentColor" />
                </svg></span>
            PKLPoint
        </div>
    </header>

    <main>
        <section class="login-card" aria-label="Form login PKLPoint">
            <h2>Login</h2>
            <form class="login-form" action="login.php" method="POST" autocomplete="off" novalidate>
                <input type="text" name="nim" placeholder="NIM / NIP" aria-label="NIM" required />
                <input type="password" name="password" placeholder="Password" aria-label="Password" required />
                <?php if ($error): ?>
                    <p class="error-message" style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <button type="submit">Masuk</button>
            </form>
            <p class="signin-text">
                Belum punya akun? <a href="register.php" aria-label="Daftar akun baru">Daftar</a>
            </p>
        </section>
    </main>

    <!-- <footer>
        <div>© 2024 PKLPoint. All rights reserved.</div>
        <div class="social-links" aria-label="Tautan media sosial">
            <a href="#" aria-label="Facebook" title="Facebook">
                <span class="material-icons" aria-hidden="true">facebook</span>
            </a>
            <a href="#" aria-label="Twitter" title="Twitter">
                <span class="material-icons" aria-hidden="true">twitter</span>
            </a>
            <a href="#" aria-label="Instagram" title="Instagram">
                <span class="material-icons" aria-hidden="true">instagram</span>
            </a>
        </div>
    </footer> -->
        <script>
        // Tunggu sampai halaman benar-benar dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const success = document.getElementById('success-register-message');

            // Jika elemen success ada, tampilkan dan sembunyikan setelah 3 detik
            if (success) {
                success.classList.add('popup', 'success');
                setTimeout(() => {
                    success.style.display = 'none';
                }, 1500);
            }
        });
    </script>            
</body>

</html>