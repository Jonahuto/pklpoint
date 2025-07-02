<?php
session_start();
require '../config.php';

// Check if user is logged in dan role = 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ambil ID dari URL
$id = intval($_GET['id'] ?? null);

if (!$id) {
    echo "ID $id tidak ditemukan.";
    exit();
}



// Ambil data dosen
$query_user = $conn->query("SELECT * FROM users WHERE user_id = $id AND role = 'dosen'");
$data_user = $query_user->fetch_assoc();

if (!$data_user) {
    echo "Data dosen tidak ditemukan.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $email = $conn->real_escape_string($_POST['email']);
    $no_telp = $conn->real_escape_string($_POST['no_telp']);
    $nim = $conn->real_escape_string($_POST['nim']);

    // Validasi sederhana
    if (empty($nama_lengkap) || empty($email) || empty($nim)) {
        echo "Nama, Email, dan NIP wajib diisi.";
    } else {
        $update = $conn->query("UPDATE users SET 
            nim = '$nim',
            nama_lengkap = '$nama_lengkap',
            email = '$email',
            no_telp = '$no_telp'
            WHERE user_id = $id AND role = 'dosen'");

        if ($update) {
            header("Location: keloladosen.php?success=1");
            exit();
        } else {
            header("Location: keloladosen.php?gagal=1");
        }
    }
}
$user_id = $_SESSION['user_id'];

$query = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $query->fetch_assoc();
$query = $conn->query("SELECT * FROM pkl_data WHERE user_id = $user_id");
$pkl_data = $query->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKL Point Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../css/admin/edit_mahasiswa.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <a href="dashboard.php">
                    <span class="material-icons" aria-hidden="true"><svg height="50" width="50" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                            <path d="M220 112c0 23.6-8.29 45.23-23.35 60.88C182.52 187.57 163.33 196 144 196c-15.45 0-26.78-4.18-34.89-9.31l-9.43 40.06a12 12 0 1 1-23.36-5.5l32-136a12 12 0 1 1 23.36 5.5l-16.45 69.93C118.72 164.86 127.16 172 144 172c25.56 0 52-22.45 52-60a68 68 0 1 0-126.91 34a12 12 0 0 1-20.77 12A92 92 0 1 1 220 112Z" fill="currentColor" />
                        </svg>
                    </span>
                    PKLPoint
                </a>
            </div>
            <nav>
                <a href="dashboard.php"><svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#000000" alttext="Logo Home">
                        <path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
                    </svg> Dashboard</a>
                <a href="kelolamahasiswa.php"><svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#000000" alttext="Logo Isi Data">
                        <path d="M200-440h240v-160H200v160Zm0-240h560v-80H200v80Zm0 560q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v252q-19-8-39.5-10.5t-40.5.5q-21 4-40.5 13.5T684-479l-39 39-205 204v116H200Zm0-80h240v-160H200v160Zm320-240h125l39-39q16-16 35.5-25.5T760-518v-82H520v160Zm0 360v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T863-300L643-80H520Zm300-263-37-37 37 37ZM580-140h38l121-122-37-37-122 121v38Zm141-141-19-18 37 37-18-19Z" />
                    </svg> Mengelola<br>Mahasiswa</a>
                <a href="keloladosen.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000" alttext="Logo Kegiatan PKL">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560h-80v120H280v-120h-80v560Zm280-560q17 0 28.5-11.5T520-800q0-17-11.5-28.5T480-840q-17 0-28.5 11.5T440-800q0 17 11.5 28.5T480-760Z" />
                    </svg> Mengelola<br>Dosen</a>
                <a href="logaktivitas.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000" alttext="Logo Laporan Akhir">
                        <path d="M120-120v-80l80-80v160h-80Zm160 0v-240l80-80v320h-80Zm160 0v-320l80 81v239h-80Zm160 0v-239l80-80v319h-80Zm160 0v-400l80-80v480h-80ZM120-327v-113l280-280 160 160 280-280v113L560-447 400-607 120-327Z" />
                    </svg> Log Aktivitas</a>
            </nav>
            <div class="online-profile">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
                    <path fill="#0DAE17" d="M14 19.5c0-2 1.1-3.8 2.7-4.7c-1.3-.5-2.9-.8-4.7-.8c-4.4 0-8 1.8-8 4v2h10zm5.5-3.5c-1.9 0-3.5 1.6-3.5 3.5s1.6 3.5 3.5 3.5s3.5-1.6 3.5-3.5s-1.6-3.5-3.5-3.5M16 8c0 2.2-1.8 4-4 4s-4-1.8-4-4s1.8-4 4-4s4 1.8 4 4" />
                </svg>
                <p><?php echo $user['nama_lengkap'] ?></p>
                <p><?php echo $user['nim'] ?></p>
            </div>
            <hr class="separator">
            <a href="../logout.php" class="logout"><svg class="logout-logo" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#F0080C">
                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                </svg> Logout</a>
        </aside>
        <main class="main-content">
            <header>
                <div class="dropdown">
                    <button class="dropbtn"><?php echo $user['nama_lengkap'] ?> ▼</button>
                    <div class="dropdown-content">
                        <a href="lihat_profile.php">Lihat Profile</a>
                    </div>
                </div>
            </header>
            <section class="dashboard">
                <hr class="separator">
                <h2>Edit Data Dosen</h2>

                <div id="dataDiri" class="tab-content active">
                    <form action="edit_dosen.php?id=<?= $id ?>" method="POST">
                        <input type="hidden" name="user_id" value="<?= $data_user['user_id'] ?>">

                        <label>Nama Lengkap:</label><br>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($data_user['nama_lengkap']) ?>"><br>

                        <label>NIP:</label><br>
                        <input type="text" name="nim" value="<?= htmlspecialchars($data_user['nim']) ?>"><br>
                        
                        <label>Email:</label><br>
                        <input type="email" name="email" value="<?= htmlspecialchars($data_user['email']) ?>"><br>

                        <label>No Telepon:</label><br>
                        <input type="text" name="no_telp" value="<?= htmlspecialchars($data_user['no_telp']) ?>"><br><br>

                        <div class="form-buttons">
                            <a href="kelolamahasiswa.php" class="btn-cancel">Batal</a>
                            <button type="submit" name="update_diri">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

            </section>
        </main>
    </div>
    <script>
        function showTab(tabId, event) {
            const tabs = document.querySelectorAll('.tab-content');
            const buttons = document.querySelectorAll('.tab-button');

            tabs.forEach(tab => tab.classList.remove('active'));
            buttons.forEach(btn => btn.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>

</html>